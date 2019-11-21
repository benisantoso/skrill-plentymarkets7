<?php
namespace Skrill\Services\Database;

use Skrill\Models\Database\SkrillBasketData;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Log\Loggable;

class SkrillBasketDataService extends DatabaseBaseService
{
    use Loggable;

    /**
     * @var DataBase
     */
    private $db;

    /**
     * SkrillBasketDataService constructor.
     *
     * @param DataBase $dataBase
     */
    public function __construct(DataBase $dataBase)
    {
        $this->db = $dataBase;
    }

    /**
     * CreateSkrillBasketData
     * @param int $basketId
     * @param array $basketData
     * @return boolean
     *
     */
    public function createSkrillBasketData(int $basketId, array $basketData): SkrillBasketData
    {
        $this->getLogger(__METHOD__)->error('Skrill:start create skrill basket data :', null);
        /** @var SkrillBasketData $skrillBasketDataRelation */
        $skrillBasketDataRelation = pluginApp(SkrillBasketData::class);

        $now = date('Y-m-d H:i:s');
        
        $skrillBasketDataRelation->basketId = $basketId;
        $skrillBasketDataRelation->typeId = $basketData['typeId'];
        $skrillBasketDataRelation->ownerId = $basketData['ownerId'];
        $skrillBasketDataRelation->plentyId = $basketData['plentyId'];
        $skrillBasketDataRelation->locationId = $basketData['locationId'];
        $skrillBasketDataRelation->statusId = $basketData['statusId'];
        $skrillBasketDataRelation->orderItems = json_encode($basketData['orderItems']);
        $skrillBasketDataRelation->properties = json_encode($basketData['properties']);
        $skrillBasketDataRelation->addressRelations = json_encode($basketData['addressRelations']);
        $skrillBasketDataRelation->relations = json_encode($basketData['relations']);
        $skrillBasketDataRelation->createdAt = $skrillBasketDataRelation->updatedAt = $now;

        $skrillBasketDataRelation = $this->db->save($skrillBasketDataRelation);
        return $skrillBasketDataRelation;
    }

    /**
     * updateSkrillBasketDataIdRelation
     * @param int $id
     * @param int $skrillBasketDataRelation
     * @return boolean
     *
     */
    public function updateSkrillBasketDataIdRelation($id, $basketId, $basketData): SkrillBasketData
    {
        $this->getLogger(__METHOD__)->error('Skrill:start update skrill basket data :', null);
        $skrillBasketDataRelation = pluginApp(SkrillBasketData::class);
        
        $now = date('Y-m-d H:i:s');
        $skrillBasketDataRelation->id = $id;
        $skrillBasketDataRelation->typeId = $basketData['typeId'];
        $skrillBasketDataRelation->ownerId = $basketData['ownerId'];
        $skrillBasketDataRelation->plentyId = $basketData['plentyId'];
        $skrillBasketDataRelation->locationId = $basketData['locationId'];
        $skrillBasketDataRelation->statusId = $basketData['statusId'];
        $skrillBasketDataRelation->orderItems = json_encode($basketData['orderItems']);
        $skrillBasketDataRelation->properties = json_encode($basketData['properties']);
        $skrillBasketDataRelation->addressRelations = json_encode($basketData['addressRelations']);
        $skrillBasketDataRelation->relations = json_encode($basketData['relations']);
        $skrillBasketDataRelation->updatedAt = $now;
        $skrillBasketDataRelation->basketId = $basketId;
        $skrillBasketDataRelation = $this->db->save($skrillBasketDataRelation);
        
        /**
         * @var SkrillBasketData $skrillBasketDataRelation
         */
        return $skrillBasketDataRelation;
    }

    /**
     * getSkrillBasketDataByBasketId
     * @param int $basketId
     * @return object
     *
     */
    public function getSkrillBasketDataByBasketId(int $basketId)
    {
        $result =  $this->db->query(SkrillBasketData::class)
            ->where(SkrillBasketData::FIELD_BASKET_ID, '=', $basketId)
            ->get();

        return $result[0];
    }

    /**
     * createOrUpdateRelation
     * @param int $basketId
     * @param array $basketData
     * @return boolean
     *
     */
    public function createOrUpdateBasketData(int $basketId = 0, array $basketData)
    {
        $skrillBasketData = $this->getSkrillBasketDataByBasketId($basketId);
        if (!$skrillBasketData instanceof SkrillBasketData) {
            $skrillBasketData = $this->createSkrillBasketData($basketId, $basketData);
        } else {
            $skrillBasketData = $this->updateSkrillBasketDataIdRelation($skrillBasketData->id, $basketId, $basketData);
        }
        return $skrillBasketData;
    }
}
