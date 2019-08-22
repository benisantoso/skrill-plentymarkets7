<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AdbPaymentMethod
* @package Skrill\Methods
*/
class AdbPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_ADB';
	const DEFAULT_NAME = 'Direct Bank Transfer';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('ARG','BRA');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'adb.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_adb';

	/**
	 * Get the description of the payment method.
	 *
	 * @return string
	 */
	public function getDescription():string
	{
		switch ($this->getBillingCountryCode()) {
			case 'BRA':
				return 'Banco Bradesco | Banco do Brasil | Banco Itau';
				break;
			
			default:
				return 'Banco Santander Rio';
				break;
		}
	}
}
