<?php

namespace Skrill\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;
use Plenty\Plugin\Routing\ApiRouter;

/**
* Class SkrillRouteServiceProvider
* @package Skrill\Providers
*/
class SkrillRouteServiceProvider extends RouteServiceProvider
{
	/**
	 * mapping the router
	 *
	 * @param Router $router
	 * @param ApiRouter $apiRouter
	 */
	public function map(Router $router, ApiRouter $apiRouter)
	{
		// Skrill-Settings routes
		$apiRouter->version(
						['v1'],
						['middleware' => 'oauth'],
						function ($apiRouter) {
							$apiRouter->post('payment/skrill/settings/', ['uses' => 'Skrill\Controllers\SettingsController@saveSettings']);
							$apiRouter->get('payment/skrill/settings/{settingType}', ['uses' => 'Skrill\Controllers\SettingsController@loadSettings']);
							$apiRouter->get('payment/skrill/setting/{plentyId}/{settingType}', ['uses' => 'Skrill\Controllers\SettingsController@loadSetting']);
						}
		);

		// Routes for display Shop Client
		$router->get('skrill/settings/{settingType}', 'Skrill\Controllers\SettingsController@loadShopClient');

		// Routes for display Skrill Configuration
		$router->get('skrill/configuration/{clientId}/{settingType}', 'Skrill\Controllers\SettingsController@loadConfiguration');

		// Routes for save Skrill settings
		$router->post('skrill/settings/save', 'Skrill\Controllers\SettingsController@saveConfiguration');

		// Routes for Skrill status_url
		$router->post('payment/skrill/status', 'Skrill\Controllers\PaymentNotificationController@handleStatusUrl');

		// Routes for Skrill return_url
		$router->get('payment/skrill/return', 'Skrill\Controllers\PaymentController@handleReturnUrl');

		// Routes for Skrill refund_status_url
		$router->post(
						'payment/skrill/refundstatus',
						'Skrill\Controllers\PaymentNotificationController@handleRefundStatusUrl'
		);

		// Routes for Skrill payment widget
		$router->get('payment/skrill/pay/{sid}/{orderId}', 'Skrill\Controllers\PaymentController@displayPaymentWidget');
	}
}
