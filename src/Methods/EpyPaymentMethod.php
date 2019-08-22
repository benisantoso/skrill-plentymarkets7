<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class EpyPaymentMethod
* @package Skrill\Methods
*/
class EpyPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_EPY';
	const DEFAULT_NAME = 'ePay.bg';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('BGR');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'epy.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_epy';
}
