<?php

namespace Skrill\Services;

use IO\Models\LocalizedOrder;
use Plenty\Modules\Order\status\Contracts\OrderStatusRepositoryContract;
use Plenty\Modules\Order\Contracts\OrderRepositoryContract;
use Plenty\Modules\Order\Property\Models\OrderPropertyType;
use Plenty\Modules\Authorization\Services\AuthHelper;
use IO\Builder\Order\OrderBuilder;
use IO\Builder\Order\OrderType;
use IO\Builder\Order\OrderOptionSubType;
use IO\Builder\Order\AddressType;
use IO\Constants\SessionStorageKeys;
use IO\Services\BasketService;
use IO\Services\SessionStorageService;
use IO\Services\CheckoutService;
use IO\Services\CustomerService;
use Plenty\Plugin\Log\Loggable;

/**
* Class OrderService
* @package Skrill\Services
*/
class OrderService
{

	use Loggable;

	/**
	 * @var OrderRepositoryContract
	 */
	private $orderRepository;

	/**
	 * OrderService constructor.
	 * @param OrderRepositoryContract $orderRepository
	 */
	public function __construct(OrderRepositoryContract $orderRepository)
	{
		$this->orderRepository = $orderRepository;
	}

	/**
	 * Place an order
	 * @return LocalizedOrder
	 */
	public function placeOrder()
	{
		$basketService = pluginApp(BasketService::class);
		$sessionStorageService = pluginApp(SessionStorageService::class);
		$checkoutService = pluginApp(CheckoutService::class);
		$customerService = pluginApp(CustomerService::class);

		$couponCode = null;
		if (strlen($basketService->getBasket()->couponCode))
		{
			$couponCode = $basketService->getBasket()->couponCode;
		}

		$order = pluginApp(OrderBuilder::class)->prepare(OrderType::ORDER)
						->fromBasket()
						->withStatus(3)
						->withContactId($customerService->getContactId())
						->withAddressId($checkoutService->getBillingAddressId(), AddressType::BILLING)
						->withAddressId($checkoutService->getDeliveryAddressId(), AddressType::DELIVERY)
						->withOrderProperty(
										OrderPropertyType::PAYMENT_METHOD,
										OrderOptionSubType::MAIN_VALUE,
										$checkoutService->getMethodOfPaymentId()
						)
						->withOrderProperty(
										OrderPropertyType::SHIPPING_PROFILE,
										OrderOptionSubType::MAIN_VALUE,
										$checkoutService->getShippingProfileId()
						)
						->done();

		$order = $this->orderRepository->createOrder($order, $couponCode);

		if ($customerService->getContactId() <= 0)
		{
			$sessionStorageService->setSessionValue(SessionStorageKeys::LATEST_ORDER_ID, $order->id);
		}

		return LocalizedOrder::wrap($order, "de");
	}

	/**
	* Update order status
	* @param int $orderId
	* @param float $statusId
	* @return boolean true | false
	*/
	public function updateOrderStatus($orderId, $statusId) {
		$data = [
			'statusId' => $statusId
		];
		$orderRepository = $this->orderRepository;
		$authHelper = pluginApp(AuthHelper::class);

        $order = $authHelper->processUnguarded(
            function () use ($orderRepository, $data, $orderId) {
                return $orderRepository->updateOrder($data, $orderId);
            }
        );
		return $order;
	}

}
