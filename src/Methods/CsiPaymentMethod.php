<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class CsiPaymentMethod
* @package Skrill\Methods
*/
class CsiPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_CSI';
	const DEFAULT_NAME = 'CartaSi by Visa';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('ITA');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'csi.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_csi';
}
