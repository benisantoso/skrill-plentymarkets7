<?php

namespace Skrill\Models\Database;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;
use Skrill\Constants\Plugin;

/**
 * Class skrillOrderTransaction
 *
 * @property int $id
 * @property string $basketId
 * @property string $typeId
 * @property string $ownerId
 * @property string $plentyId
 * @property string $locationId
 * @property string $statusId
 * @property string $orderItems
 * @property string $properties
 * @property string $addressRelations
 * @property string $relations
 * @property string $createdAt
 * @property string $updatedAt
 */
class SkrillBasketData extends Model
{
    const FIELD_ID                  = 'id';
    const FIELD_BASKET_ID           = 'basketId';
    const FIELD_TYPE_ID             = 'typeId';
    const FIELD_OWNER_ID            = 'ownerId';
    const FIELD_PLENTY_ID           = 'plentyId';
    const FIELD_LOCATION_ID         = 'locationId';
    const FIELD_STATUS_ID           = 'statusId';
    const FIELD_ORDER_ITEMS         = 'orderItems';
    const FIELD_PROPERTIES          = 'properties';
    const FIELD_ADDRESS_RELATIONS   = 'addressRelations';
    const FIELD_RELATIONS           = 'relations';
    const FIELD_CREATED_AT          = 'createdAt';
    const FIELD_UPDATED_AT          = 'updatedAt';

    /**
     * @var int
     */
    public $id                 = 0;

    /**
     * @var int
     */
    public $basketId           = 0;

    /**
     * @var int
     */
    public $typeId             = 1;

    /**
     * @var int
     */
    public $ownerId            = 0;

    /**
     * @var int
     */
    public $plentyId           = 0;

    /**
     * @var int
     */
    public $locationId         = 0;

    /**
     * @var string
     */
    public $statusId           = 0;

    /**
     * @var string
     */
    public $orderItems;

    /**
     * @var string
     */
    public $properties;

    /**
     * @var string
     */
    public $addressRelations;

    /**
     * @var string
     */
    public $relations;

    /**
     * @var string
     */
    public $createdAt          = '';

    /**
     * @var string
     */
    public $updatedAt          = '';

    /**
     * @var array
     */
    protected $textFields = [
        'orderItems',
        'properties',
        'addressRelations',
        'relations'
    ];

    /**
     * @return string
     */
    public function getTableName()
    {
        return Plugin::NAME . '::SkrillBasketData';
    }
}