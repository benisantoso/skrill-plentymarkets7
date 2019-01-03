<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;

/**
* Class PchPaymentMethod
* @package Skrill\Methods
*/
class PchPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	/**
	 * @var name
	 */
	protected $name = 'Paysafecash';

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('AUT','HRV','HUN','ITA','MLT','PRT','ROU','SVN','ESP');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'pch.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_pch';
}
