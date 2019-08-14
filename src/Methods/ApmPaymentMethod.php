<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class ApmPaymentMethod
* @package Skrill\Methods
*/
class ApmPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_APM';
	const DEFAULT_NAME = 'All Credit Card and Alternative Payment Methods';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'apm.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_apm';

	/**
	 * Check whether the payment setting is show separately
	 *
	 * @return bool
	 */
	protected function isShowSeparately()
	{
		return true;
	}
}
