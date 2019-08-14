<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class ObtPaymentMethod
* @package Skrill\Methods
*/
class ObtPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_OBT';
	const DEFAULT_NAME = 'Rapid Transfer';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array(
		'AUT','BEL','BGR','DNK','ESP','EST','FIN','FRA','DEU','HUN',
		'ITA','LVA','NLD','NOR','POL','PRT','SWE','GBR','USA'
	);

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'obt.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_obt';
}
