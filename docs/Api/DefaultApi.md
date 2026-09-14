# MaytesApiClient\DefaultApi



All URIs are relative to http://localhost, except if the operation defines another base path.

| Method | HTTP request | Description |
| ------------- | ------------- | ------------- |
| [**cancelCheckout()**](DefaultApi.md#cancelCheckout) | **POST** /api/merchant/v1/checkouts/{checkoutUuid}/cancel | Cancel authorized checkout |
| [**captureCheckout()**](DefaultApi.md#captureCheckout) | **POST** /api/merchant/v1/checkouts/{checkoutUuid}/capture | Capture authorized checkout |
| [**createCheckout()**](DefaultApi.md#createCheckout) | **POST** /api/merchant/v1/checkouts | Create a new checkout |
| [**getCheckout()**](DefaultApi.md#getCheckout) | **GET** /api/merchant/v1/checkouts/{checkoutUuid} | Get checkout details |
| [**getHealth()**](DefaultApi.md#getHealth) | **GET** /api/health | Health check |
| [**getOAuthToken()**](DefaultApi.md#getOAuthToken) | **POST** /oauth/token | Issue an access token |
| [**getSettlement()**](DefaultApi.md#getSettlement) | **GET** /api/merchant/v1/settlements/{uuid} | Get a settlement by UUID |
| [**listSettlements()**](DefaultApi.md#listSettlements) | **GET** /api/merchant/v1/settlements | List settlements for this merchant |
| [**refundCheckout()**](DefaultApi.md#refundCheckout) | **POST** /api/merchant/v1/checkouts/{checkoutUuid}/refund | Refund a captured checkout |


## `cancelCheckout()`

```php
cancelCheckout($checkout_uuid, $cancel_checkout_request): \MaytesApiClient\Model\CancelCheckoutResponse
```

Cancel authorized checkout

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$checkout_uuid = 'checkout_uuid_example'; // string
$cancel_checkout_request = new \MaytesApiClient\Model\CancelCheckoutRequest(); // \MaytesApiClient\Model\CancelCheckoutRequest

try {
    $result = $apiInstance->cancelCheckout($checkout_uuid, $cancel_checkout_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->cancelCheckout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **checkout_uuid** | **string**|  | |
| **cancel_checkout_request** | [**\MaytesApiClient\Model\CancelCheckoutRequest**](../Model/CancelCheckoutRequest.md)|  | |

### Return type

[**\MaytesApiClient\Model\CancelCheckoutResponse**](../Model/CancelCheckoutResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `captureCheckout()`

```php
captureCheckout($checkout_uuid, $capture_checkout_request): \MaytesApiClient\Model\CaptureCheckoutResponse
```

Capture authorized checkout

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$checkout_uuid = 'checkout_uuid_example'; // string
$capture_checkout_request = new \MaytesApiClient\Model\CaptureCheckoutRequest(); // \MaytesApiClient\Model\CaptureCheckoutRequest

try {
    $result = $apiInstance->captureCheckout($checkout_uuid, $capture_checkout_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->captureCheckout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **checkout_uuid** | **string**|  | |
| **capture_checkout_request** | [**\MaytesApiClient\Model\CaptureCheckoutRequest**](../Model/CaptureCheckoutRequest.md)|  | |

### Return type

[**\MaytesApiClient\Model\CaptureCheckoutResponse**](../Model/CaptureCheckoutResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `createCheckout()`

```php
createCheckout($create_checkout_request): \MaytesApiClient\Model\CreateCheckoutResponse
```

Create a new checkout

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$create_checkout_request = new \MaytesApiClient\Model\CreateCheckoutRequest(); // \MaytesApiClient\Model\CreateCheckoutRequest

try {
    $result = $apiInstance->createCheckout($create_checkout_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->createCheckout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **create_checkout_request** | [**\MaytesApiClient\Model\CreateCheckoutRequest**](../Model/CreateCheckoutRequest.md)|  | |

### Return type

[**\MaytesApiClient\Model\CreateCheckoutResponse**](../Model/CreateCheckoutResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getCheckout()`

```php
getCheckout($checkout_uuid, $merchant_order_id): \MaytesApiClient\Model\GetCheckoutResponse
```

Get checkout details

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$checkout_uuid = 'checkout_uuid_example'; // string
$merchant_order_id = 'merchant_order_id_example'; // string

try {
    $result = $apiInstance->getCheckout($checkout_uuid, $merchant_order_id);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->getCheckout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **checkout_uuid** | **string**|  | |
| **merchant_order_id** | **string**|  | [optional] |

### Return type

[**\MaytesApiClient\Model\GetCheckoutResponse**](../Model/GetCheckoutResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getHealth()`

```php
getHealth(): \MaytesApiClient\Model\HealthResponse
```

Health check

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);

try {
    $result = $apiInstance->getHealth();
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->getHealth: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

This endpoint does not need any parameter.

### Return type

[**\MaytesApiClient\Model\HealthResponse**](../Model/HealthResponse.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getOAuthToken()`

```php
getOAuthToken($o_auth_token_request): \MaytesApiClient\Model\OAuthTokenResponse
```

Issue an access token

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client()
);
$o_auth_token_request = new \MaytesApiClient\Model\OAuthTokenRequest(); // \MaytesApiClient\Model\OAuthTokenRequest

try {
    $result = $apiInstance->getOAuthToken($o_auth_token_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->getOAuthToken: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **o_auth_token_request** | [**\MaytesApiClient\Model\OAuthTokenRequest**](../Model/OAuthTokenRequest.md)|  | |

### Return type

[**\MaytesApiClient\Model\OAuthTokenResponse**](../Model/OAuthTokenResponse.md)

### Authorization

No authorization required

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `getSettlement()`

```php
getSettlement($uuid): \MaytesApiClient\Model\GetSettlementResponse
```

Get a settlement by UUID

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$uuid = 'uuid_example'; // string | Settlement UUID

try {
    $result = $apiInstance->getSettlement($uuid);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->getSettlement: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **uuid** | **string**| Settlement UUID | |

### Return type

[**\MaytesApiClient\Model\GetSettlementResponse**](../Model/GetSettlementResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `listSettlements()`

```php
listSettlements($offset, $limit, $sort_dir, $sort_by, $created_before, $created_after, $status): \MaytesApiClient\Model\ListSettlementsResponse
```

List settlements for this merchant

Returns settlements for the authenticated merchant. Default sort: created_at ASC.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$offset = NULL; // mixed | Pagination offset (default 0)
$limit = NULL; // mixed | Max records to return (default 50, max 200)
$sort_dir = 'sort_dir_example'; // string | Sort direction (default: asc)
$sort_by = 'sort_by_example'; // string | Field to sort by (default: created_at)
$created_before = NULL; // mixed | Include settlements created at or before this ISO-8601 timestamp
$created_after = NULL; // mixed | Include settlements created at or after this ISO-8601 timestamp
$status = 'status_example'; // string | Filter by status

try {
    $result = $apiInstance->listSettlements($offset, $limit, $sort_dir, $sort_by, $created_before, $created_after, $status);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->listSettlements: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **offset** | [**mixed**](../Model/.md)| Pagination offset (default 0) | [optional] |
| **limit** | [**mixed**](../Model/.md)| Max records to return (default 50, max 200) | [optional] |
| **sort_dir** | **string**| Sort direction (default: asc) | [optional] |
| **sort_by** | **string**| Field to sort by (default: created_at) | [optional] |
| **created_before** | [**mixed**](../Model/.md)| Include settlements created at or before this ISO-8601 timestamp | [optional] |
| **created_after** | [**mixed**](../Model/.md)| Include settlements created at or after this ISO-8601 timestamp | [optional] |
| **status** | **string**| Filter by status | [optional] |

### Return type

[**\MaytesApiClient\Model\ListSettlementsResponse**](../Model/ListSettlementsResponse.md)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: Not defined
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)

## `refundCheckout()`

```php
refundCheckout($checkout_uuid, $create_refund_request)
```

Refund a captured checkout

Initiates a full refund of the captured payment. Returns 204 immediately; Stripe confirms asynchronously via webhook. Poll GET /checkouts/:uuid for status and refund details.

### Example

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');


// Configure Bearer (JWT) authorization: bearer
$config = MaytesApiClient\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');


$apiInstance = new MaytesApiClient\Api\DefaultApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$checkout_uuid = 'checkout_uuid_example'; // string
$create_refund_request = new \MaytesApiClient\Model\CreateRefundRequest(); // \MaytesApiClient\Model\CreateRefundRequest

try {
    $apiInstance->refundCheckout($checkout_uuid, $create_refund_request);
} catch (Exception $e) {
    echo 'Exception when calling DefaultApi->refundCheckout: ', $e->getMessage(), PHP_EOL;
}
```

### Parameters

| Name | Type | Description  | Notes |
| ------------- | ------------- | ------------- | ------------- |
| **checkout_uuid** | **string**|  | |
| **create_refund_request** | [**\MaytesApiClient\Model\CreateRefundRequest**](../Model/CreateRefundRequest.md)|  | |

### Return type

void (empty response body)

### Authorization

[bearer](../../README.md#bearer)

### HTTP request headers

- **Content-Type**: `application/json`
- **Accept**: `application/json`

[[Back to top]](#) [[Back to API list]](../../README.md#endpoints)
[[Back to Model list]](../../README.md#models)
[[Back to README]](../../README.md)
