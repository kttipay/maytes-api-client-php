<?php
/**
 * Bundled with the generated SDK via bin/postprocess-php.sh.
 *
 * Lives at build/php-client/lib/WebhookVerifier.php after `make generate-php`.
 * PSR-4 autoload (defined in the generated composer.json) discovers it
 * automatically — no further wiring needed.
 *
 * Verifies the `X-Maytes-Signature` header on an inbound webhook delivery and
 * returns the parsed event. Merchants must never hand-roll this — see the four
 * sharp edges below, each of which silently produces either a security hole or
 * an outage:
 *
 *   1. RAW BODY. The signature covers the exact bytes we sent. Verifying
 *      against re-serialised JSON (json_encode of a decoded array) fails for
 *      any body whose key order or number formatting differs after a
 *      decode/encode round-trip. This is the single most common webhook
 *      integration bug.
 *   2. MULTIPLE `v1=` ENTRIES. During a signing-secret rotation grace window
 *      the header carries TWO signatures (current secret first, previous
 *      second) and the delivery is valid if EITHER matches. A verifier that
 *      reads only the first `v1=` works perfectly until the first rotation,
 *      then rejects live traffic. See §7 of the webhook spec.
 *   3. CONSTANT-TIME COMPARISON. A plain `===` on digests leaks how much of a
 *      forged signature was correct, which is enough to recover a valid
 *      signature one byte at a time.
 *   4. REPLAY WINDOW. The `t=` timestamp is the anti-replay anchor; a captured
 *      delivery stays valid forever without a freshness check.
 *
 * Verification and JSON decoding are deliberately fused into ONE call: the
 * method takes the raw body and hands back the parsed event, so there is no
 * intermediate state in which a caller can decode first and verify second.
 *
 * Algorithm (webhook spec §6.1):
 *   signed_payload = "{t}.{raw_body}"
 *   signature      = lowercase_hex( HMAC_SHA256(signing_secret, signed_payload) )
 */

namespace MaytesApiClient;

/**
 * Thrown when an inbound delivery is not authentic. Respond 400 and do NOT
 * act on the body.
 *
 * `getReason()` returns one of:
 *   'malformed_header'           — header absent or missing `t=` / `v1=`
 *   'timestamp_out_of_tolerance' — `t=` too far from now; possible replay
 *   'no_matching_signature'      — wrong secret, or the body was modified
 *   'invalid_json'               — signature valid but body wasn't JSON
 */
class WebhookSignatureError extends \Exception
{
    private string $reason;

    public function __construct(string $reason, string $message)
    {
        parent::__construct($message);
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}

/**
 * A verified webhook event (webhook spec §6).
 *
 * `data` is left as an open array because its shape varies per `type` —
 * branch on `type`, then read the fields that event documents. Ignore
 * unrecognised fields: additive changes do NOT bump `api_version`.
 */
class MaytesWebhookEvent
{
    /** Stable event id — identical across every retry. Deduplicate on this. */
    public string $id;
    /** e.g. 'checkout.authorized', 'checkout.voided', 'webhook.test'. */
    public string $type;
    /** Envelope schema version, e.g. '2026-06'. Bumped only on breaking changes. */
    public string $apiVersion;
    /** When the event occurred. */
    public string $createdAt;
    /** @var array<string, mixed> Per-event payload. */
    public array $data;

    /** @param array<string, mixed> $data */
    public function __construct(
        string $id,
        string $type,
        string $apiVersion,
        string $createdAt,
        array $data
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->apiVersion = $apiVersion;
        $this->createdAt = $createdAt;
        $this->data = $data;
    }
}

class WebhookVerifier
{
    /** Default anti-replay window, in seconds (webhook spec §6.1 advises ~5 min). */
    public const DEFAULT_TOLERANCE_SECONDS = 300;

    /**
     * Verify an inbound Maytes webhook delivery and return the parsed event.
     *
     * Pass the **raw** request body — the exact bytes, before any JSON decode.
     *
     *   use MaytesApiClient\WebhookVerifier;
     *   use MaytesApiClient\WebhookSignatureError;
     *
     *   try {
     *       $event = WebhookVerifier::verify(
     *           file_get_contents('php://input'),          // raw body
     *           $_SERVER['HTTP_X_MAYTES_SIGNATURE'] ?? null,
     *           getenv('MAYTES_WEBHOOK_SECRET'),
     *       );
     *   } catch (WebhookSignatureError $e) {
     *       http_response_code(400);
     *       return;
     *   }
     *   // ... handle $event->type
     *
     * `$toleranceSeconds = 0` skips the freshness check entirely — TEST ONLY,
     * for replaying a stored delivery in a fixture. Never 0 in production: it
     * makes any captured delivery replayable forever.
     *
     * @throws WebhookSignatureError if the delivery is not authentic.
     */
    public static function verify(
        string $rawBody,
        ?string $signatureHeader,
        string $signingSecret,
        int $toleranceSeconds = self::DEFAULT_TOLERANCE_SECONDS
    ): MaytesWebhookEvent {
        [$timestamp, $signatures] = self::parseSignatureHeader($signatureHeader);

        if ($toleranceSeconds > 0) {
            if (!ctype_digit($timestamp)) {
                throw new WebhookSignatureError(
                    'malformed_header',
                    'X-Maytes-Signature carries a non-numeric t= value.'
                );
            }
            if (abs(time() - (int) $timestamp) > $toleranceSeconds) {
                throw new WebhookSignatureError(
                    'timestamp_out_of_tolerance',
                    "Webhook timestamp is outside the {$toleranceSeconds}s tolerance — possible replay."
                );
            }
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $signingSecret);

        // ANY match wins — during a rotation grace window the current secret's
        // signature and the previous secret's signature are both present.
        foreach ($signatures as $candidate) {
            if (hash_equals($expected, $candidate)) {
                return self::parseEnvelope($rawBody);
            }
        }

        throw new WebhookSignatureError(
            'no_matching_signature',
            'No v1 signature matched the computed HMAC — wrong signing secret, '
                . 'or the body was modified in transit.'
        );
    }

    /**
     * @return array{0: string, 1: list<string>}
     * @throws WebhookSignatureError
     */
    private static function parseSignatureHeader(?string $header): array
    {
        if ($header === null || $header === '') {
            throw new WebhookSignatureError(
                'malformed_header',
                'Missing X-Maytes-Signature header.'
            );
        }

        $timestamp = null;
        $signatures = [];

        foreach (explode(',', $header) as $part) {
            $trimmed = trim($part);
            if (str_starts_with($trimmed, 't=')) {
                $timestamp = substr($trimmed, 2);
            } elseif (str_starts_with($trimmed, 'v1=')) {
                $signatures[] = substr($trimmed, 3);
            }
            // Unknown schemes (a future v2=) are ignored, not fatal — forward
            // compatibility: we may add a scheme alongside v1 before removing v1.
        }

        if ($timestamp === null || $timestamp === '' || $signatures === []) {
            throw new WebhookSignatureError(
                'malformed_header',
                'X-Maytes-Signature is malformed — expected `t=<unix>,v1=<hex>`.'
            );
        }

        return [$timestamp, $signatures];
    }

    /** @throws WebhookSignatureError */
    private static function parseEnvelope(string $rawBody): MaytesWebhookEvent
    {
        $parsed = json_decode($rawBody, true);
        if (!is_array($parsed)) {
            throw new WebhookSignatureError(
                'invalid_json',
                'Signature verified but the body is not valid JSON.'
            );
        }

        return new MaytesWebhookEvent(
            (string) ($parsed['id'] ?? ''),
            (string) ($parsed['type'] ?? ''),
            (string) ($parsed['api_version'] ?? ''),
            (string) ($parsed['created_at'] ?? ''),
            is_array($parsed['data'] ?? null) ? $parsed['data'] : []
        );
    }
}
