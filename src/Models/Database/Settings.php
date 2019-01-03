<?php

namespace Skrill\Models\Database;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
* Class Settings
* @package Skrill\Models\Database
*
* @property int $id
* @property int $webstore
* @property string $name
* @property array $value
* @property string $createdAt
* @property string $updatedAt
*/
class Settings extends Model
{
	const AVAILABLE_LANGUAGES = array('de', 'en');

	const AVAILABLE_PAYMENT_METHODS = array(
		'skrill_pch' => array(
			'de' => array('paymentName' => 'Paysafecash'),
			'en' => array('paymentName' => 'Paysafecash'),
		),
		'skrill_apm' => array(
			'de' => array('paymentName' => 'Alle Karten und alternativen Zahlungsmethoden'),
			'en' => array('paymentName' => 'All Cards and Alternative Payment Methods'),
		),
		'skrill_wlt' => array(
			'de' => array('paymentName' => 'Skrill Wallet'),
			'en' => array('paymentName' => 'Skrill Wallet'),
		),
		'skrill_psc' => array(
			'de' => array('paymentName' => 'Paysafecard'),
			'en' => array('paymentName' => 'Paysafecard'),
		),
		'skrill_acc' => array(
			'de' => array('paymentName' => 'Kreditkarte'),
			'en' => array('paymentName' => 'Credit Cards'),
		),
		'skrill_vsa' => array(
			'de' => array('paymentName' => 'Visa'),
			'en' => array('paymentName' => 'Visa'),
		),
		'skrill_msc' => array(
			'de' => array('paymentName' => 'MasterCard'),
			'en' => array('paymentName' => 'MasterCard'),
		),
		'skrill_mae' => array(
			'de' => array('paymentName' => 'Maestro'),
			'en' => array('paymentName' => 'Maestro'),
		),
		'skrill_amx' => array(
			'de' => array('paymentName' => 'American Express'),
			'en' => array('paymentName' => 'American Express'),
		),
		'skrill_gcb' => array(
			'de' => array('paymentName' => 'Carte Bleue by Visa'),
			'en' => array('paymentName' => 'Carte Bleue by Visa'),
		),
		'skrill_dnk' => array(
			'de' => array('paymentName' => 'Dankort by Visa'),
			'en' => array('paymentName' => 'Dankort by Visa'),
		),
		'skrill_psp' => array(
			'de' => array('paymentName' => 'PostePay by Visa'),
			'en' => array('paymentName' => 'PostePay by Visa'),
		),
		'skrill_csi' => array(
			'de' => array('paymentName' => 'CartaSi by Visa'),
			'en' => array('paymentName' => 'CartaSi by Visa'),
		),
		'skrill_obt' => array(
			'de' => array('paymentName' => 'Rapid Transfer'),
			'en' => array('paymentName' => 'Rapid Transfer'),
		),
		'skrill_gir' => array(
			'de' => array('paymentName' => 'Giropay'),
			'en' => array('paymentName' => 'Giropay'),
		),
		'skrill_did' => array(
			'de' => array('paymentName' => 'Direct Debit / ELV'),
			'en' => array('paymentName' => 'Direct Debit / ELV'),
		),
		'skrill_sft' => array(
			'de' => array('paymentName' => 'Sofort'),
			'en' => array('paymentName' => 'Online Bank Transfer'),
		),
		'skrill_ebt' => array(
			'de' => array('paymentName' => 'Nordea Solo'),
			'en' => array('paymentName' => 'Nordea Solo'),
		),
		'skrill_idl' => array(
			'de' => array('paymentName' => 'iDEAL'),
			'en' => array('paymentName' => 'iDEAL'),
		),
		'skrill_npy' => array(
			'de' => array('paymentName' => 'EPS (Netpay)'),
			'en' => array('paymentName' => 'EPS (Netpay)'),
		),
		'skrill_pli' => array(
			'de' => array('paymentName' => 'POLi'),
			'en' => array('paymentName' => 'POLi'),
		),
		'skrill_pwy' => array(
			'de' => array('paymentName' => 'Przelewy24'),
			'en' => array('paymentName' => 'Przelewy24'),
		),
		'skrill_epy' => array(
			'de' => array('paymentName' => 'ePay.bg'),
			'en' => array('paymentName' => 'ePay.bg'),
		),
		'skrill_ali' => array(
			'de' => array('paymentName' => 'Alipay'),
			'en' => array('paymentName' => 'Alipay'),
		),
		'skrill_ntl' => array(
			'de' => array('paymentName' => 'Neteller'),
			'en' => array('paymentName' => 'Neteller'),
		),
		'skrill_aci' => array(
			'de' => array('paymentName' => 'Barzahlung / Rechnung'),
			'en' => array('paymentName' => 'Cash / Invoice'),
		),
		'skrill_adb' => array(
			'de' => array('paymentName' => 'Sofort Bank-Überweisung'),
			'en' => array('paymentName' => 'Direct Bank Transfer'),
		),
		'skrill_aob' => array(
			'de' => array('paymentName' => 'Manuelle Bank-Überweisung'),
			'en' => array('paymentName' => 'Manual Bank Transfer'),
		),
		'skrill_aup' => array(
			'de' => array('paymentName' => 'Unionpay'),
			'en' => array('paymentName' => 'Unionpay'),
		),
		'skrill_btc' => array(
			'de' => array('paymentName' => 'Bitcoin'),
			'en' => array('paymentName' => 'Bitcoin'),
		)
	);

	public $id = 0;
	public $webstore = 0;
	public $name = '';
	public $value = array();
	public $createdAt = '';
	public $updatedAt = '';

	/**
	 * get table name
	 *
	 * @return string
	 */
	public function getTableName()
	{
		return 'Skrill::settings';
	}
}