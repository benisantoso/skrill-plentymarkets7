<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AccPaymentMethod
* @package Skrill\Methods
*/
class AccPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

    const KEY = 'SKRILL_ACC';
    const DEFAULT_NAME = 'Credit Card';
    const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
    const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'acc.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_acc';
}
