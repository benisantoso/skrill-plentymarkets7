<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class GcbPaymentMethod
* @package Skrill\Methods
*/
class GcbPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_GCB';
	const DEFAULT_NAME = 'Carte Bleue by Visa';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('FRA');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'gcb.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_gcb';
}
