<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class IdlPaymentMethod
* @package Skrill\Methods
*/
class IdlPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_IDL';
	const DEFAULT_NAME = 'iDEAL';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('NLD');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'idl.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_idl';
}
