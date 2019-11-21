<?php

namespace Skrill\Methods;

use Plenty\Plugin\Log\Loggable;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;

/**
* Class NtlPaymentMethod
* @package Skrill\Methods
*/
class NtlPaymentMethod extends AbstractPaymentMethod
{
	use Loggable;

	const KEY = 'SKRILL_NTL';
	const DEFAULT_NAME = 'Neteller';
	const RETURN_TYPE = GetPaymentMethodContent::RETURN_TYPE_HTML;
	const INITIALIZE_PAYMENT = true;
    

	/**
	 * @var name
	 */
	protected $name = self::DEFAULT_NAME;

	/**
	 * @var exceptedBillingCountries
	 */
	protected $exceptedBillingCountries = array(
		'AFG','ARM','BTN','BVT','MMR','CHN','COD','COK','CUB','ERI','SGS','GUM','GIN','HMD','IRN','IRQ','CIV','KAZ',
		'PRK','KGZ','LBR','LBY','MNG','MNP','FSM','MHL','PLW','PAK','TLS','PRI','SLE','SOM','ZWE','SDN','SYR','TJK',
		'TKM','UGA','USA','VIR','UZB','YEM'
	);

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = 'ntl.png';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_ntl';
}
