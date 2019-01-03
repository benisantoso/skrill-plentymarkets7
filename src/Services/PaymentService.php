<?php

namespace Skrill\Services;

use Plenty\Modules\Basket\Models\Basket;
use Plenty\Modules\Item\Item\Contracts\ItemRepositoryContract;
use Plenty\Modules\Basket\Models\BasketItem;
use Plenty\Modules\Account\Address\Models\Address;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Models\PaymentMethod;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Frontend\Services\SystemService;
use Plenty\Plugin\Log\Loggable;

use IO\Services\BasketService;

use Skrill\Services\OrderService;
use Skrill\Helper\PaymentHelper;
use Skrill\Services\Database\SettingsService;
use Skrill\Services\GatewayService;

/**
* Class PaymentService
* @package Skrill\Services
*/
class PaymentService
{
	use Loggable;

	/**
	 *
	 * @var ItemRepositoryContract
	 */
	private $itemRepository;

	/**
	 *
	 * @var FrontendSessionStorageFactoryContract
	 */
	private $session;

	/**
	 *
	 * @var AddressRepositoryContract
	 */
	private $addressRepository;

	/**
	 *
	 * @var CountryRepositoryContract
	 */
	private $countryRepository;

	/**
	 *
	 * @var PaymentHelper
	 */
	private $paymentHelper;

	/**
	 *
	 * @var systemService
	 */
	private $systemService;

	/**
	 *
	 * @var settingsService
	 */
	private $settingsService;

	/**
	 *
	 * @var gatewayService
	 */
	private $gatewayService;

	/**
	 *
	 * @var orderService
	 */
	private $orderService;

	/**
	 *
	 * @var orderRepository
	 */
	private $orderRepository;

	/**
	 * @var array
	 */
	public $settings = [];

	/**
	 * Constructor.
	 *
	 * @param ItemRepositoryContract $itemRepository
	 * @param FrontendSessionStorageFactoryContract $session
	 * @param AddressRepositoryContract $addressRepository
	 * @param CountryRepositoryContract $countryRepository
	 * @param PaymentHelper $paymentHelper
	 * @param SystemService $systemService
	 * @param SettingsService $settingsService
	 * @param GatewayService $gatewayService
	 * @param OrderService $orderService
	 * @param OrderRepositoryContract $orderRepository
	 */
	public function __construct(
					ItemRepositoryContract $itemRepository,
					FrontendSessionStorageFactoryContract $session,
					AddressRepositoryContract $addressRepository,
					CountryRepositoryContract $countryRepository,
					PaymentHelper $paymentHelper,
					SystemService $systemService,
					SettingsService $settingsService,
					GatewayService $gatewayService,
					OrderService $orderService,
					OrderRepositoryContract $orderRepository
	) {
		$this->itemRepository = $itemRepository;
		$this->session = $session;
		$this->addressRepository = $addressRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentHelper = $paymentHelper;
		$this->systemService = $systemService;
		$this->settingsService = $settingsService;
		$this->gatewayService = $gatewayService;
		$this->orderService = $orderService;
		$this->orderRepository = $orderRepository;
	}

	/**
	 * Load the settings from the database for the given settings type
	 *
	 * @param $settingsType
	 * @return array|null
	 */
	public function loadCurrentSettings($settingsType = 'skrill_general')
	{
		$setting = $this->settingsService->loadSetting($this->systemService->getPlentyId(), $settingsType);
		if (is_array($setting) && count($setting) > 0)
		{
			$this->settings = $setting;
		}
	}

	/**
	 * get the settings from the database for the given settings type is skrill_general
	 *
	 * @return array|null
	 */
	public function getSkrillSettings()
	{
		$this->loadCurrentSettings();
		return $this->settings;
	}

	/**
	 * Returns the payment method's content.
	 *
	 * @param Basket $basket
	 * @param PaymentMethod $paymentMethod
	 * @return array
	 */
	public function getPaymentContent(Basket $basket, PaymentMethod $paymentMethod)
	{
		$this->getLogger(__METHOD__)->error('Skrill:paymentMethod', $paymentMethod);

		$basketService = pluginApp(BasketService::class);
		$basketData = $basketService->getBasket();

		$skrillSettings = $this->getSkrillSettings();

		if (empty($skrillSettings['merchantId'])
			|| empty($skrillSettings['merchantAccount'])
			|| empty($skrillSettings['recipient'])
			|| empty($skrillSettings['logoUrl'])
			|| empty($skrillSettings['apiPassword'])
			|| empty($skrillSettings['secretWord']))
		{
			return [
				'type' => GetPaymentMethodContent::RETURN_TYPE_ERROR,
				'content' => 'The Merchant Skrill configuration is not complete. Please contact the Merchant'
			];
		}

		$orderData = $this->orderService->placeOrder();

		$this->getLogger(__METHOD__)->error('Skrill:orderData', $orderData);
		$this->getLogger(__METHOD__)->error('Skrill:basketData', $basketData);

		if (!isset($orderData->order->id))
		{
			return [
				'type' => GetPaymentMethodContent::RETURN_TYPE_ERROR,
				'content' => 'The order can not created'
			];
		}

		$orderId = $orderData->order->id;
		$transactionId = time() . $this->getRandomNumber(4) . $orderId;

		$billingAddress = $this->getAddress($this->getBillingAddress($basket));

		$parameters = [
			'pay_to_email' => $skrillSettings['merchantAccount'],
			'recipient_description' => $skrillSettings['recipient'],
			'transaction_id' => $transactionId,
			'return_url' => $this->paymentHelper->getDomain().
				'/payment/skrill/return?orderId='.$orderId,
			'status_url' => $this->paymentHelper->getDomain().
				'/payment/skrill/status?orderId='.$orderId.
				'&paymentKey='.$paymentMethod->paymentKey,
			'cancel_url' => $this->paymentHelper->getDomain().'/checkout',
			'language' => $this->getLanguage(),
			'logo_url' => $skrillSettings['logoUrl'],
			'prepare_only' => 1,
			'firstname' => $billingAddress['firstName'],
			'lastname' => $billingAddress['lastName'],
			'address' => $billingAddress['address'],
			'postal_code' => $billingAddress['postalCode'],
			'city' => $billingAddress['city'],
			'country' => $billingAddress['country'],
			'amount' => $basketData->basketAmount,
			'currency' => $basketData->currency,
			'detail1_description' => 'Order',
			'detail1_text' => $orderId,
			'detail2_description' => "Order Amount",
			'detail2_text' => $basketData->itemSum . ' ' . $basketData->currency,
			'detail3_description' => "Shipping",
			'detail3_text' => $basketData->shippingAmount . ' ' . $basketData->currency,
			'merchant_fields' => 'platform',
			'platform' => '21477252',
		];
		if ($paymentMethod->paymentKey == 'SKRILL_ACC')
		{
			$parameters['payment_methods'] = 'VSA, MSC, AMX';
		}
		elseif ($paymentMethod->paymentKey != 'SKRILL_APM')
		{
			$parameters['payment_methods'] = str_replace('SKRILL_', '', $paymentMethod->paymentKey);
		}
		if (!empty($skrillSettings['merchantEmail']))
		{
			$parameters['status_url2'] = 'mailto:' . $skrillSettings['merchantEmail'];
		}

		$this->getLogger(__METHOD__)->error('Skrill:parameters', $parameters);

		try
		{
			$sidResult = $this->gatewayService->getSidResult($parameters);
		}
		catch (\Exception $e)
		{
			$this->getLogger(__METHOD__)->error('Skrill:getSidResult', $e);
			return [
				'type' => GetPaymentMethodContent::RETURN_TYPE_ERROR,
				'content' => 'An error occurred while processing your transaction. Please contact our support.'
			];
		}

		$this->getLogger(__METHOD__)->error('Skrill:sidResult', $sidResult);

		if ($skrillSettings['display'] == 'REDIRECT')
		{
			$paymentPageUrl = $this->gatewayService->getPaymentPageUrl($sidResult);
		}
		else
		{
			$paymentPageUrl = $this->paymentHelper->getDomain().'/payment/skrill/pay/' . $sidResult . '/' . $orderId;
		}

		return [
			'type' => GetPaymentMethodContent::RETURN_TYPE_REDIRECT_URL,
			'content' => $paymentPageUrl
		];
	}

	/**
	 * Returns the language code when use at checkout.
	 *
	 * @return string
	 */
	private function getLanguage()
	{
		$language = $this->session->getLocaleSettings()->language;
		return strtoupper($language);
	}

	/**
	 * Returns a random number with length as parameter given.
	 *
	 * @param int $length
	 * @return string
	 */
	private function getRandomNumber($length)
	{
		$result = '';

		for ($i = 0; $i < $length; $i++)
		{
			$result .= rand(0, 9);
		}

		return $result;
	}

	/**
	 * this function will execute after we are doing a payment and show payment success or not.
	 *
	 * @param int $orderId
	 * @return array
	 */
	public function executePayment($orderId)
	{
		$transactionId = $this->session->getPlugin()->getValue('skrillTransactionId');

		$this->session->getPlugin()->setValue('skrillTransactionId', null);

		return $this->paymentHelper->getOrderPaymentStatus($transactionId);
	}

	/**
	 * get billing address
	 *
	 * @param Basket $basket
	 * @return Address
	 */
	private function getBillingAddress(Basket $basket)
	{
		$addressId = $basket->customerInvoiceAddressId;
		return $this->addressRepository->findAddressById($addressId);
	}

	/**
	 * get billing country code
	 *
	 * @param int $customerInvoiceAddressId
	 * @return string
	 */
	public function getBillingCountryCode($customerInvoiceAddressId)
	{
		$billingAddress = $this->addressRepository->findAddressById($customerInvoiceAddressId);
		return $this->countryRepository->findIsoCode($billingAddress->countryId, 'iso_code_3');
	}

	/**
	 * get shipping address
	 *
	 * @param Basket $basket
	 * @return Address
	 */
	private function getShippingAddress(Basket $basket)
	{
		$addressId = $basket->customerShippingAddressId;
		if ($addressId != null && $addressId != - 99)
		{
			return $this->addressRepository->findAddressById($addressId);
		}
		else
		{
			return $this->getBillingAddress($basket);
		}
	}

	/**
	 * get address by given parameter
	 *
	 * @param Address $address
	 * @return array
	 */
	private function getAddress(Address $address)
	{
		return [
			'email' => $address->email,
			'firstName' => $address->firstName,
			'lastName' => $address->lastName,
			'address' => $address->street . ' ' . $address->houseNumber,
			'postalCode' => $address->postalCode,
			'city' => $address->town,
			'country' => $this->countryRepository->findIsoCode($address->countryId, 'iso_code_3'),
			'birthday' => $address->birthday,
			'companyName' => $address->companyName,
			'phone' => $address->phone
		];
	}

	/**
	 * get basket items
	 *
	 * @param Basket $basket
	 * @return array
	 */
	private function getBasketItems(Basket $basket)
	{
		$items = [];
		/** @var BasketItem $basketItem */
		foreach ($basket->basketItems as $basketItem)
		{
			$item = $basketItem->getAttributes();
			$item['name'] = $this->getBasketItemName($basketItem);
			$items[] = $item;
		}
		return $items;
	}

	/**
	 * get basket item name
	 *
	 * @param BasketItem $basketItem
	 * @return string
	 */
	private function getBasketItemName(BasketItem $basketItem)
	{
		$this->getLogger(__METHOD__)->error('Skrill::item name', $basketItem);

		/** @var \Plenty\Modules\Item\Item\Models\Item $item */
		$item = $this->itemRepository->show($basketItem->itemId);

		/** @var \Plenty\Modules\Item\Item\Models\ItemText $itemText */
		$itemText = $item->texts;
		return $itemText->first()->name1;
	}

	/**
	 * send refund to the gateway with transaction_id and returns error or success.
	 *
	 * @param string $transactionId
	 * @param Payment $payment
	 */
	public function refund($transactionId, Payment $payment)
	{
		try
		{
			$skrillSettings = $this->getSkrillSettings();
			$parameters['email'] = $skrillSettings['merchantAccount'];
			$parameters['password'] = md5($skrillSettings['apiPassword']);
			$parameters['transaction_id'] = $transactionId;
			$parameters['amount'] = $payment->amount;
			$parameters['refund_status_url'] = $this->paymentHelper->getDomain() . '/payment/skrill/refundstatus';

			$parametersLog = $parameters;
			$parametersLog['password'] = '*****';

			$this->getLogger(__METHOD__)->error('Skrill:parametersLog', $parametersLog);

			$response = $this->gatewayService->doRefund($parameters);

		}
		catch (\Exception $e)
		{
			$this->getLogger(__METHOD__)->error('Skrill:refundFailed', $e);

			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}

		return [
			'success' => true,
			'response' => $response
		];
	}

	/**
	 * send get currenty payment status to the gateway with transaction_id and returns error or success.
	 *
	 * @param string $transactionId
	 * @param Order $order
	 */
	public function updateOrderStatus($transactionId, Order $order)
	{
		try {
			$skrillSettings = $this->getSkrillSettings();
			$parameters['email'] = $skrillSettings['merchantAccount'];
			$parameters['password'] = md5($skrillSettings['apiPassword']);
			$parameters['trn_id'] = $transactionId;

			$parametersLog = $parameters;
			$parametersLog['password'] = '*****';

			$this->getLogger(__METHOD__)->error('Skrill:parametersLog', $parametersLog);

			$response = $this->gatewayService->getPaymentStatus($parameters);

			$this->getLogger(__METHOD__)->error('Skrill:response', $response);
		}
		catch (\Exception $e)
		{
			$this->getLogger(__METHOD__)->error('Skrill:updateOrderStatusFailed', $e->getMessage());

			$this->orderRepository->updateOrder(['statusId' => 3], $order->id);

			return [
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}

		if ($response['status'] != '2')
		{
			$this->orderRepository->updateOrder(['statusId' => 3], $order->id);
		}

		return [
			'success' => true,
			'response' => $response
		];
	}
}
