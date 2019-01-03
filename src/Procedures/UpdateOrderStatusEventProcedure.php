<?php
namespace Skrill\Procedures;

use Plenty\Modules\EventProcedures\Events\EventProceduresTriggered;
use Plenty\Modules\Payment\Contracts\PaymentRepositoryContract;
use Plenty\Modules\Order\Models\Order;
use Plenty\Modules\Payment\Models\Payment;
use Plenty\Modules\Payment\Models\PaymentProperty;
use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;

use Skrill\Services\PaymentService;
use Skrill\Services\OrderService;
use Skrill\Helper\PaymentHelper;

/**
* Class UpdateOrderStatusEventProcedure
* @package Skrill\Procedures
*/
class UpdateOrderStatusEventProcedure
{
	use Loggable;

	/**
	 * @param EventProceduresTriggered $eventTriggered
	 * @param PaymentRepositoryContract $paymentRepository
	 * @param PaymentService $paymentService
	 * @param PaymentHelper $paymentHelper
	 * @throws \Exception
	 */
	public function run(
					EventProceduresTriggered $eventTriggered,
					PaymentRepositoryContract $paymentRepository,
					PaymentService $paymentService,
					OrderService $orderService,
					PaymentHelper $paymentHelper,
					OrderRepositoryContract $orderRepository
	) {
		/** @var Order $order */
		$order = $eventTriggered->getOrder();

		// only sales orders are allowed order types to upate order status
		if ($order->typeId == 1)
		{
			$orderId = $order->id;
		}

		if (empty($orderId))
		{
			throw new \Exception('Update order status Skrill payment failed! The given order is invalid!');
		}

		/** @var Payment[] $payment */
		$payments = $paymentRepository->getPaymentsByOrderId($orderId);

		$this->getLogger(__METHOD__)->error('Skrill:payments', $payments);

		if (count($payments) > 0)
		{
			/** @var Payment $payment */
			foreach ($payments as $payment)
			{
				if ($paymentHelper->isSkrillPaymentMopId($payment->mopId))
				{
					$transactionId = $paymentHelper->getPaymentPropertyValue(
									$payment->properties,
									PaymentProperty::TYPE_TRANSACTION_ID
					);

					$this->getLogger(__METHOD__)->error('Skrill:transactionId', $transactionId);

					if (isset($transactionId))
					{
						// update order status the payment
						$updateResult = $paymentService->updateOrderStatus($transactionId, $order);
						$this->getLogger(__METHOD__)->error('Skrill:updateResult', $updateResult);

						if ($updateResult['error'])
						{
							throw new \Exception('Update order status Skrill payment failed!');
						}
						elseif ($updateResult['success'])
						{
							$paymentStatus = $updateResult['response'];

							$generatedSignatured = $paymentHelper->generateMd5sigByResponse($paymentStatus);
							$isCredentialValid = $paymentHelper->isPaymentSignatureEqualsGeneratedSignature(
											$paymentStatus['md5sig'],
											$generatedSignatured
							);

							$this->getLogger(__METHOD__)->error('Skrill:isCredentialValid', $isCredentialValid);

							$state = $paymentHelper->mapTransactionState((string) $paymentStatus['status']);

							if ($isCredentialValid && $payment->status == $state)
							{
								$payment->status = $state;

								if ($state == Payment::STATUS_APPROVED)
								{
									$orderService->updateOrderStatus($orderId, 5);
								}
							}
						}
					}
				}
			}
		}
		else
		{
			throw new \Exception('Update order status Skrill payment failed! The given order does not have payment!');
		}
	}
}
