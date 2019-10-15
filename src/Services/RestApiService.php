<?php

namespace Skrill\Services;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;

use Skrill\Services\Database\SkrillBasketDataService;
use Skrill\Services\PaymentService;

/**
* Class RestApi
* @package Skrill\Services
*/
class RestApiService
{
	use Loggable;

	/**
	 *
	 * @var PaymentService
	 */
	private $paymentService;

	/**
	 *
	 * @var shopUrl
	 */
	private $shopUrl;

	/**
	 *
	 * @var BasketRepositoryContract
	 */
	private $basketRepo;

	/**
	 *
	 * @var SkrillBasketDataService
	 */
	private $skrillBasketDataService;

	/**
	 *
	 * @var skrillSettings
	 */
	private $skrillSettings;

	/**
     * Constructor
     *
     * @param PaymentService $paymentService
     * @param BasketRepositoryContract $basketRepo
     * @param SkrillBasketDataService $skrillBasketDataService
     */
	public function __construct(
		PaymentService $paymentService,
		BasketRepositoryContract $basketRepo,
		SkrillBasketDataService $skrillBasketDataService
	) {
		$this->paymentService = $paymentService;
		$this->basketRepo = $basketRepo;
		$this->skrillSettings = $paymentService->getSkrillSettings();
		$this->skrillBasketDataService = $skrillBasketDataService;
		$this->shopUrl = $this->skrillSettings['shopUrl'];
	}

	/**
     * request response
     *
     * @param string $url
     * @param array $headers
     * @param string $type
     * @param array $postFields
     *
     * @return json $responseBody
     */
	private function requestResponse($url = '', $headers = false, $type = 'GET', $postFields = array())
	{
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseBody = curl_exec($ch);
        $curlErrorNo = curl_errno($ch);

        curl_close($ch);

        return $responseBody;
	}

	/**
     * get access token
     *
     * @return json
     */
	private function getAccessToken()
	{
		$getAccessTokenUrl = $this->shopUrl . 'rest/login';
		$headers = [
			'Content-Type: application/json'
		];

		$parameters = [
			'username' => $this->skrillSettings['backendUsername'],
			'password' => $this->skrillSettings['backendPassword']
		];

		$accessTokenResponse = $this->requestResponse($getAccessTokenUrl, $headers, 'POST', json_encode($parameters));
		$this->getLogger(__METHOD__)->error('Skrill:accessTokenResponse', $accessTokenResponse);

		return json_decode($accessTokenResponse);
	}

	/**
     * place Order
     *
     * @param int $basketId
     *
     * @return json
     */
	public function placeOrder($basketId)
	{
		$ordersUrl = $this->shopUrl . 'rest/orders';
		$requestAccessToken = $this->getAccessToken();
		$accessToken = $requestAccessToken->access_token;
		$tokenType = $requestAccessToken->tokenType;

		$skrillBasketData = $this->skrillBasketDataService->getSkrillBasketDataByBasketId($basketId);

		$headers = [
			'Content-Type: application/json',
			'Authorization: '. $tokenType . ' ' . $accessToken
		];

		foreach ($skrillBasketData as $key => $value) {
			if ($key == 'orderItems'
				|| $key == 'properties'
				|| $key == 'addressRelations'
				|| $key == 'relations'
			) {
				$parameters[$key] = json_decode($value);
			} else {
				$parameters[$key] = $value;
			}
		}

		unset($parameters['id'], $parameters['createdAt'], $parameters['updatedAt'], $parameters['basketId']);
		$orderData = $this->requestResponse($ordersUrl, $headers, 'POST', json_encode($parameters));
		$this->getLogger(__METHOD__)->error('Skrill:orderData', $orderData);

		return json_decode($orderData);
	}

}
