<?php

namespace Skrill\Providers;

use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Modules\Basket\Contracts\BasketRepositoryContract;
use Plenty\Modules\Basket\Events\Basket\AfterBasketChanged;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Frontend\Events\FrontendLanguageChanged;
use Plenty\Modules\Frontend\Events\FrontendUpdateInvoiceAddress;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;

use Skrill\Services\PaymentService;
use Skrill\Helper\PaymentHelper;
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
use Skrill\Procedures\RefundEventProcedure;
use Skrill\Procedures\UpdateOrderStatusEventProcedure;

/**
* Class SkrillServiceProvider
* @package Skrill\Providers
*/
class SkrillServiceProvider extends ServiceProvider
{
	/**
	 * Register the route service provider
	 */
	public function register()
	{
		$this->getApplication()->register(SkrillRouteServiceProvider::class);
		$this->getApplication()->bind(RefundEventProcedure::class);
		$this->getApplication()->bind(UpdateOrderStatusEventProcedure::class);
	}

	/**
	 * Boot additional Skrill services
	 *
	 * @param Dispatcher                        $eventDispatcher
	 * @param PaymentHelper                     $paymentHelper
	 * @param PaymentService                    $paymentService
	 * @param BasketRepositoryContract          $basket
	 * @param PaymentMethodContainer            $payContainer
	 * @param PaymentMethodRepositoryContract   $paymentMethodService
	 * @param EventProceduresService            $eventProceduresService
	 */
	public function boot(
					Dispatcher $eventDispatcher,
					PaymentHelper $paymentHelper,
					PaymentService $paymentService,
					BasketRepositoryContract $basket,
					PaymentMethodContainer $payContainer,
					PaymentMethodRepositoryContract $paymentMethodService,
					EventProceduresService $eventProceduresService
	) {
		$this->registerPaymentMethod($payContainer, 'SKRILL_PCH', PchPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_APM', ApmPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_WLT', WltPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_PSC', PscPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_ACC', AccPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_VSA', VsaPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_MSC', MscPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_MAE', MaePaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_AMX', AmxPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_GCB', GcbPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_DNK', DnkPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_PSP', PspPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_CSI', CsiPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_OBT', ObtPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_GIR', GirPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_DID', DidPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_SFT', SftPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_EBT', EbtPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_IDL', IdlPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_NPY', NpyPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_PLI', PliPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_PWY', PwyPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_EPY', EpyPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_ALI', AliPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_NTL', NtlPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_ACI', AciPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_ADB', AdbPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_AOB', AobPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_AUP', AupPaymentMethod::class);
		$this->registerPaymentMethod($payContainer, 'SKRILL_BTC', BtcPaymentMethod::class);

		// Register Skrill Refund Event Procedure
		$eventProceduresService->registerProcedure(
						'Skrill',
						ProcedureEntry::PROCEDURE_GROUP_ORDER,
						[
						'de' => 'Rückzahlung der Skrill-Zahlung',
						'en' => 'Refund the Skrill-Payment'
						],
						'Skrill\Procedures\RefundEventProcedure@run'
		);

		// Register Skrill Update Order Status Event Procedure
		$eventProceduresService->registerProcedure(
						'Skrill',
						ProcedureEntry::PROCEDURE_GROUP_ORDER,
						[
						'de' => 'Update order status the Skrill-Payment',
						'en' => 'Update order status the Skrill-Payment'
						],
						'Skrill\Procedures\UpdateOrderStatusEventProcedure@run'
		);

		// Listen for the event that gets the payment method content
		$eventDispatcher->listen(
						GetPaymentMethodContent::class,
						function (GetPaymentMethodContent $event) use ($paymentHelper, $basket, $paymentService, $paymentMethodService) {
							if ($paymentHelper->isSkrillPaymentMopId($event->getMop()))
							{
								$content = $paymentService->getPaymentContent(
												$basket->load(),
												$paymentMethodService->findByPaymentMethodId($event->getMop())
								);
								$event->setValue(isset($content['content']) ? $content['content'] : null);
								$event->setType(isset($content['type']) ? $content['type'] : '');
							}
						}
		);

		// Listen for the event that executes the payment
		$eventDispatcher->listen(
						ExecutePayment::class,
						function (ExecutePayment $event) use ($paymentHelper, $paymentService) {
							if ($paymentHelper->isSkrillPaymentMopId($event->getMop()))
							{
								$result = $paymentService->executePayment($event->getOrderId());

								$event->setType($result['type']);
								$event->setValue($result['value']);
							}
						}
		);
	}

	/**
	 * register payment method.
	 *
	 * @param PaymentMethodContainer $payContainer
	 * @param string $paymentKey
	 * @param PaymentMethodService $class
	 */
	private function registerPaymentMethod($payContainer, $paymentKey, $class)
	{
		$payContainer->register(
						'skrill::' . $paymentKey,
						$class,
						[
							AfterBasketChanged::class,
							AfterBasketItemAdd::class,
							AfterBasketCreate::class,
							FrontendLanguageChanged::class,
							FrontendUpdateInvoiceAddress::class
						]
		);
	}
}
