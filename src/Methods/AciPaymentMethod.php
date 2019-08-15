<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;

/**
* Class AciPaymentMethod
* @package Skrill\Methods
*/
class AciPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	/**
	 * @var name
	 */
	protected $name = 'Cash / Invoice';

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
	public function getDescription()
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
