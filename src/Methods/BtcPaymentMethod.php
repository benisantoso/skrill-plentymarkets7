<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class BtcPaymentMethod
* @package Skrill\Methods
*/
class BtcPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_BTC';
	const DEFAULT_NAME = 'Bitcoin';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var exceptedBillingCountries
	 */
	protected $exceptedBillingCountries = array(
		'CUB','SDN','SYR','PRK','IRN','KGZ','BOL','ECU','BGD','CAN','USA','TUR'
	);

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'btc.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_btc';
}
