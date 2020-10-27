<?php

namespace Skrill\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Log\Loggable;

use Skrill\Services\RestApiService;
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
	 * @var RestApiService
	 */
	private $restApiService;

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
	 * @param SkrillOrderTransactionRepository
	 * @param RestApiService
	 */
	public function __construct(
		Request $request,
		PaymentHelper $paymentHelper,
		SkrillOrderTransactionRepository $skrillOrderTransaction,
		RestApiService $restApiService
	) {
		$this->request = $request;
		$this->paymentHelper = $paymentHelper;
		$this->skrillOrderTransaction = $skrillOrderTransaction;
		$this->restApiService = $restApiService;
	}

	/**
	 * handle status_url from payment gateway
	 * @return string
	 */
	public function handleStatusUrl()
	{
		$paymentStatus = $this->request->all();
		$orderData = $this->restApiService->placeOrder($paymentStatus['basketId']);
		$orderId = $orderData->id;
		$plentyId = $orderData->plentyId;
		$transactionId = $paymentStatus['transaction_id'];

		$this->getLogger(__METHOD__)->error('Skrill:orderData', $orderData);

		$paymentStatus['orderId'] = $orderId;
		$this->paymentHelper->updatePlentyPayment($paymentStatus);
		
		$paymentStatus['plentyId'] = $plentyId;
		$this->skrillOrderTransaction->createOrUpdateRelation($orderId, $paymentStatus);
		
		$this->getLogger(__METHOD__)->error('Skrill:paymentStatus', $paymentStatus);

		$skrillOrderTrx = $this->skrillOrderTransaction->getSkrillOrderTransactionByTransactionId($transactionId);
		$this->getLogger(__METHOD__)->error('Skrill:skrillOrderTrx', $skrillOrderTrx);
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
