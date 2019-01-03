<?php

namespace Skrill\Migrations;

use Plenty\Modules\Payment\Method\Contracts\PaymentMethodRepositoryContract;
use Skrill\Helper\PaymentHelper;

/**
* Migration to create payment methods
*
* Class CreatePaymentMethod_1_0_9
* @package Skrill\Migrations
*/
class CreatePaymentMethod_1_0_9
{
	/**
	 * @var PaymentMethodRepositoryContract
	 */
	private $paymentMethodRepository;

	/**
	 * @var PaymentHelper
	 */
	private $paymentHelper;

	/**
	 * CreatePaymentMethod_1_0_9 constructor.
	 *
	 * @param PaymentMethodRepositoryContract $paymentMethodRepository
	 * @param PaymentHelper $paymentHelper
	 */
	public function __construct(PaymentMethodRepositoryContract $paymentMethodRepository, PaymentHelper $paymentHelper)
	{
		$this->paymentMethodRepository = $paymentMethodRepository;
		$this->paymentHelper = $paymentHelper;
	}

	/**
	 * Run on plugin build
	 *
	 * Create Method of Payment ID for Skrill if they don't exist
	 */
	public function run()
	{
		$this->createPaymentMethodByPaymentKey('SKRILL_PCH', 'Paysafecash');
		$this->createPaymentMethodByPaymentKey('SKRILL_APM', 'All Cards and Alternative Payment Methods');
		$this->createPaymentMethodByPaymentKey('SKRILL_WLT', 'Skrill Wallet');
		$this->createPaymentMethodByPaymentKey('SKRILL_PSC', 'Paysafecard');
		$this->createPaymentMethodByPaymentKey('SKRILL_ACC', 'Credit Cards');
		$this->createPaymentMethodByPaymentKey('SKRILL_VSA', 'Visa');
		$this->createPaymentMethodByPaymentKey('SKRILL_MSC', 'MasterCard');
		$this->createPaymentMethodByPaymentKey('SKRILL_MAE', 'Maestro');
		$this->createPaymentMethodByPaymentKey('SKRILL_AMX', 'American Express');
		$this->createPaymentMethodByPaymentKey('SKRILL_GCB', 'Carte Bleue by Visa');
		$this->createPaymentMethodByPaymentKey('SKRILL_DNK', 'Dankort by Visa');
		$this->createPaymentMethodByPaymentKey('SKRILL_PSP', 'PostePay by Visa');
		$this->createPaymentMethodByPaymentKey('SKRILL_CSI', 'CartaSi by Visa');
		$this->createPaymentMethodByPaymentKey('SKRILL_OBT', 'Rapid Transfer');
		$this->createPaymentMethodByPaymentKey('SKRILL_GIR', 'Giropay');
		$this->createPaymentMethodByPaymentKey('SKRILL_DID', 'Direct Debit / ELV');
		$this->createPaymentMethodByPaymentKey('SKRILL_SFT', 'Sofort');
		$this->createPaymentMethodByPaymentKey('SKRILL_EBT', 'Nordea Solo');
		$this->createPaymentMethodByPaymentKey('SKRILL_IDL', 'iDEAL');
		$this->createPaymentMethodByPaymentKey('SKRILL_NPY', 'EPS (Netpay)');
		$this->createPaymentMethodByPaymentKey('SKRILL_PLI', 'POLi');
		$this->createPaymentMethodByPaymentKey('SKRILL_PWY', 'Przelewy24');
		$this->createPaymentMethodByPaymentKey('SKRILL_EPY', 'ePay.bg');
		$this->createPaymentMethodByPaymentKey('SKRILL_ALI', 'Alipay');
		$this->createPaymentMethodByPaymentKey('SKRILL_NTL', 'Neteller');
		$this->createPaymentMethodByPaymentKey('SKRILL_ACI', 'Cash / Invoice');
		$this->createPaymentMethodByPaymentKey('SKRILL_ADB', 'Direct Bank Transfer');
		$this->createPaymentMethodByPaymentKey('SKRILL_AOB', 'Manual Bank Transfer');
		$this->createPaymentMethodByPaymentKey('SKRILL_AUP', 'Unionpay');
		$this->createPaymentMethodByPaymentKey('SKRILL_BTC', 'Bitcoin');
	}

	/**
	 * Create payment method with given parameters if it doesn't exist
	 *
	 * @param string $paymentKey
	 * @param string $name
	 */
	private function createPaymentMethodByPaymentKey($paymentKey, $name)
	{
		// Check whether the ID of the Skrill payment method has been created
		$paymentMethod = $this->paymentHelper->getPaymentMethodByPaymentKey($paymentKey);
		if (is_null($paymentMethod))
		{
			$this->paymentMethodRepository->createPaymentMethod(
							[
								'pluginKey' => 'skrill',
								'paymentKey' => (string) $paymentKey,
								'name' => $name
							]
			);
		}
	}
}
