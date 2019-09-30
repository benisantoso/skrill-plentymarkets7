<?php

namespace Skrill\Helper;

use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentOrderRelationRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Payment\Contracts\PaymentPropertyRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Frontend\Events\FrontendLanguageChanged;
use Plenty\Modules\Frontend\Events\FrontendShippingCountryChanged;
use Plenty\Modules\Order\Shipping\Countries\Contracts\CountryRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Item\VariationDescription\Contracts\VariationDescriptionRepositoryContract;
use IO\Services\CustomerService;

use Skrill\Methods\PchPaymentMethod;
use Skrill\Methods\AccPaymentMethod;
use Skrill\Methods\AciPaymentMethod;
use Skrill\Methods\AdbPaymentMethod;
use Skrill\Methods\AliPaymentMethod;
use Skrill\Methods\AmxPaymentMethod;
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


use Skrill\Constants\Plugin;
use Skrill\Configs\MethodConfig;

/**
* Class PaymentHelper
* @package Skrill\Helper
*/
class PaymentHelper
{
	use Loggable;

	const NO_PAYMENTMETHOD_FOUND = -1;

	/**
	 * @var PaymentMethodRepositoryContract
	 */
	private $paymentMethodRepository;

	/**
	 * @var PaymentOrderRelationRepositoryContract
	 */
	private $paymentOrderRelationRepository;

	/**
	 * @var PaymentRepositoryContract
	 */
	private $paymentRepository;

	/**
	 * @var PaymentPropertyRepositoryContract
	 */
	private $paymentPropertyRepository;

	/**
	 * @var OrderRepositoryContract
	 */
	private $orderRepository;

	/** @var MethodConfig $methodConfig */
    private $methodConfig;

	/**
	 * PaymentHelper constructor.
	 *
	 * @param PaymentMethodRepositoryContract $paymentMethodRepository
	 * @param PaymentRepositoryContract $paymentRepository
	 * @param PaymentPropertyRepositoryContract $paymentPropertyRepository
	 * @param PaymentOrderRelationRepositoryContract $paymentOrderRelationRepository
	 * @param OrderRepositoryContract $orderRepository
	 */
	public function __construct(
					PaymentMethodRepositoryContract $paymentMethodRepository,
					PaymentRepositoryContract $paymentRepository,
					MethodConfig $methodConfig,
					PaymentPropertyRepositoryContract $paymentPropertyRepository,
					PaymentOrderRelationRepositoryContract $paymentOrderRelationRepository,
					OrderRepositoryContract $orderRepository
	) {
		$this->paymentMethodRepository          = $paymentMethodRepository;
		$this->paymentOrderRelationRepository   = $paymentOrderRelationRepository;
		$this->paymentRepository                = $paymentRepository;
		$this->paymentPropertyRepository        = $paymentPropertyRepository;
		$this->orderRepository                  = $orderRepository;
		$this->methodConfig 					= $methodConfig;
	}

	/**
	 * get Customer Id is active
	 *
	 * 
	 * @return int $customerId
	 */
	public function getCustomerId() 
	{
		$customerService = pluginApp(CustomerService::class);
		$customerId = $customerService->getContactId();
		return $customerId;
	}

	/**
     * Create the payment method IDs that don't exist yet.
     */
    public function createMopsIfNotExist()
    {
        foreach ($this->methodConfig::getPaymentMethods() as $paymentMethod) {
            $this->createMopIfNotExists($paymentMethod);
        }
    }

	/**
     * Create the payment method ID if it doesn't exist yet
     *
     * @param string $paymentMethodClass
     */
    public function createMopIfNotExists(string $paymentMethodClass)
    {
        if ($this->getPaymentMethodId($paymentMethodClass) === self::NO_PAYMENTMETHOD_FOUND) {
            $paymentMethodData = [
                'pluginKey' => Plugin::KEY,
                'paymentKey' => $this->methodConfig->getPaymentMethodKey($paymentMethodClass),
                'name' => $this->methodConfig->getPaymentMethodDefaultName($paymentMethodClass)
            ];

            $this->paymentMethodRepository->createPaymentMethod($paymentMethodData);
        }
    }

    /**
     * Load the payment method ID for the given plugin key.
     *
     * @param string $paymentMethodClass
     *
     * @return int
     */
    public function getPaymentMethodId(string $paymentMethodClass)
    {
        $paymentMethods = $this->paymentMethodRepository->allForPlugin(Plugin::KEY);

        if (!empty($paymentMethods)) {
            /** @var PaymentMethod $payMethod */
            foreach ($paymentMethods as $payMethod) {
                if ($payMethod->paymentKey === $this->methodConfig->getPaymentMethodKey($paymentMethodClass)) {
                    return $payMethod->id;
                }
            }
        }

        return self::NO_PAYMENTMETHOD_FOUND;
    }

    /**
     * Returns the payment method key ('plugin_name::payment_key')
     *
     * @param string $paymentMethodClass
     *
     * @return string
     */
    public function getPluginPaymentMethodKey(string $paymentMethodClass): string
    {
        return Plugin::KEY . '::' . $this->methodConfig->getPaymentMethodKey($paymentMethodClass);
    }

    /**
     * Returns a list of events that should be observed.
     *
     * @return array
     */
    public function getPaymentMethodEventList(): array
    {
        return [
            AfterBasketChanged::class,
            AfterBasketItemAdd::class,
            AfterBasketCreate::class,
            FrontendLanguageChanged::class,
            FrontendShippingCountryChanged::class
        ];
    }

    /**
     * @param $mop
     * @return string
     */
    public function mapMopToPaymentMethod($mop): string
    {
        $paymentMethod = '';

        foreach ($this->methodConfig::getPaymentMethods() as $paymentMethodClass) {
            if ($this->getPaymentMethodId($paymentMethodClass) == $mop) {
            	$paymentMethod = $paymentMethodClass;
            }
        }
        $this->getLogger(__METHOD__)->error('Skrill:paymentMethod', $paymentMethod);

        return $paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return PaymentMethodContract|null
     */
    public function getPaymentMethodInstance(string $paymentMethod)
    {
        /** @var PaymentMethodContract $instance */
        
        switch ($paymentMethod) {
        	case PchPaymentMethod::class:
        		$instance = pluginApp(PchPaymentMethod::class);
        		break;
        	case AccPaymentMethod::class:
        		$instance = pluginApp(AccPaymentMethod::class);
        		break;
        	case AciPaymentMethod::class:
        		$instance = pluginApp(AciPaymentMethod::class);
        		break;
        	case AdbPaymentMethod::class:
        		$instance = pluginApp(AdbPaymentMethod::class);
        		break;
        	case AliPaymentMethod::class:
        		$instance = pluginApp(AliPaymentMethod::class);
        		break;
        	case AmxPaymentMethod::class:
        		$instance = pluginApp(AmxPaymentMethod::class);
        		break;
        	case AobPaymentMethod::class:
        		$instance = pluginApp(AobPaymentMethod::class);
        		break;
        	case ApmPaymentMethod::class:
        		$instance = pluginApp(ApmPaymentMethod::class);
        		break;
        	case AupPaymentMethod::class:
        		$instance = pluginApp(AupPaymentMethod::class);
        		break;
        	case BtcPaymentMethod::class:
        		$instance = pluginApp(BtcPaymentMethod::class);
        		break;
        	case CsiPaymentMethod::class:
        		$instance = pluginApp(CsiPaymentMethod::class);
        		break;
        	case DidPaymentMethod::class:
        		$instance = pluginApp(DidPaymentMethod::class);
        		break;
        	case DnkPaymentMethod::class:
        		$instance = pluginApp(DnkPaymentMethod::class);
        		break;
        	case EbtPaymentMethod::class:
        		$instance = pluginApp(EbtPaymentMethod::class);
        		break;
        	case EpyPaymentMethod::class:
        		$instance = pluginApp(EpyPaymentMethod::class);
        		break;
        	case GcbPaymentMethod::class:
        		$instance = pluginApp(GcbPaymentMethod::class);
        		break;
        	case GirPaymentMethod::class:
        		$instance = pluginApp(GirPaymentMethod::class);
        		break;
        	case IdlPaymentMethod::class:
        		$instance = pluginApp(IdlPaymentMethod::class);
        		break;
        	case MaePaymentMethod::class:
        		$instance = pluginApp(MaePaymentMethod::class);
        		break;
        	case MscPaymentMethod::class:
        		$instance = pluginApp(MscPaymentMethod::class);
        		break;
        	case NpyPaymentMethod::class:
        		$instance = pluginApp(NpyPaymentMethod::class);
        		break;
        	case NtlPaymentMethod::class:
        		$instance = pluginApp(NtlPaymentMethod::class);
        		break;
        	case ObtPaymentMethod::class:
        		$instance = pluginApp(ObtPaymentMethod::class);
        		break;
        	case PliPaymentMethod::class:
        		$instance = pluginApp(PliPaymentMethod::class);
        		break;
        	case PscPaymentMethod::class:
        		$instance = pluginApp(PscPaymentMethod::class);
        		break;
        	case PspPaymentMethod::class:
        		$instance = pluginApp(PspPaymentMethod::class);
        		break;
        	case PwyPaymentMethod::class:
        		$instance = pluginApp(PwyPaymentMethod::class);
        		break;
        	case SftPaymentMethod::class:
        		$instance = pluginApp(SftPaymentMethod::class);
        		break;
        	case VsaPaymentMethod::class:
        		$instance = pluginApp(VsaPaymentMethod::class);
        		break;
        	case WltPaymentMethod::class:
        		$instance = pluginApp(WltPaymentMethod::class);
        		break;
        	
        	default:
        		$instance = null;
        		break;
        }

        return $instance;
    }

	/**
	 * get Customer Login status
	 *
	 * 
	 * @return bool $isLogin
	 */
	public function getIsLogin() 
	{
		if ($this->getCustomerId()) {
			$isLogin = true;
		} else {
			$isLogin = false;
		}

		return $isLogin;
	}

	/**
	 * get payment method by paymentkey
	 *
	 * @param string $paymentKey
	 * @return null|Plenty\Modules\Payment\Method\Models\PaymentMethod
	 */
	public function getPaymentMethodByPaymentKey($paymentKey)
	{
		// List all payment methods for the given plugin
		$paymentMethods = $this->paymentMethodRepository->allForPlugin('skrill');

		if (strlen($paymentKey) && !is_null($paymentMethods))
		{
			foreach ($paymentMethods as $paymentMethod)
			{
				if ($paymentMethod->paymentKey == $paymentKey)
				{
					return $paymentMethod;
				}
			}
		}

		return null;
	}

	/**
	 * check if the mopId is Skrill mopId.
	 *
	 * @param number $mopId
	 * @return bool
	 */
	public function isSkrillPaymentMopId($mopId)
	{
		$paymentMethods = $this->paymentMethodRepository->allForPlugin(Plugin::KEY);

		if (!is_null($paymentMethods))
		{
			foreach ($paymentMethods as $paymentMethod)
			{
				if ($paymentMethod->id == $mopId)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Create a debit payment when create a note (make refund).
	 *
	 * @param Payment $payment
	 * @param array $refundStatus
	 * @return Payment
	 */
	public function createPlentyRefundPayment($payment, $refundStatus)
	{
		$debitPayment = pluginApp(\Plenty\Modules\Payment\Models\Payment::class);

		$debitPayment->mopId = $payment->mopId;
		$debitPayment->parentId = $payment->id;
		$debitPayment->type = 'debit';
		$debitPayment->transactionType = Payment::TRANSACTION_TYPE_BOOKED_POSTING;
		$debitPayment->currency = (string)$refundStatus->mb_currency;
		$debitPayment->amount = (string)$refundStatus->mb_amount;

		$state = $this->mapTransactionState((string)$refundStatus->status, true);

		$debitPayment->status = $state;

		if ($state == Payment::STATUS_REFUNDED)
		{
			$debitPayment->unaccountable = 0;
		} else {
			$debitPayment->unaccountable = 1;
		}

		$paymentProperty = [];
		$paymentProperty[] = $this->getPaymentProperty(
						PaymentProperty::TYPE_TRANSACTION_ID,
						(string)$refundStatus->mb_transaction_id
		);
		$paymentProperty[] = $this->getPaymentProperty(PaymentProperty::TYPE_ORIGIN, Payment::ORIGIN_PLUGIN);
		$paymentProperty[] = $this->getPaymentProperty(
						PaymentProperty::TYPE_BOOKING_TEXT,
						$this->getRefundPaymentBookingText($refundStatus)
		);

		$debitPayment->properties = $paymentProperty;
		$debitPayment->regenerateHash = true;

		$debitPayment = $this->paymentRepository->createPayment($debitPayment);

		$this->getLogger(__METHOD__)->error('Skrill:debitPayment', $debitPayment);

		return $debitPayment;
	}

	/**
	 * Create a credit payment when status_url triggered and no payment created before.
	 *
	 * @param array $paymentStatus
	 * @return Payment
	 */
	public function createPlentyPayment($paymentStatus)
	{
		$generatedSignatured = $this->generateMd5sigByResponse($paymentStatus);
		$isCredentialValid = $this->isPaymentSignatureEqualsGeneratedSignature(
						$paymentStatus['md5sig'],
						$generatedSignatured
		);

		$this->getLogger(__METHOD__)->error('Skrill:isCredentialValid', $isCredentialValid);

		$payment = pluginApp(\Plenty\Modules\Payment\Models\Payment::class);

		$mopId = 0;
		$paymentMethod = $this->getPaymentMethodByPaymentKey($paymentStatus['paymentKey']);

		if (!empty($paymentMethod))
		{
			$mopId = $paymentMethod->id;
		}

		$payment->mopId = (int) $mopId;
		$payment->transactionType = Payment::TRANSACTION_TYPE_BOOKED_POSTING;

		if (!$isCredentialValid)
		{
			$state = Payment::STATUS_AWAITING_APPROVAL;
		}
		else
		{
			$state = $this->mapTransactionState((string)$paymentStatus['status']);
		}

		$payment->status = $state;
		$payment->currency = $paymentStatus['currency'];
		$payment->amount = $paymentStatus['amount'];

		if ($state == Payment::STATUS_APPROVED)
		{
			$payment->unaccountable = 0;
		} else {
			$payment->unaccountable = 1;
		}

		$paymentProperty = [];
		$paymentProperty[] = $this->getPaymentProperty(
						PaymentProperty::TYPE_TRANSACTION_ID,
						$paymentStatus['transaction_id']
		);
		$paymentProperty[] = $this->getPaymentProperty(PaymentProperty::TYPE_ORIGIN, Payment::ORIGIN_PLUGIN);
		$paymentProperty[] = $this->getPaymentProperty(
						PaymentProperty::TYPE_BOOKING_TEXT,
						$this->getPaymentBookingText($paymentStatus, $isCredentialValid)
		);

		if (isset($paymentStatus['pay_to_email']))
		{
			$paymentProperty[] = $this->getPaymentProperty(
							PaymentProperty::TYPE_ACCOUNT_OF_RECEIVER,
							$paymentStatus['pay_to_email']
			);
		}

		if (isset($paymentStatus['failed_reason_code']))
		{
			$paymentProperty[] = $this->getPaymentProperty(
							PaymentProperty::TYPE_EXTERNAL_TRANSACTION_STATUS,
							$paymentStatus['failed_reason_code']
			);
		}

		$payment->properties = $paymentProperty;
		$payment->regenerateHash = true;

		$payment = $this->paymentRepository->createPayment($payment);

		$this->getLogger(__METHOD__)->error('Skrill:payment', $payment);

		return $payment;
	}

	/**
	 * update the payment by transaction_id when status_url triggered if payment already created before.
	 * create a payment if no payment created before.
	 *
	 * @param array $paymentStatus
	 */
	public function updatePlentyPayment($paymentStatus)
	{
		$payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
						PaymentProperty::TYPE_TRANSACTION_ID,
						$paymentStatus['transaction_id']
		);
		$this->getLogger(__METHOD__)->error('Skrill:typeTransactionId', PaymentProperty::TYPE_TRANSACTION_ID);
		$this->getLogger(__METHOD__)->error('Skrill:paymentStatus', $paymentStatus);
		$this->getLogger(__METHOD__)->error('Skrill:transactionId', $paymentStatus['transaction_id']);
		$this->getLogger(__METHOD__)->error('Skrill:payments', $payments);

		if (count($payments) > 0)
		{
			$generatedSignatured = $this->generateMd5sigByResponse($paymentStatus);
			$isCredentialValid = $this->isPaymentSignatureEqualsGeneratedSignature(
							$paymentStatus['md5sig'],
							$generatedSignatured
			);

			$this->getLogger(__METHOD__)->error('Skrill:isCredentialValid', $isCredentialValid);

			$state = $this->mapTransactionState((string)$paymentStatus['status']);
			foreach ($payments as $payment)
			{
				if ($isCredentialValid && $payment->status != $state)
				{
					$payment->status = $state;

					if ($state == Payment::STATUS_APPROVED)
					{
						$payment->unaccountable = 0;
						$payment->updateOrderPaymentStatus = true;
					}
				}

				$this->updatePaymentPropertyValue(
								$payment->properties,
								PaymentProperty::TYPE_BOOKING_TEXT,
								$this->getPaymentBookingText($paymentStatus, $isCredentialValid)
				);

				$this->getLogger(__METHOD__)->error('Skrill:update_payment', $payment);

				$this->paymentRepository->updatePayment($payment);
			}
		} else {
			$payment = $this->createPlentyPayment($paymentStatus);

			$this->assignPlentyPaymentToPlentyOrder($payment, (int) $paymentStatus['orderId']);
		}
	}

	/**
	 * update the debit payment by mb_transaction_id when refund_status_url triggered.
	 *
	 * @param array $refundStatus
	 */
	public function updatePlentyRefundPayment($refundStatus)
	{
		$payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
						PaymentProperty::TYPE_TRANSACTION_ID,
						$refundStatus['mb_transaction_id']
		);

		$this->getLogger(__METHOD__)->error('Skrill:payments', $payments);

		if (count($payments) > 0)
		{
			$state = $this->mapTransactionState((string)$refundStatus['status'], true);
			foreach ($payments as $payment)
			{
				if ($payment->status != $state)
				{
					$payment->status = $state;

					if ($state == Payment::STATUS_REFUNDED)
					{
						$payment->unaccountable = 0;
						$payment->updateOrderPaymentStatus = true;
					}

					$this->updatePaymentPropertyValue(
									$payment->properties,
									PaymentProperty::TYPE_BOOKING_TEXT,
									$this->getRefundPaymentBookingText($refundStatus)
					);

					$this->getLogger(__METHOD__)->error('Skrill:update_payment', $payment);

					$this->paymentRepository->updatePayment($payment);
				}
			}
		}
	}

	/**
	 * update payment property value.
	 *
	 * @param array $properties
	 * @param int $propertyType
	 * @param string $value
	 */
	public function updatePaymentPropertyValue($properties, $propertyType, $value)
	{
		if (count($properties) > 0)
		{
			foreach ($properties as $property)
			{
				if ($property->typeId == $propertyType)
				{
					$paymentProperty = $property;
					break;
				}
			}

			if (isset($paymentProperty))
			{
				$paymentProperty->value = $value;
				$this->paymentPropertyRepository->changeProperty($paymentProperty);
			}
		}
	}

	/**
	 * get payment property value.
	 *
	 * @param array $properties
	 * @param int $propertyType
	 * @return null|string
	 */
	public function getPaymentPropertyValue($properties, $propertyType)
	{
		if (count($properties) > 0)
		{
			foreach ($properties as $property)
			{
				if ($property instanceof PaymentProperty && $property->typeId == $propertyType)
				{
					return $property->value;
				}
			}
		}

		return null;
	}

	/**
	 * get order payment status by transactionId (success or error)
	 *
	 * @param string $transactionId
	 * @return array
	 */
	public function getOrderPaymentStatus($transactionId)
	{
		$payments = $this->paymentRepository->getPaymentsByPropertyTypeAndValue(
						PaymentProperty::TYPE_TRANSACTION_ID,
						$transactionId
		);

		$status = '';
		$properties = [];

		if (count($payments) > 0)
		{
			foreach ($payments as $payment)
			{
				$status = $payment->status;
				$properties = $payment->properties;
				break;
			}
		}

		$this->getLogger(__METHOD__)->error('Skrill:payments', $status);

		$this->getLogger(__METHOD__)->error('Skrill:status', $status);

		if ($status == Payment::STATUS_REFUSED)
		{
			$failedReasonCode = $this->getPaymentPropertyValue(
							$properties,
							PaymentProperty::TYPE_EXTERNAL_TRANSACTION_STATUS
			);

			$this->getLogger(__METHOD__)->error('Skrill:failedReasonCode', $failedReasonCode);

			return [
				'type' => 'error',
				'value' => 'The payment has been failed : ' . $this->getSkrillErrorMessage($failedReasonCode)
			];
		}

		return [
			'type' => 'success',
			'value' => 'The payment has been executed successfully.'
		];
	}

	/**
	 * get payment booking text (use for show payment detail information).
	 *
	 * @param array $paymentStatus
	 * @param bool $isCredentialValid
	 * @return string
	 */
	public function getPaymentBookingText($paymentStatus, $isCredentialValid)
	{
		$paymentBookingText = [];
		$countryRepository = pluginApp(CountryRepositoryContract::class);

		if (isset($paymentStatus['transaction_id']))
		{
			$paymentBookingText[] = "Transaction ID : " . (string) $paymentStatus['transaction_id'];
		}
		if (isset($paymentStatus['payment_type']))
		{
			if ($paymentStatus['payment_type'] == 'NGP')
			{
				$paymentStatus['payment_type'] = 'OBT';
			}
			$paymentMethod = $this->getPaymentMethodByPaymentKey('SKRILL_' . $paymentStatus['payment_type']);
			if (isset($paymentMethod))
			{
				$paymentBookingText[] = "Used payment method : " . $paymentMethod->name;
			}
		}
		if (isset($paymentStatus['status']))
		{
			$paymentBookingText[] = "Payment status : " .
				$this->getPaymentStatus((string)$paymentStatus['status'], $isCredentialValid);
		}
		if (isset($paymentStatus['IP_country']))
		{
			$ipCountry = $countryRepository->getCountryByIso($paymentStatus['IP_country'], 'isoCode2');
			$paymentBookingText[] = "Order originated from : " . $ipCountry->name;
		}
		if (isset($paymentStatus['payment_instrument_country']))
		{
			$paymentInstrumentCountry = $countryRepository->getCountryByIso(
							$paymentStatus['payment_instrument_country'],
							'isoCode3'
			);
			$paymentBookingText[] = "Country (of the card-issuer) : " . $paymentInstrumentCountry->name;
		}
		if (isset($paymentStatus['pay_from_email']))
		{
			$paymentBookingText[] = "Skrill account email : " . $paymentStatus['pay_from_email'];
		}
		if (!empty($paymentBookingText))
		{
			return implode("\n", $paymentBookingText);
		}

		return '';
	}

	/**
	 * get refund payment booking text (use for show refund payment detail information).
	 *
	 * @param array $refundStatus
	 * @return string
	 */
	public function getRefundPaymentBookingText($refundStatus)
	{
		$paymentBookingText = [];

		if (is_array($refundStatus))
		{
			$mbTransactionId = (string)$refundStatus['mb_transaction_id'];
			$status = (string)$refundStatus['status'];
		}
		else
		{
			$mbTransactionId = (string)$refundStatus->mb_transaction_id;
			$status = (string)$refundStatus->status;
		}
		if (isset($mbTransactionId))
		{
			$paymentBookingText[] = "MB Transaction ID : " . $mbTransactionId;
		}
		if (isset($status))
		{
			$paymentBookingText[] = "Refund status : " . $this->getPaymentStatus($status);
		}
		if (!empty($paymentBookingText))
		{
			return implode("\n", $paymentBookingText);
		}

		return '';
	}

	/**
	 * get payment status (use for payment/refund detail information status).
	 *
	 * @param string $status
	 * @param bool $isCredentialValid
	 * @return string
	 */
	public function getPaymentStatus(string $status, $isCredentialValid = true)
	{
		if (!$isCredentialValid)
		{
			return 'Invalid Credential';
		}

		switch ($status)
		{
			case '0':
				return 'Pending';
			case '2':
				return 'Processed';
			case '-2':
				return 'Failed';
		}

		return 'null';
	}

	/**
	 * Returns the plentymarkets payment status matching the given payment response status.
	 *
	 * @param string $status
	 * @param bool $isRefund
	 * @return int
	 */
	public function mapTransactionState(string $status, $isRefund = false)
	{
		switch ($status)
		{
			case '0':
				return Payment::STATUS_AWAITING_APPROVAL;
			case '2':
				if ($isRefund)
				{
					return Payment::STATUS_REFUNDED;
				}
				return Payment::STATUS_APPROVED;
			case '-2':
				return Payment::STATUS_REFUSED;
		}

		return Payment::STATUS_AWAITING_APPROVAL;
	}

	/**
	 * Returns a PaymentProperty with the given params
	 *
	 * @param int $typeId
	 * @param string $value
	 * @return PaymentProperty
	 */
	private function getPaymentProperty($typeId, $value)
	{
		$paymentProperty = pluginApp(PaymentProperty::class);

		$paymentProperty->typeId = $typeId;
		$paymentProperty->value = (string) $value;

		return $paymentProperty;
	}

	/**
	 * Assign the payment to an order in plentymarkets.
	 *
	 * @param Payment $payment
	 * @param int $orderId
	 */
	public function assignPlentyPaymentToPlentyOrder(Payment $payment, int $orderId)
	{
		$orderRepo = pluginApp(OrderRepositoryContract::class);
		$authHelper = pluginApp(AuthHelper::class);

		$order = $authHelper->processUnguarded(
						function () use ($orderRepo, $orderId) {
							return $orderRepo->findOrderById($orderId);
						}
		);

		if (!is_null($order) && $order instanceof Order)
		{
			$this->getLogger(__METHOD__)->error('Skrill:payment', $payment);
			$this->getLogger(__METHOD__)->error('Skrill:order', $order);
			$this->paymentOrderRelationRepository->createOrderRelation($payment, $order);
		}
	}

	/**
	 * get domain from webstoreconfig.
	 *
	 * @return string
	 */
	public function getDomain()
	{
		$webstoreHelper = pluginApp(\Plenty\Modules\Helper\Services\WebstoreHelper::class);
		$webstoreConfig = $webstoreHelper->getCurrentWebstoreConfiguration();
		$domain = $webstoreConfig->domainSsl;

		return $domain;
	}

	/**
	 * get error message by failed reason code
	 *
	 * @param int $code
	 * @return string
	 */
	public function getSkrillErrorMessage($code)
	{
		$errorMessages = array(
			"01" => "Referred by card issuer",
			"02" => "Invalid Merchant",
			"03" => "Stolen card",
			"04" => "Declined by customer's Card Issuer",
			"05" => "Insufficient funds",
			"08" => "PIN tries exceeded - card blocked",
			"09" => "Invalid Transaction",
			"10" => "Transaction frequency limit exceeded",
			"12" => "Invalid credit card or bank account",
			"15" => "Duplicate transaction",
			"19" => "Unknown failure reason. Try again",
			"24" => "Card expired",
			"28" => "Lost/Stolen card",
			"32" => "Card Security Code check failed",
			"37" => "Card restricted by card issuer",
			"38" => "Security violation",
			"42" => "Card blocked by card issuer",
			"44" => "Customer's issuing bank not available",
			"51" => "Processing system error",
			"63" => "Transaction not permitted to cardholder ",
			"70" => "Customer failed to complete 3DS",
			"71" => "Customer failed SMS verification",
			"80" => "Fraud engine declined",
			"98" => "Error in communication with provider",
			"99" => "Failure reason not specified"
		);

		if (array_key_exists($code, $errorMessages))
		{
			return $errorMessages[$code];
		}

		return 'Failure reason not specified';
	}

	/**
	 * generate Md5sig by response
	 *
	 * @param array $response
	 * @return string
	 */
	public function generateMd5sigByResponse($response)
	{
		$paymentService = pluginApp(\Skrill\Services\PaymentService::class);
		$skrillSettings = $paymentService->getSkrillSettings();

		$string = $skrillSettings['merchantId'].
				$response['transaction_id'].
				strtoupper(md5($skrillSettings['secretWord'])).
				$response['mb_amount'].
				$response['mb_currency'].
				$response['status'];

		return strtoupper(md5($string));
	}

	/**
	 * check if payment signature equals with value that already generated using function generateMd5sigByResponse.
	 *
	 * @param string $paymentSignature
	 * @param string $generatedSignature
	 * @return bool
	 */
	public function isPaymentSignatureEqualsGeneratedSignature($paymentSignature, $generatedSignature)
	{
		return $paymentSignature == $generatedSignature;
	}

	/**
	 * get Variation Description
	 *
	 * @param BasketItem $basketItem
	 * @return string
	 */
	public function getVariationDescription($variationId)
	{
		$variationDescriptionContract = pluginApp(VariationDescriptionRepositoryContract::class);
		$authHelper = pluginApp(AuthHelper::class);

        $variationDescription = $authHelper->processUnguarded(
            function () use ($variationDescriptionContract, $variationId) {
                return $variationDescriptionContract->findByVariationId($variationId);
            }
        );

		return $variationDescription;
	}

}
