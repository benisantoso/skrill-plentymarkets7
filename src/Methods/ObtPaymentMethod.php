<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;

/**
* Class ObtPaymentMethod
* @package Skrill\Methods
*/
class ObtPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	/**
	 * @var name
	 */
	protected $name = 'Rapid Transfer';

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array(
		'AUT','DNK','FIN','FRA','DEU','HUN','ITA','NOR','POL','PRT','ESP','SWE',
		'GBR','BEL','BGR','EST','LVA','NLD','USA'
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
