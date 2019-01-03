<?php

namespace Skrill\Methods;

use Plenty\Modules\Payment\Method\Contracts\PaymentMethodService;
use Plenty\Modules\Frontend\Contracts\Checkout;
use Plenty\Modules\Frontend\Session\Storage\Contracts\FrontendSessionStorageFactoryContract;
use Plenty\Plugin\Application;
use Plenty\Plugin\Log\Loggable;
use Skrill\Services\PaymentService;

/**
* Class AbstractPaymentMethod
* @package Skrill\Methods
*/
class AbstractPaymentMethod extends PaymentMethodService
{
	use Loggable;

	/**
	 * @var Checkout
	 */
	protected $checkout;

	/**
	 * @var PaymentService
	 */
	protected $paymentService;

	/**
	 * @var name
	 */
	protected $name = '';

	/**
	 * @var fee
	 */
	protected $fee = 0.00;

	/**
	 * @var allowedBillingCountries
	 */
	protected $allowedBillingCountries = array(
		'ALA','ALB','DZA','ASM','AND','AGO','AIA','ATA','ATG','ARG','ARM','ABW','AUS','AUT','AZE','BHS','BHR','BGD',
		'BRB','BLR','BEL','BLZ','BEN','BMU','BTN','BOL','BIH','BWA','BVT','BRA','BRN','BGR','BFA','BDI','KHM','CMR',
		'CAN','CPV','CYM','CAF','TCD','CHL','CHN','CXR','CCK','COL','COM','COG','COD','COK','CRI','CIV','HRV','CYP',
		'CZE','DNK','DJI','DMA','DOM','ECU','EGY','SLV','GNQ','ERI','EST','ETH','FLK','FRO','FJI','FIN','FRA','GUF',
		'PYF','ATF','GAB','GMB','GEO','DEU','GHA','GIB','GRC','GRL','GRD','GLP','GUM','GTM','GGY','HTI','HMD','VAT',
		'GIN','GNB','GUY','HND','HKG','HUN','ISL','IND','IDN','IRL','IMN','ISR','ITA','JAM','JPN','JEY','JOR','KAZ',
		'KEN','KIR','KOR','KWT','LAO','LVA','LBN','LSO','LBR','LIE','LTU','LUX','MAC','MKD','MDG','MWI','MYS','MDV',
		'MLI','MLT','MHL','MTQ','MRT','MUS','MYT','MEX','FSM','MDA','MCO','MNG','MNE','MSR','MAR','MOZ','MMR','NAM',
		'NPL','NLD','ANT','NCL','NZL','NIC','NER','NGA','NIU','NFK','MNP','NOR','OMN','PAK','PLW','PSE','PAN','PNG',
		'PRY','PER','PHL','PCN','POL','PRT','PRI','QAT','REU','ROU','RUS','RWA','SHN','KNA','LCA','MAF','SPM','VCT',
		'WSM','SMR','STP','SAU','SEN','SRB','SYC','SLE','SGP','SVK','SVN','SLB','SOM','ZAF','SGS','ESP','LKA','SUR',
		'SJM','SWZ','SWE','CHE','TWN','TJK','TZA','THA','TLS','TGO','TKL','TON','TTO','TUN','TUR','TKM','TCA','TUV',
		'UGA','UKR','ARE','GBR','USA','UMI','URY','UZB','VUT','VEN','VNM','VGB','VIR','WLF','ESH','YEM','ZMB','ZWE'
	);

	/**
	 * @var unallowedBillingCountries
	 */
	protected $unallowedBillingCountries = array(
		'AFG','CUB','ERI','IRN','IRQ','JPN','KGZ','LBY','PRK','SDN','SSD','SYR'
	);

	/**
	 * @var exceptedBillingCountries
	 */
	protected $exceptedBillingCountries = array();

	/**
	 * @var logoFileName
	 */
	protected $logoFileName = '';

	/**
	 * @var settingsType
	 */
	protected $settingsType = 'skrill_general';

	/**
	 * AbstractPaymentMethod constructor.
	 *
	 * @param Checkout $checkout
	 * @param PaymentService $paymentService
	 */
	public function __construct(Checkout $checkout, PaymentService $paymentService)
	{
		$this->checkout         = $checkout;
		$this->paymentService   = $paymentService;
		$this->paymentService->loadCurrentSettings($this->settingsType);
	}

	/**
	 * Check whether the payment setting is enabled
	 *
	 * @return bool
	 */
	protected function isEnabled()
	{
		if (array_key_exists('enabled', $this->paymentService->settings) && $this->paymentService->settings['enabled'] == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * Check whether the payment setting is show separately
	 *
	 * @return bool
	 */
	protected function isShowSeparately()
	{
		if (array_key_exists('showSeparately', $this->paymentService->settings) &&
			$this->paymentService->settings['showSeparately'] == 1)
		{
			return true;
		}
		return false;
	}

	/**
	 * get allowed billing countries
	 *
	 * @return array
	 */
	protected function getAllowedBillingCountries()
	{
		return $this->allowedBillingCountries;
	}

	/**
	 * get unallowed billing countries
	 *
	 * @return array
	 */
	protected function getUnallowedBillingCountries()
	{
		return $this->unallowedBillingCountries;
	}

	/**
	 * get excepted billing countries
	 *
	 * @return array
	 */
	protected function getExceptedBillingCountries()
	{
		return $this->exceptedBillingCountries;
	}

	/**
	 * get logo file name
	 *
	 * @return string
	 */
	protected function getLogoFileName()
	{
		return $this->logoFileName;
	}

	/**
	 * check whether billing countries is allowed
	 *
	 * @return array
	 */
	protected function isBillingCountriesAllowed()
	{
		$customerInvoiceAddressId = $this->checkout->getCustomerInvoiceAddressId();

		if (!empty($customerInvoiceAddressId))
		{
			$billingCountryCode = $this->paymentService->getBillingCountryCode($customerInvoiceAddressId);

			$unallowedBillingCountries = array_merge(
							$this->getUnallowedBillingCountries(),
							$this->getExceptedBillingCountries()
			);

			if (is_array($unallowedBillingCountries) && in_array($billingCountryCode, $unallowedBillingCountries))
			{
				return false;
			}

			$allowedBillingCountries = $this->getAllowedBillingCountries();

			if (is_array($allowedBillingCountries) && in_array($billingCountryCode, $allowedBillingCountries))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether the payment method is active
	 *
	 * @return bool
	 */
	public function isActive()
	{
		if ($this->isEnabled() && $this->isShowSeparately() && $this->isBillingCountriesAllowed())
		{
			return true;
		}
		return false;
	}

	/**
	 * Get the name of the payment method
	 *
	 * @return string
	 */
	public function getName()
	{
		$session = pluginApp(FrontendSessionStorageFactoryContract::class);
		$lang = $session->getLocaleSettings()->language;
		$name = '';
		if (array_key_exists('language', $this->paymentService->settings))
		{
			if (array_key_exists($lang, $this->paymentService->settings['language']))
			{
				if (array_key_exists('paymentName', $this->paymentService->settings['language'][$lang]))
				{
					$name = $this->paymentService->settings['language'][$lang]['paymentName'];
				}
			}
		}

		if (!strlen($name))
		{
			return $this->name;
		}
		return $name;
	}

	/**
	 * Get additional costs for Skrill.
	 * Skrill did not allow additional costs
	 *
	 * @return float
	 */
	public function getFee()
	{
		return $this->fee;
	}

	/**
	 * Get the path of the icon
	 *
	 * @return string
	 */
	public function getIcon()
	{
		$app = pluginApp(Application::class);
		$icon = $app->getUrlPath('skrill').'/images/logos/'.$this->getLogoFileName();
		return $icon;
	}

	/**
	 * Get the description of the payment method.
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return '';
	}

	/**
	 * Check if it is allowed to switch to this payment method
	 *
	 * @param int $orderId
	 * @return bool
	 */
	public function isSwitchableTo($orderId)
	{
		return false;
	}

	/**
	 * Check if it is allowed to switch from this payment method
	 *
	 * @param int $orderId
	 * @return bool
	 */
	public function isSwitchableFrom($orderId)
	{
		return true;
	}
}
