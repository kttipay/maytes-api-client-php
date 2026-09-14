<?php
/**
 * Proves the installed package is usable on this PHP version: PSR-4 autoloading
 * resolves both the generated classes and the hand-written extras, and the
 * webhook verifier round-trips a signature it did not produce itself.
 *
 * No network — the API surface is exercised by the upstream e2e suite.
 *
 * This file is generated — edit it at sdks/backend-sdk/mirror/php/ in the
 * api-ts-v3 monorepo, not here.
 */

require __DIR__ . '/../vendor/autoload.php';

$failures = 0;

function check(string $label, bool $ok): void
{
    global $failures;
    printf("  %-56s %s\n", $label, $ok ? 'ok' : 'FAILED');
    if (!$ok) {
        $failures++;
    }
}

printf("PHP %s\n", PHP_VERSION);

foreach ([
    'MaytesApiClient\ClientFactory',
    'MaytesApiClient\WebhookVerifier',
    'MaytesApiClient\Configuration',
    'MaytesApiClient\Api\DefaultApi',
    'MaytesApiClient\Model\CreateCheckoutRequest',
    // The discriminated-union parent AND the variant a caller actually
    // constructs. `LineItem` alone passing proves little: the parent exists in
    // any generation, while a missing variant is how the union breaks.
    'MaytesApiClient\Model\LineItem',
    'MaytesApiClient\Model\OtherLineItem',
] as $class) {
    check($class, class_exists($class));
}

// Round-trip a signature through the verifier. Signed here rather than hardcoded
// so the test states the algorithm it expects instead of trusting a fixture.
$secret = 'whsec_smoke';
$body = json_encode([
    'id' => 'evt_smoke',
    'type' => 'checkout.authorized',
    'apiVersion' => '2026-06',
    'createdAt' => '2026-01-01T00:00:00Z',
    'data' => [],
]);
$timestamp = (string) time();
$header = 't=' . $timestamp . ',v1=' . hash_hmac('sha256', $timestamp . '.' . $body, $secret);

try {
    $event = MaytesApiClient\WebhookVerifier::verify($body, $header, $secret);
    check('WebhookVerifier::verify accepts a valid signature', $event->id === 'evt_smoke');
} catch (Throwable $e) {
    check('WebhookVerifier::verify accepts a valid signature (' . $e->getMessage() . ')', false);
}

// A tampered body must be rejected — a verifier that accepts everything would
// pass every check above.
try {
    MaytesApiClient\WebhookVerifier::verify($body . ' ', $header, $secret);
    check('WebhookVerifier::verify rejects a tampered body', false);
} catch (MaytesApiClient\WebhookSignatureError $e) {
    check('WebhookVerifier::verify rejects a tampered body', true);
}

echo $failures === 0 ? "\nAll checks passed.\n" : "\n$failures check(s) failed.\n";
exit($failures === 0 ? 0 : 1);
