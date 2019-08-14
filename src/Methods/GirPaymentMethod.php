<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class GirPaymentMethod
* @package Skrill\Methods
*/
class GirPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_GIR';
	const DEFAULT_NAME = 'Giropay';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('DEU');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'gir.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_gir';
}
