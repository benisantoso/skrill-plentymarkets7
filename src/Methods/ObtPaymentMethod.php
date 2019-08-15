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
