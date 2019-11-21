<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class PliPaymentMethod
* @package Skrill\Methods
*/
class PliPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_PLI';
	const DEFAULT_NAME = 'POLi';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('AUS');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'pli.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_pli';
}
