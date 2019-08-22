<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class DidPaymentMethod
* @package Skrill\Methods
*/
class DidPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_DID';
	const DEFAULT_NAME = 'Direct Debit / ELV';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('DEU');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'did.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_did';
}
