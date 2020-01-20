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
use Plenty\Modules\Authorization\Services\AuthHelper;
use Plenty\Modules\Order\Models\Order;
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
		$language = 'de';
		$this->getLogger(__METHOD__)->error('Skrill:return_url', $this->request->all());
		$this->sessionStorage->getPlugin()->setValue(SessionKeys::SESSION_KEY_TRANSACTION_ID, $this->request->get('transaction_id'));
		sleep(10);
		$skrillOrderTrx = $this->skrillOrderTransaction->getSkrillOrderTransactionByTransactionId($this->request->get('transaction_id'));
		$this->getLogger(__METHOD__)->error('Skrill:skrillOrderTrx', $skrillOrderTrx);

		$orderRepo = pluginApp(OrderRepositoryContract::class);
		$authHelper = pluginApp(AuthHelper::class);
		$orderId = $skrillOrderTrx->order_id;
		$order = $authHelper->processUnguarded(
						function () use ($orderRepo, $orderId) {
							return $orderRepo->findOrderById($orderId);
						}
		);

		if (!is_null($order) && $order instanceof Order)
		{
			$language = $order->properties[1]->value;
		}

		if ($orderId > 0) {
			$this->resetBasket();
		}

		return $this->response->redirectTo($language.'/execute-payment/'.$orderId);
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
