<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AmxPaymentMethod
* @package Skrill\Methods
*/
class AmxPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_AMX';
	const DEFAULT_NAME = 'American Express';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var exceptedBillingCountries
	 */
	protected $exceptedBillingCountries = array('USA');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'amx.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_amx';

	/**
     * Check whether the payment method is active
     *
     * @return bool
     */
	public function isActive()
	{
		if ($this->isShowSeparately()
			&& $this->isEnabled()
			&& $this->isBillingCountriesAllowed()
		) {
			return true;
		}

		return false;
	}
}
