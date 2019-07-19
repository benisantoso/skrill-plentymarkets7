<?php

namespace Skrill\Migrations;

use Skrill\Models\Database\Settings;
use Skrill\Services\Database\SettingsService;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
* Migration to create skrill configuration tables
*
* Class CreateSkrillTables_1_0_9
* @package Skrill\Migrations
*/
class CreateSkrillTables_1_0_9
{
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

		// Set default payment method name in all supported languages.
		$service = pluginApp(SettingsService::class);
		$service->setInitialSettings();
	}
}
