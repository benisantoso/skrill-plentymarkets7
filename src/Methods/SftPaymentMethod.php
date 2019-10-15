<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class SftPaymentMethod
* @package Skrill\Methods
*/
class SftPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_SFT';
	const DEFAULT_NAME = 'Klarna';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('DEU','AUT','BEL','NLD','ITA','FRA','POL','HUN','SVK','CZE','GBR');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'sft.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_sft';
}
