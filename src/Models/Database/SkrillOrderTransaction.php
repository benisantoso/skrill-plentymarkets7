<?php

namespace Skrill\Models\Database;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Skrill\Constants\Plugin;

/**
 * Class skrillOrderTransaction
 *
 * @property int $id
 * @property string $order_id
 * @property string $transaction_id
 * @property string $country
 * @property string $mb_amount
 * @property string $amount
 * @property string $md5sig
 * @property string $merchant_id
 * @property string $platform
 * @property string $payment_type
 * @property string $mb_transaction_id
 * @property string $mb_currency
 * @property string $pay_from_email
 * @property string $sha2sig
 * @property string $pay_to_email
 * @property string $currency
 * @property string $customer_id
 * @property string $status
 * @property string $paymentKey
 * @property string $mopId
 * @property string $assignedAt
 * @property string $createdAt
 * @property string $updatedAt
 */
class SkrillOrderTransaction extends Model
{
    const FIELD_ID                  = 'id';
    const FIELD_ORDER_ID            = 'order_id';
    const FIELD_TRANSACTION_ID      = 'transaction_id';
    const FIELD_COUNTRY             = 'country';
    const FIELD_MB_AMOUNT           = 'mb_amount';
    const FIELD_AMOUNT              = 'amount';
    const FIELD_MD5SIG              = 'md5sig';
    const FIELD_MERCHANT_ID         = 'merchant_id';
    const FIELD_PLATFORM            = 'platform';
    const FIELD_PAYMENT_TYPE        = 'payment_type';
    const FIELD_MB_TRANSACTION_ID   = 'mb_transaction_id';
    const FIELD_MB_CURRENCY         = 'mb_currency';
    const FIELD_PAY_FROM_EMAIL      = 'pay_from_email';
    const FIELD_SHA2SIG             = 'sha2sig';
    const FIELD_PAY_TO_EMAIL        = 'pay_to_email';
    const FIELD_CURRENCY            = 'currency';
    const FIELD_CUSTOMER_ID         = 'customer_id';
    const FIELD_STATUS              = 'status';
    const FIELD_PAYMENT_KEY         = 'paymentKey';
    const FIELD_MOP_ID              = 'mopId';
    const FIELD_ASSIGNED_AT         = 'assignedAt';
    const FIELD_CREATED_AT          = 'createdAt';
    const FIELD_UPDATED_AT          = 'updatedAt';

    public $id                  = 0;
    public $order_id            = 0;
    public $transaction_id      = '';
    public $country             = '';
    public $mb_amount           = '';
    public $amount              = '';
    public $md5sig              = '';
    public $merchant_id         = '';
    public $platform            = '';
    public $payment_type        = '';
    public $mb_transaction_id   = '';
    public $mb_currency         = '';
    public $pay_from_email      = '';
    public $sha2sig             = '';
    public $pay_to_email        = '';
    public $currency            = '';
    public $customer_id         = '';
    public $status              = '';
    public $paymentKey          = '';
    public $mopId               = '';
    public $assignedAt          = '';
    public $createdAt           = '';
    public $updatedAt           = '';

    /**
     * @return string
     */
    public function getTableName():string
    {
        return Plugin::NAME . '::SkrillOrderTransaction';
    }
}