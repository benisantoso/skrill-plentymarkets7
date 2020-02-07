<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AliPaymentMethod
* @package Skrill\Methods
*/
class AliPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_ALI';
	const DEFAULT_NAME = 'Alipay';
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
	protected $logoFileName = 'ali.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_ali';
}
