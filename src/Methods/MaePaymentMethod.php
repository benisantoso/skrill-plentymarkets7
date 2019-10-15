<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class MaePaymentMethod
* @package Skrill\Methods
*/
class MaePaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_MAE';
	const DEFAULT_NAME = 'Maestro';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('GBR','ESP','IRL','AUT');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'mae.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_mae';
}
