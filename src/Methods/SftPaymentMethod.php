<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;

/**
* Class SftPaymentMethod
* @package Skrill\Methods
*/
class SftPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	/**
	 * @var name
	 */
	protected $name = 'Klarna';

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
