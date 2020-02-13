<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class PchPaymentMethod
* @package Skrill\Methods
*/
class PchPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_PCH';
	const DEFAULT_NAME = 'Paysafecash';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('AUT','BEL','CAN','CHE','CZE','DNK','FRA','GRC','HRV','HUN','IRL','ITA','LUX','MLT','NLD','POL','PRT','ROU','SVK','SVN','SWE','ESP', 'GBR');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'pch.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_pch';
}
