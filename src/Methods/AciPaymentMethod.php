<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class AciPaymentMethod
* @package Skrill\Methods
*/
class AciPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_ACI';
    const DEFAULT_NAME = 'Cash / Invoice';
    const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
    const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array('ARG','BRA','CHL','CHN','COL','MEX','PER','URY');

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'aci.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_aci';

	/**
	 * Get the description of the payment method.
	 *
	 * @return string
	 */
	public function getDescription():string
	{
		switch ($this->getBillingCountryCode()) {
			case 'BRA':
				return 'Boleto';
				break;

			case 'ARG':
				return 'RedLink | Pago Facil';
				break;

			case 'CHL':
				return 'Servi Pag';
				break;

			case 'COL':
				return 'Davivienda | EDEQ | Carulla | Efecty | Éxito |  SurtiMax';
				break;

			case 'MEX':
				return 'Santander | Banamex | BBVA Bancomer | OXXO';
				break;

			case 'PER':
				return 'Banco de Occidente';
				break;
			
			default:
				return 'Redpagos';
				break;
		}
	}
}
