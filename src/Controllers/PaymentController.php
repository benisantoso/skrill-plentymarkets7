<?php

namespace Skrill\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Modules\Basket\Contracts\BasketItemRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;
use Skrill\Services\GatewayService;
use Skrill\Constants\SessionKeys;
use Skrill\Models\Repositories\SkrillOrderTransactionRepository;
use Skrill\Services\OrderService;
use Skrill\Helper\PaymentHelper;

/**
* Class PaymentController
* @package Skrill\Controllers
*/
class PaymentController extends Controller
{
	use Loggable;

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var Response
	 */
	private $response;

	/**
	 * @var BasketItemRepositoryContract
	 */
	private $basketItemRepository;

	/**
	 * @var OrderRepositoryContract
	 */
	private $orderRepository;

	/**
	 * @var SessionStorage
	 */
	private $sessionStorage;

	/**
	 *
	 * @var gatewayService
	 */
	private $gatewayService;

	/**
	 *
	 * @var SkrillOrderTransactionRepository
	 */
	private $skrillOrderTransaction;

	/**
	 *
	 * @var OrderService
	 */
	private $orderService;

	/**
	 *
	 * @var PaymentHelper
	 */
	private $paymentHelper;

	/**
	 * PaymentController constructor.
	 *
	 * @param Request $request
	 * @param Response $response
	 * @param BasketItemRepositoryContract $basketItemRepository
	 * @param SessionStorageService $sessionStorage
	 */
	public function __construct(
					Request $request,
					Response $response,
					BasketItemRepositoryContract $basketItemRepository,
					OrderRepositoryContract $orderRepository,
					FrontendSessionStorageFactoryContract $sessionStorage,
					GatewayService $gatewayService,
					SkrillOrderTransactionRepository $skrillOrderTransaction,
					OrderService $orderService,
					PaymentHelper $paymentHelper
	) {
		$this->request = $request;
		$this->response = $response;
		$this->basketItemRepository = $basketItemRepository;
		$this->orderRepository = $orderRepository;
		$this->sessionStorage = $sessionStorage;
		$this->gatewayService = $gatewayService;
		$this->skrillOrderTransaction = $skrillOrderTransaction;
		$this->orderService = $orderService;
		$this->paymentHelper = $paymentHelper;
	}

	/**
	 * handle return_url from payment gateway
	 */
	public function handleReturnUrl()
	{
		$this->getLogger(__METHOD__)->error('Skrill:return_url', $this->request->all());
		$this->sessionStorage->getPlugin()->setValue(SessionKeys::SESSION_KEY_TRANSACTION_ID, $this->request->get('transaction_id'));
		sleep(10);
		$skrillOrderTrx = $this->skrillOrderTransaction->getSkrillOrderTransactionByTransactionId($this->request->get('transaction_id'));
		$this->getLogger(__METHOD__)->error('Skrill:skrillOrderTrx', $skrillOrderTrx);

		$orderData = $this->orderService->placeOrder();
		$transactionId = $this->request->get('transaction_id');
		$mopId = $this->request->get('mopId');
		$orderId = $orderData->order->id;
		$this->resetBasket();

		if ($skrillOrderTrx->status) {
			$this->getLogger(__METHOD__)->error('Skrill:responseStatus', (array) $skrillOrderTrx);
			$paymentStatus = (array) $skrillOrderTrx;
		} else {
			$paymentStatus['transaction_id'] = $transactionId;
			$paymentStatus['currency'] = $orderData->order->amounts[0]->currency;
			$paymentStatus['amount'] = $orderData->order->amounts[0]->invoiceTotal;
			$paymentStatus['status'] = $this->paymentHelper->mapTransactionState(0);
		}

		$paymentStatus['orderId'] = $orderId;
		$this->getLogger(__METHOD__)->error('Skrill:paymentStatusOnReturnUrl', $paymentStatus);

		$this->paymentHelper->updatePlentyPayment($paymentStatus);
		$this->skrillOrderTransaction->createOrUpdateRelation($orderId, array('transaction_id' => $transactionId));

		return $this->response->redirectTo('execute-payment/'.$orderId);
	}

	/**
	 * for reset Basket order
	 *
	 */
	private function resetBasket() {
		$basketItems = $this->basketItemRepository->all();
		foreach ($basketItems as $basketItem)
		{
			$this->basketItemRepository->removeBasketItem($basketItem->id);
		}
	}
}
