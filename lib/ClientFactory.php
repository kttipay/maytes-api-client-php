<?php
/**
 * Bundled with the generated SDK via bin/postprocess-php.sh.
 *
 * Lives at build/php-client/lib/ClientFactory.php after `make generate-php`.
 * PSR-4 autoload (defined in the generated composer.json) discovers it
 * automatically — no further wiring needed.
 *
 * Adds OAuth2 client_credentials credential management on top of the raw
 * DefaultApi using a **reactive 401-retry** pattern:
 *
 *   1. First authed call mints a token via GetOAuthToken (a @auth([]) op) and
 *      caches it.
 *   2. Subsequent calls send the cached token as-is — no client-side expiry
 *      math, no proactive refresh.
 *   3. If the server returns 401, a Guzzle middleware refreshes the token and
 *      retries the request once with the new Authorization header. The retry
 *      bypasses the middleware (via an `_oauth_retry` flag on options) so a
 *      persistent 401 propagates rather than looping.
 *
 * PHP is single-threaded — no dedup lock needed — but `refreshIfStale()`
 * keeps the API surface consistent with TS / Python / Go / Java wrappers.
 */

namespace MaytesApiClient;

class OAuthTokenProvider
{
    private string $clientId;
    private string $clientSecret;
    private ?string $token;

    private Api\DefaultApi $unauthedApi;

    public function __construct(string $endpoint, string $clientId, string $clientSecret, ?string $initialToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->token = $initialToken;

        $config = Configuration::getDefaultConfiguration()->setHost($endpoint);
        $this->unauthedApi = new Api\DefaultApi(new \GuzzleHttp\Client(), $config);
    }

    public function get(): string
    {
        if ($this->token !== null) {
            return $this->token;
        }
        return $this->mint();
    }

    /**
     * Refresh only if the cache still holds the stale token observed by the
     * caller. PHP doesn't have real concurrency here, but the pattern keeps
     * this wrapper's behavior aligned with the other SDKs.
     */
    public function refreshIfStale(?string $staleToken): string
    {
        if ($this->token !== null && $this->token !== $staleToken) {
            return $this->token;
        }
        return $this->mint();
    }

    private function mint(): string
    {
        $body = new Model\OAuthTokenRequest([
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);
        $response = $this->unauthedApi->getOAuthToken($body);

        $this->token = $response->getAccessToken();
        return $this->token;
    }
}

/**
 * Configuration whose getAccessToken() delegates to the provider every call.
 * Internal — merchants should use ClientFactory::create.
 */
class ManagedConfiguration extends Configuration
{
    private OAuthTokenProvider $provider;

    public function __construct(OAuthTokenProvider $provider, string $host)
    {
        parent::__construct();
        $this->setHost($host);
        $this->provider = $provider;
    }

    public function getAccessToken(): string
    {
        return $this->provider->get();
    }
}

/**
 * Factory for fully managed Maytes API clients.
 *
 *   $api = \MaytesApiClient\ClientFactory::create(
 *       'https://api.maytes.co',
 *       $clientId,
 *       $clientSecret,
 *   );
 *   $result = $api->createCheckout($body);
 *
 * `$initialToken` is an SDK-test-only escape hatch — seeds the cache with a
 * pre-minted JWT. Used by the E2E test path that forges a short-lived token
 * via the shared RSA private key and verifies the wrapper's reactive 401-retry
 * refresh fires when the seeded token expires. Production callers omit it.
 */
class ClientFactory
{
    public static function create(
        string $endpoint,
        string $clientId,
        string $clientSecret,
        ?string $initialToken = null
    ): Api\DefaultApi {
        $provider = new OAuthTokenProvider($endpoint, $clientId, $clientSecret, $initialToken);
        $config = new ManagedConfiguration($provider, $endpoint);

        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->push(self::retry401Middleware($provider));
        $httpClient = new \GuzzleHttp\Client(['handler' => $stack]);

        return new Api\DefaultApi($httpClient, $config);
    }

    /**
     * Guzzle middleware that intercepts 401 responses, refreshes the token,
     * and retries the request once. The retry sets `_oauth_retry` on the
     * options array so the middleware skips itself on the second pass —
     * preventing infinite loops on persistent 401.
     */
    private static function retry401Middleware(OAuthTokenProvider $provider): callable
    {
        return function (callable $handler) use ($provider) {
            return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler, $provider) {
                if (!empty($options['_oauth_retry'])) {
                    return $handler($request, $options);
                }

                $authHeader = $request->getHeaderLine('Authorization');
                $staleToken = str_starts_with($authHeader, 'Bearer ')
                    ? substr($authHeader, 7)
                    : null;

                $retry = function () use ($handler, $request, $options, $provider, $staleToken) {
                    $newToken = $provider->refreshIfStale($staleToken);
                    $retried = $request->withHeader('Authorization', 'Bearer ' . $newToken);
                    $options['_oauth_retry'] = true;
                    return $handler($retried, $options);
                };

                return $handler($request, $options)->then(
                    function ($response) use ($retry) {
                        return $response->getStatusCode() === 401 ? $retry() : $response;
                    },
                    function ($reason) use ($retry) {
                        if (!($reason instanceof \GuzzleHttp\Exception\RequestException)) {
                            throw $reason;
                        }
                        $response = $reason->getResponse();
                        if ($response === null || $response->getStatusCode() !== 401) {
                            throw $reason;
                        }
                        return $retry();
                    }
                );
            };
        };
    }
}
