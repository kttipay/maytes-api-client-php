# CreateCheckoutRequest

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**merchant_order_id** | **string** |  | [optional]
**merchant_expires_at** | **\DateTime** |  | [optional]
**total_amount** | **int** |  |
**currency** | **string** |  |
**return_url** | **string** |  |
**cancel_url** | **string** |  |
**items** | [**\MaytesApiClient\Model\LineItem[]**](LineItem.md) |  |
**fees** | [**\MaytesApiClient\Model\CheckoutFee[]**](CheckoutFee.md) |  | [optional]
**discounts** | [**\MaytesApiClient\Model\CheckoutDiscount[]**](CheckoutDiscount.md) |  | [optional]
**tax** | [**\MaytesApiClient\Model\Tax**](Tax.md) |  | [optional]
**shipping_amount** | **int** |  | [optional]
**customer_data** | [**\MaytesApiClient\Model\CustomerData**](CustomerData.md) |  | [optional]
**metadata** | **array<string,mixed>** |  | [optional]
**allocation_model** | **string** |  | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
