<?php

namespace Skrill\Migrations;

use Skrill\Models\Database\Settings;
use Skrill\Models\Database\SkrillOrderTransaction;
use Skrill\Services\Database\SettingsService;
use Skrill\Helper\PaymentHelper;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
* Migration to create skrill configuration tables
*
* Class CreateSkrillTables_1_0_19
* @package Skrill\Migrations
*/
class CreateSkrillTables_1_0_19
{

	private $paymentHelper;

	public function __construct(
		PaymentHelper $paymentHelper
	) {
		$this->paymentHelper = $paymentHelper;
	}

	/**
	 * Run on plugin build
	 *
	 * Create skrill configuration tables.
	 *
	 * @param Migrate $migrate
	 */
	public function run(Migrate $migrate)
	{
		/**
		 * Create the settings table
		 */
		try {
			$migrate->deleteTable(Settings::class);
		}
		catch (\Exception $e)
		{
			//Table does not exist
		}
		$migrate->createTable(Settings::class);

		/**
		 * Create the settings table
		 */
		try {
			$migrate->deleteTable(SkrillOrderTransaction::class);
		}
		catch (\Exception $e)
		{
			//Table does not exist
		}
		$migrate->createTable(SkrillOrderTransaction::class);

		$this->paymentHelper->createMopsIfNotExist();

		// Set default payment method name in all supported languages.
		$service = pluginApp(SettingsService::class);
		$service->setInitialSettings();
	}
}
