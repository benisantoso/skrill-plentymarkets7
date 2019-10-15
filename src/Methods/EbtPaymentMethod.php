<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class EbtPaymentMethod
* @package Skrill\Methods
*/
class EbtPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_EBT';
	const DEFAULT_NAME = 'Nordea Solo';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('SWE');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'ebt.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_ebt';
}
