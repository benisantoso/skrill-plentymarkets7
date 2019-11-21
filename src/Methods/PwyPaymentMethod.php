<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class PwyPaymentMethod
* @package Skrill\Methods
*/
class PwyPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_PWY';
	const DEFAULT_NAME = 'Przelewy24';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('POL');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'pwy.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_pwy';
}
