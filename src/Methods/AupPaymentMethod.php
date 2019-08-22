<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AupPaymentMethod
* @package Skrill\Methods
*/
class AupPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_AUP';
	const DEFAULT_NAME = 'Unionpay';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('CHN');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'aup.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_aup';
}
