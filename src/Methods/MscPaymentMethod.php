<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class MscPaymentMethod
* @package Skrill\Methods
*/
class MscPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_MSC';
	const DEFAULT_NAME = 'MasterCard';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'msc.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_msc';

	/**
     * Check whether the payment method is active
     *
     * @return bool
     */
	public function isActive()
	{
		if ($this->isAllCardActive() && !$this->isShowSeparately()) {
			return false;
		} elseif (
			$this->isShowSeparately()
			|| $this->isEnabled()
			&& $this->isBillingCountriesAllowed()
		) {
			return true;
		}
	}
}
