<?php

namespace Skrill\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Http\Request;
use Plenty\Plugin\Http\Response;
use Plenty\Modules\Basket\Contracts\BasketItemRepositoryContract;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\Templates\Twig;
use Skrill\Services\GatewayService;

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
	 * @var SessionStorage
	 */
	private $sessionStorage;

	/**
	 *
	 * @var gatewayService
	 */
	private $gatewayService;

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
					FrontendSessionStorageFactoryContract $sessionStorage,
					GatewayService $gatewayService
	) {
		$this->request = $request;
		$this->response = $response;
		$this->basketItemRepository = $basketItemRepository;
		$this->sessionStorage = $sessionStorage;
		$this->gatewayService = $gatewayService;
	}

	/**
	 * handle return_url from payment gateway
	 */
	public function handleReturn()
	{
		$this->getLogger(__METHOD__)->error('Skrill:return_url', $this->request->all());
		$this->sessionStorage->getPlugin()->setValue('skrillTransactionId', $this->request->get('transaction_id'));

		$orderId = $this->request->get('orderId');

		$basketItems = $this->basketItemRepository->all();
		foreach ($basketItems as $basketItem)
		{
			$this->basketItemRepository->removeBasketItem($basketItem->id);
		}

		return $this->response->redirectTo('execute-payment/'.$orderId);
	}

	/**
	 * show payment widget
	 */
	public function handlePayment(Twig $twig, $sid)
	{
		$paymentPageUrl = $this->gatewayService->getPaymentPageUrl($sid);
		$this->getLogger(__METHOD__)->error('Skrill:paymentPageUrl', $paymentPageUrl);

		return $twig->render('Skrill::Payment.PaymentWidget', ['paymentPageUrl' => $paymentPageUrl]);
	}
}
