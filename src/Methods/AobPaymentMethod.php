<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AobPaymentMethod
* @package Skrill\Methods
*/
class AobPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_AOB';
	const DEFAULT_NAME = 'Manual Bank Transfer';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('BRA','CHL','COL');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'aob.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_aob';

	/**
	 * Get the description of the payment method.
	 *
	 * @return string
	 */
	public function getDescription():string
	{
		switch ($this->getBillingCountryCode()) {
			case 'BRA':
				return 'Santander | Caixa | HSBC';
				break;

			case 'CHL':
				return 'WebPay';
				break;
			
			default:
				return 'Bancolombia | PSEi';
				break;
		}
	}
}
