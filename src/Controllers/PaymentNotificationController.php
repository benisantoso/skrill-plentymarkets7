<?php

namespace Skrill\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;
use Skrill\Helper\PaymentHelper;
use Skrill\Models\Repositories\SkrillOrderTransactionRepository;

/**
* Class PaymentNotificationController
* @package Skrill\Controllers
*/
class PaymentNotificationController extends Controller
{
	use Loggable;

	/**
	 *
	 * @var Request
	 */
	private $request;

	/**
	 *
	 * @var PaymentHelper
	 */
	private $paymentHelper;

	/**
	 *
	 * @var SkrillOrderTransactionRepository
	 */
	private $skrillOrderTransaction;

	/**
	 * PaymentNotificationController constructor.
	 *
	 * @param Request $request
	 * @param PaymentHelper $paymentHelper
	 */
	public function __construct(
		Request $request,
		PaymentHelper $paymentHelper,
		SkrillOrderTransactionRepository $skrillOrderTransaction
	) {
		$this->request = $request;
		$this->paymentHelper = $paymentHelper;
		$this->skrillOrderTransaction = $skrillOrderTransaction;
	}

	/**
	 * handle status_url from payment gateway
	 * @return string
	 */
	public function handleStatusUrl()
	{
		$this->getLogger(__METHOD__)->error('Skrill:status_url', $this->request->all());

		$paymentStatus = $this->request->all();
		$transactionId = $paymentStatus['transaction_id'];
		$mopId = $paymentStatus['mopId'];
		$skrillOrderTrx = $this->skrillOrderTransaction->getSkrillOrderTransactionByTransactionId($transactionId);
		$this->getLogger(__METHOD__)->error('Skrill:skrillOrderTrxOnStatusUrl', $skrillOrderTrx);

		if ($skrillOrderTrx) {
			$paymentStatus['orderId'] = $skrillOrderTrx->order_id;
			$this->paymentHelper->updatePlentyPayment($paymentStatus);
		}

		$this->skrillOrderTransaction->createOrUpdateRelation(0, $paymentStatus);

		return 'ok';
	}

	/**
	 * handle refund_status_url from refund payment gateway
	 * @return string
	 */
	public function handleRefundStatusUrl()
	{
		$this->getLogger(__METHOD__)->error('Skrill:refund_status_url', $this->request->all());

		$refundStatus = $this->request->all();
		$this->paymentHelper->updatePlentyRefundPayment($refundStatus);

		return 'ok';
	}
}
