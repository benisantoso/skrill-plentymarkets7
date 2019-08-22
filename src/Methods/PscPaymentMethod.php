<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class PscPaymentMethod
* @package Skrill\Methods
*/
class PscPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_PSC';
	const DEFAULT_NAME = 'Paysafecard';
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
		'ASM','AUT','BEL','CAN','HRV','CYP','CZE','DNK','FIN','FRA','DEU','GUM','HUN','IRL','ITA','LVA','LUX','MLT',
		'MEX','NLD','MNP','NOR','POL','PRT','PRI','ROU','SVK','SVN','ESP','SWE','CHE','TUR','GBR','USA','VIR'
	);

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'psc.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_psc';
}
