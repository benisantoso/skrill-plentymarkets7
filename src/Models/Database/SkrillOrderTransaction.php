<?php

namespace Skrill\Models\Database;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Skrill\Constants\Plugin;

/**
 * Class skrillOrderTransaction
 *
 * @property int $id
 * @property int $plentyId
 * @property int $order_id
 * @property string $transaction_id
 * @property string $responseStatus
 * @property string $assignedAt
 * @property string $createdAt
 * @property string $updatedAt
 */
class SkrillOrderTransaction extends Model
{
    const FIELD_ID                  = 'id';
    const FIELD_PLENTY_ID           = 'plentyId';
    const FIELD_ORDER_ID            = 'order_id';
    const FIELD_TRANSACTION_ID      = 'transaction_id';
    const FIELD_RESPONSE_STATUS     = 'responseStatus';
    const FIELD_ASSIGNED_AT         = 'assignedAt';
    const FIELD_CREATED_AT          = 'createdAt';
    const FIELD_UPDATED_AT          = 'updatedAt';

    /**
     * @var int
     */
    public $id                  = 0;
    
    /**
     * @var int
     */
    public $plentyId            = 0;

    /**
     * @var int
     */
    public $order_id            = 0;

    /**
     * @var string
     */
    public $transaction_id      = '';

    /**
     * @var string
     */
    public $responseStatus;

    /**
     * @var string
     */
    public $assignedAt          = '';

    /**
     * @var string
     */
    public $createdAt           = '';

    /**
     * @var string
     */
    public $updatedAt           = '';

    /**
     * @var array
     */
    protected $textFields = [
        'responseStatus'
    ];
    /**
     * @return string
     */
    public function getTableName():string
    {
        return Plugin::NAME . '::SkrillOrderTransaction';
    }
}