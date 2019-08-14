<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class PspPaymentMethod
* @package Skrill\Methods
*/
class PspPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_PSP';
	const DEFAULT_NAME = 'PostePay by Visa';
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
	protected $logoFileName = 'psp.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_psp';
}
