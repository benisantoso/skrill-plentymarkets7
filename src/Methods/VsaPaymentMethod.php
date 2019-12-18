<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class VsaPaymentMethod
* @package Skrill\Methods
*/
class VsaPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_VSA';
	const DEFAULT_NAME = 'Visa';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'vsa.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_vsa';

	/**
     * Check whether the payment method is active
     *
     * @return bool
     */
	public function isActive()
	{
		if (!$this->isMethodActive('skrill_acc')
			&& $this->isEnabled()
			&& $this->isBillingCountriesAllowed()
		) {
			return true;
		}

		return false;
	}
}
