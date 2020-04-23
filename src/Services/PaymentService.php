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
use Plenty\Plugin\Templates\Twig;
use Plenty\Plugin\Application;

use IO\Services\BasketService;

use Skrill\Services\OrderService;
use Skrill\Helper\PaymentHelper;
use Skrill\Services\Database\SettingsService;
use Skrill\Services\GatewayService;
use Skrill\Constants\SessionKeys;
use Skrill\Models\Repositories\SkrillOrderTransactionRepository;
use Skrill\Services\Database\SkrillBasketDataService;
use Skrill\Configs\MethodConfigContract;

use Skrill\Methods\PchPaymentMethod;
use Skrill\Methods\AccPaymentMethod;
use Skrill\Methods\AciPaymentMethod;
use Skrill\Methods\AdbPaymentMethod;
use Skrill\Methods\AliPaymentMethod;
use Skrill\Methods\AobPaymentMethod;
use Skrill\Methods\ApmPaymentMethod;
use Skrill\Methods\AupPaymentMethod;
use Skrill\Methods\BtcPaymentMethod;
use Skrill\Methods\CsiPaymentMethod;
use Skrill\Methods\DidPaymentMethod;
use Skrill\Methods\DnkPaymentMethod;
use Skrill\Methods\EbtPaymentMethod;
use Skrill\Methods\EpyPaymentMethod;
use Skrill\Methods\GcbPaymentMethod;
use Skrill\Methods\GirPaymentMethod;
use Skrill\Methods\IdlPaymentMethod;
use Skrill\Methods\MaePaymentMethod;
use Skrill\Methods\MscPaymentMethod;
use Skrill\Methods\NpyPaymentMethod;
use Skrill\Methods\NtlPaymentMethod;
use Skrill\Methods\ObtPaymentMethod;
use Skrill\Methods\PliPaymentMethod;
use Skrill\Methods\PscPaymentMethod;
use Skrill\Methods\PspPaymentMethod;
use Skrill\Methods\PwyPaymentMethod;
use Skrill\Methods\SftPaymentMethod;
use Skrill\Methods\VsaPaymentMethod;
use Skrill\Methods\WltPaymentMethod;

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
	private $sessionStorageFactory;

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
	 *
	 * @var BasketServiceContract
	 */
	private $basketServiceContract;

	/**
	 *
	 * @var Twig
	 */
	private $twig;

	/**
	 *
	 * @var SkrillOrderTransactionRepository
	 */
	private $skrillOrderTransRepo;

	/**
	 *
	 * @var MethodConfigContract
	 */
	private $methodConfigContract;

	/**
	 * @var array
	 */
	public $settings = [];

	/**
	 * @var SkrillBasketDataService
	 */
	public $skrillBasketDataService;

	/**
	 * Constructor.
	 *
	 * @param ItemRepositoryContract $itemRepository
	 * @param FrontendSessionStorageFactoryContract $sessionStorageFactory
	 * @param AddressRepositoryContract $addressRepository
	 * @param CountryRepositoryContract $countryRepository
	 * @param PaymentHelper $paymentHelper
	 * @param SystemService $systemService
	 * @param SettingsService $settingsService
	 * @param GatewayService $gatewayService
	 * @param OrderService $orderService
	 * @param OrderRepositoryContract $orderRepository
	 * @param BasketServiceContract $basketServiceContract
	 * @param Twig $twig
	 * @param SkrillOrderTransactionRepository $skrillOrderTransRepo
	 * @param MethodConfigContract $methodConfigContract
	 * @param SkrillBasketDataService $skrillBasketDataService
	 */
	public function __construct(
					ItemRepositoryContract $itemRepository,
					FrontendSessionStorageFactoryContract $sessionStorageFactory,
					AddressRepositoryContract $addressRepository,
					CountryRepositoryContract $countryRepository,
					PaymentHelper $paymentHelper,
					SystemService $systemService,
					SettingsService $settingsService,
					GatewayService $gatewayService,
					OrderService $orderService,
					OrderRepositoryContract $orderRepository,
					BasketServiceContract $basketServiceContract,
					Twig $twig,
					SkrillOrderTransactionRepository $skrillOrderTransRepo,
					MethodConfigContract $methodConfigContract,
					SkrillBasketDataService $skrillBasketDataService
	) {
		$this->itemRepository = $itemRepository;
		$this->sessionStorageFactory = $sessionStorageFactory;
		$this->addressRepository = $addressRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentHelper = $paymentHelper;
		$this->systemService = $systemService;
		$this->settingsService = $settingsService;
		$this->gatewayService = $gatewayService;
		$this->orderService = $orderService;
		$this->orderRepository = $orderRepository;
		$this->basketServiceContract = $basketServiceContract;
		$this->twig = $twig;
		$this->skrillOrderTransRepo = $skrillOrderTransRepo;
		$this->methodConfigContract = $methodConfigContract;
		$this->skrillBasketDataService = $skrillBasketDataService;
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
	 * Load the card settings from the database for the given settings type
	 *
	 * @param $settingsType
	 * @return array|null
	 */
	public function loadMethodSettings($settingsType)
	{
		$setting = $this->settingsService->loadSetting($this->systemService->getPlentyId(), $settingsType);
		if (is_array($setting) && count($setting) > 0)
		{
			return $setting;
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
	 * @param string $paymentMethod
	 * @param int $mopId
	 * @return array
	 */
	public function getPaymentContent(string $paymentMethod, int $mopId)
	{
		$this->getLogger(__METHOD__)->error('Skrill:Start payment widget Content :', null);
		$this->settingsService->getClients();

		$methodInstance = $this->paymentHelper->getPaymentMethodInstance($paymentMethod);
		$app = pluginApp(Application::class);
		$this->getLogger(__METHOD__)->error('Skrill:methodInstance', $methodInstance);

		$type = $methodInstance->getReturnType();
		$value = '';
		$sidResult = null;
		$basket     = $this->basketServiceContract->getBasket();
		$this->getLogger(__METHOD__)->error('Skrill:Bakset :', $basket);

		$skrillSettings = $this->getSkrillSettings();

		if (empty($skrillSettings['merchantId'])
			|| empty($skrillSettings['merchantAccount'])
			|| empty($skrillSettings['recipient'])
			|| empty($skrillSettings['logoUrl'])
			|| empty($skrillSettings['apiPassword'])
			|| empty($skrillSettings['secretWord'])
		) {
			$this->getLogger(__METHOD__)->error('Skrill:The Merchant Skrill configuration is not complete. Please contact the Merchant :', '');
			return [
				'type' => GetPaymentMethodContent::RETURN_TYPE_ERROR,
				'value' => 'The Merchant Skrill configuration is not complete. Please contact the Merchant'
			];
		}

		$this->skrillBasketDataService->createOrUpdateBasketData($basket->id, $this->getBasketData($basket, $this->systemService->getPlentyId()));
		$skrillBasketData = $this->skrillBasketDataService->getSkrillBasketDataByBasketId($basket->id);
		$this->getLogger(__METHOD__)->error('Skrill:skrillBasketData', $skrillBasketData);

		try
		{
			$sidResult = $this->sendPaymentRequest($basket, $paymentMethod, $mopId, $skrillSettings);
		}
		catch (\Exception $e)
		{
			$this->getLogger(__METHOD__)->error('Skrill:getSidResult', $e);
			return [
				'type' => GetPaymentMethodContent::RETURN_TYPE_ERROR,
				'value' => 'An error occurred while processing your transaction. Please contact our support.'
			];
		}

		$this->getLogger(__METHOD__)->error('Skrill:sidResult', $sidResult);

		if ($skrillSettings['display'] == 'REDIRECT')
		{
			$value = $this->gatewayService->getPaymentPageUrl($sidResult);
			$type = GetPaymentMethodContent::RETURN_TYPE_REDIRECT_URL;
		}
		else
		{
			$loader = $app->getUrlPath('skrill').'/images/gif/load.gif';
			$paymentPageUrl = $this->gatewayService->getPaymentPageUrl($sidResult);
			$parameters = [
                'sid' => $paymentPageUrl,
                'loader' => $loader
            ];
            $value      = $this->renderPaymentForm('Skrill::Payment.PaymentWidget', $parameters);
		}

		return [
			'type' => $type,
			'value' => $value
		];
	}

	/**
     * Renders the given template injecting the parameters
     *
     * @param string $template
     * @param array $parameters
     * @return string
     */
    protected function renderPaymentForm(string $template, array $parameters = []): string
    {
        return $this->twig->render($template, $parameters);
    }


    /**
     * getBasketData
     *
     * @param array $basket
     * @param int $plentyId
     * @return array $basketsData
     */
    protected function getBasketData(Basket $basket, int $plentyId)
    {
    	$basketsData = [
    		'typeId' 			=> 1,
    		'ownerId' 			=> 0,
    		'plentyId' 			=> $plentyId,
    		'locationId' 		=> 1,
    		'statusId' 			=> 3,
    		'orderItems' 		=> $this->getOrderItems($basket),
    		'properties' 		=> [
    			[
    				'typeId' 	=> 1,
    				'value' 	=> "1"
    			],
    			[
    				'typeId' 	=> 66,
    				'value' 	=> 'Skrill'
    			],
    			[
    				'typeId'	=> 3,
    				'value'		=> (string)$basket->methodOfPaymentId
    			]
    		],
    		'addressRelations' 	=> $this->getAddressRelations(),
    		'relations' 			=> [
    			[
    				'referenceType' => 'contact',
	    			'relation' 		=> 'receiver'
    			]
    		]
    	];

    	if ($this->paymentHelper->getIsLogin()) {
    		$basketsData['relations'][0]['referenceId'] = $basket->customerId;
    	} else {
    		$basketsData['relations'][0]['referenceId'] = 0;
    	}

    	return $basketsData;
    }

    /**
     * get Order Items
     *
     * @param array $basket
     * @return array $orderItems
     */
    protected function getOrderItems(Basket $basket)
    {
    	$basketService = pluginApp(BasketService::class);
    	$basketItemsForTemplate = $basketService->getBasketItemsForTemplate();
    	$this->getLogger(__METHOD__)->error('Skrill:basketItemsForTemplate :', $basketItemsForTemplate);
    	$this->getLogger(__METHOD__)->error('Skrill:basketItems :', $basket->basketItems);

    	foreach ($basket->basketItems as $key => $item) {
			$itemName = $this->paymentHelper->getVariationDescription($item->variationId);
			$orderItems[$key] = [
				'typeId' => 1,
				'referrerId' => $item->referrerId,
				'itemVariationId' => $item->variationId,
				'quantity' => $item->quantity,
				'vatRate' => $item->vat,
				'orderItemName' => $itemName[0]->name,
				'shippingProfileId' => $item->shippingProfileId
			];

			foreach ($basketItemsForTemplate as $basketItem) {
				if ($basketItem['id'] == $item->id) {
					$orderItems[$key]['amounts'] = [
						[
							'isSystemCurrency' => true,
							'currency' => $basket->currency,
							'exchangeRate' => 1,
							'priceOriginalGross' => $basketItem['variation']['data']['prices']['default']['data']['basePrice'],
							'priceOriginalNet' => $basketItem['variation']['data']['prices']['default']['data']['basePriceNet'],
							'priceGross' => $basketItem['variation']['data']['prices']['default']['data']['unitPrice'],
							'priceNet' => $basketItem['variation']['data']['prices']['default']['data']['unitPriceNet'],
							'discount' => $basketItem['variation']['data']['prices']['default']['data']['customerClassDiscount'],
							'isPercentage' => true
						]
					];

					$orderItems[$key]['properties'] = [
						[
							'typeId' => 1,
							'value' => "1"
						]
					];
				}
			}
		} 

		return $orderItems;
    }

    /**
     * get Address Relations
     *
     * @param array $basket
     * @return array $addressRelations
     */
    private function getAddressRelations()
    {
    	$addresses = $this->basketServiceContract->getCustomerAddressData();
		$addressRelations = [
			[
				'typeId' => 1,
				'addressId' => $addresses['billing']->id
			],
			[
				'typeId' => 2,
				'addressId' => $addresses['shipping']->id
			]
		];

    	return $addressRelations;
    }

    /**
     * @param Basket $basket
     * @param string $paymentMethod
     * @param int $mopId
     * @param array $additionalParams
     * @return string $sidResult
     */
    public function sendPaymentRequest(
		Basket $basket,
		string $paymentMethod,
		int $mopId,
		array $additionalParams = []
	)
	{
		$transactionId = $this->createNewTransactionId($basket);
		$skrillParameters = $this->prepareSkrillParameters($basket, $paymentMethod, $mopId, $transactionId, $additionalParams);
		$sidResult = $this->gatewayService->getSidResult($skrillParameters);

		return $sidResult;
	}

    /**
     * @param Basket $basket
     * @param string $paymentMethod
     * @param int $mopId
     * @param string $transactionId
     * @param array $additionalParams
     * @throws RuntimeException
     */
    private function prepareSkrillParameters(
        Basket $basket,
        string $paymentMethod,
        int $mopId,
        string $transactionId,
        array $additionalParams = []
    ){
    	$this->getLogger(__METHOD__)->error('Skrill:Start', $basketArray);

        $basketArray = $basket->toArray();
        $paymentKey = $this->methodConfigContract->getPaymentMethodKey($paymentMethod);

        // set customer personal information & address data
        $addresses      = $this->basketServiceContract->getCustomerAddressData();
        $this->getLogger(__METHOD__)->error('Skrill:addresses', $addresses);
        $billingAddress = $addresses['billing'];

        if ($this->sessionStorageFactory->getCustomer()->showNetPrice) {
            $basketArray['itemSum']        = $basketArray['itemSumNet'];
            $basketArray['basketAmount']   = $basketArray['basketAmountNet'];
            $basketArray['shippingAmount'] = $basketArray['shippingAmountNet'];
        }

        $parameters = [
			'pay_to_email' => $additionalParams['merchantAccount'],
			'recipient_description' => $additionalParams['recipient'],
			'transaction_id' => $transactionId,
			'return_url' => $this->paymentHelper->getDomain().
				'/payment/skrill/return?basketId='.$basket->id.'&mopId='.$mopId,
			'status_url' => $this->paymentHelper->getDomain().
				'/payment/skrill/status?&paymentKey='.$paymentKey.'&mopId='.$mopId.'&basketId='.$basket->id,
			'cancel_url' => $this->paymentHelper->getDomain().'/'.strtolower($this->getLanguage()).'/checkout',
			'language' => $this->getLanguage(),
			'logo_url' => $additionalParams['logoUrl'],
			'prepare_only' => 1,
			'firstname' => $billingAddress->firstName,
			'lastname' => $billingAddress->lastName,
			'address' => $billingAddress->address1,
			'postal_code' => $billingAddress->postalCode,
			'city' => $billingAddress->town,
			'country' => $billingAddress->country->isoCode2,
			'amount' => $basketArray['basketAmount'],
			'currency' => $basketArray['currency'],
			'detail1_description' => 'Order',
			'detail1_text' => $transactionId,
			'detail2_description' => "Order Amount",
			'detail2_text' => $basketArray['basketAmount'] . ' ' . $basketArray['currency'],
			'detail3_description' => "Shipping",
			'detail3_text' => $basketArray['shippingAmount'] . ' ' . $basketArray['currency'],
			'merchant_fields' => 'platform',
			'platform' => '21477252',
		];

		if ($paymentKey == 'SKRILL_ACC')
		{
			$parameters['payment_methods'] = 'VSA, MSC';
		}
		elseif ($paymentKey != 'SKRILL_APM')
		{
			$parameters['payment_methods'] = str_replace('SKRILL_', '', $paymentKey);
		}
		if (!empty($additionalParams['merchantEmail']))
		{
			$parameters['status_url2'] = 'mailto:' . $additionalParams['merchantEmail'];
		}

		return $parameters;
    }

    /**
     * Creates transactionId and store it in the customer session to fetch the correct transaction later.
     *
     * @param Basket $basket
     * @return string
     */
    private function createNewTransactionId(Basket $basket): string
    {
    	$transactionId = time() . $this->getRandomNumber(4) . $basket->id;;
        $this->sessionStorageFactory->getPlugin()->setValue(SessionKeys::SESSION_KEY_TRANSACTION_ID, $transactionId);
        return $transactionId;
    }

	/**
	 * Returns the language code when use at checkout.
	 *
	 * @return string
	 */
	private function getLanguage()
	{
		$language = $this->sessionStorageFactory->getLocaleSettings()->language;
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
	 * @return array
	 */
	public function executePayment()
	{
		$transactionId = $this->sessionStorageFactory->getPlugin()->getValue(SessionKeys::SESSION_KEY_TRANSACTION_ID);

		$this->sessionStorageFactory->getPlugin()->setValue(SessionKeys::SESSION_KEY_TRANSACTION_ID, null);

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
