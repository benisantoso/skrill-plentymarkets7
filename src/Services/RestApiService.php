<?php

namespace Skrill\Services;

use Plenty\Plugin\Log\Loggable;

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
	 * @var skrillSettings
	 */
	private $skrillSettings;

	public function __construct(
		PaymentService $paymentService
	) {
		$this->paymentService = $paymentService;
		$this->skrillSettings = $paymentService->getSkrillSettings();
		$this->shopUrl = $this->skrillSettings['shopUrl'];
	}

	private function requestResponse($url, $headers = false, $type = 'GET', $postFields)
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
        $this->getLogger(__METHOD__)->error('Payreto:responseBody', $responseBody);
        $curlResponse = array();
        $curlErrorNo = curl_errno($ch);

        curl_close($ch);

        return $responseBody;
	}

	/**
	 * Place an order
	 * @return LocalizedOrder
	 */
	private function getAccessToken()
	{
		$getAccessTokenUrl = $this->shopUrl . 'rest/login';
		$headers = [
			'Content-Type: application/json'
		];

		$parameters = [
			'username' => 'py30002',
			'password' => '87733491'
		];

		$response = $this->requestResponse($getAccessTokenUrl, $headers, 'POST', json_encode($parameters));

		return json_decode($response);
	}

	/**
	* Update order status
	* @param int $orderId
	* @param float $statusId
	* @return boolean true | false
	*/
	public function placeOrder()
	{
		$requestAccessToken = $this->getAccessToken();
		$accessToken = $requestAccessToken['access_token'];
		$tokenType = $requestAccessToken['tokenType'];
	}

}
