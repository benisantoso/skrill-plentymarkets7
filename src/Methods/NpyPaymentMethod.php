<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class NpyPaymentMethod
* @package Skrill\Methods
*/
class NpyPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_NPY';
	const DEFAULT_NAME = 'EPS (Netpay)';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('AUT');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'npy.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_npy';
}
