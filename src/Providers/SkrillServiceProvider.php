<?php

namespace Skrill\Providers;

use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;

use Skrill\Services\PaymentService;
use Skrill\Helper\PaymentHelper;
use Skrill\Configs\MethodConfig;
use Skrill\Configs\MethodConfigContract;
use Skrill\Procedures\RefundEventProcedure;
use Skrill\Procedures\UpdateOrderStatusEventProcedure;
use Skrill\Services\BasketService;
use Skrill\Services\BasketServiceContract;

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
		$app = $this->getApplication();
		$app->register(SkrillRouteServiceProvider::class);
		$app->bind(BasketServiceContract::class, BasketService::class);
		$app->bind(MethodConfigContract::class, MethodConfig::class);
		$app->bind(RefundEventProcedure::class);
		$app->bind(UpdateOrderStatusEventProcedure::class);
	}

	/**
	 * Boot additional Skrill services
	 *
	 * @param Dispatcher                        $eventDispatcher
	 * @param PaymentHelper                     $paymentHelper
	 * @param PaymentService                    $paymentService
	 * @param PaymentMethodContainer            $methodContainer
	 * @param PaymentMethodRepositoryContract   $paymentMethodService
	 * @param EventProceduresService            $eventProceduresService
	 */
	public function boot(
					Dispatcher $eventDispatcher,
					PaymentHelper $paymentHelper,
					PaymentService $paymentService,
					PaymentMethodContainer $methodContainer,
					PaymentMethodRepositoryContract $paymentMethodRepo,
					EventProceduresService $eventProceduresService
	) {

		// loop through all of the plugin's available payment methods
        /** @var string $paymentMethodClass */
        foreach (MethodConfig::getPaymentMethods() as $paymentMethodClass) {
            // register the payment method in the payment method container
            $methodContainer->register(
                $paymentHelper->getPluginPaymentMethodKey($paymentMethodClass),
                $paymentMethodClass,
                $paymentHelper->getPaymentMethodEventList()
            );
        }

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
						function (GetPaymentMethodContent $event) use ($paymentHelper, $paymentService) {
							$mop = $event->getMop();
							$paymentMethod = $paymentHelper->mapMopToPaymentMethod($mop);
							if ($paymentHelper->isSkrillPaymentMopId($mop))
							{
								$content = $paymentService->getPaymentContent(
									$paymentMethod,
									$mop
								);
								$event->setValue($content['value']);
								$event->setType($content['type']);
							}
						}
		);

		// Listen for the event that executes the payment
		$eventDispatcher->listen(
						ExecutePayment::class,
						function (ExecutePayment $event) use ($paymentHelper, $paymentService) {
							if ($paymentHelper->isSkrillPaymentMopId($event->getMop()))
							{
								$result = $paymentService->executePayment();

								$event->setValue($result['value']);
								$event->setType($result['type']);
							}
						}
		);
	}
}
