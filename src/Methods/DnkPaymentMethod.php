<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class DnkPaymentMethod
* @package Skrill\Methods
*/
class DnkPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_DNK';
	const DEFAULT_NAME = 'Dankort by Visa';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('DNK');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'dnk.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_dnk';
}
