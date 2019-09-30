<?php
namespace Skrill\Models\Repositories;

use Skrill\Models\Database\SkrillOrderTransaction;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;

class SkrillOrderTransactionRepository
{
    /**
     * @var DataBase
     */
    private $database;

    /**
     * PaymentTxnIdRelationRepository constructor.
     *
     * @param DataBase $dataBase
     */
    public function __construct(DataBase $dataBase)
    {
        $this->database = $dataBase;
    }

    /**
     * CreateSkrillOrderTransaction
     * @param int $orderId
     * @param array $responseStatus
     * @return boolean
     *
     */
    public function createSkrillOrderTransaction(int $orderId, array $responseStatus): SkrillOrderTransaction
    {
        /** @var SkrillOrderTransaction $skrillOrderTransactionRelation */
        $skrillOrderTransactionRelation = pluginApp(SkrillOrderTransaction::class);

        $now = date('Y-m-d H:i:s');
        
        $skrillOrderTransactionRelation->order_id = $orderId;
        $skrillOrderTransactionRelation->plentyId = $responseStatus['plentyId'];
        $skrillOrderTransactionRelation->transaction_id = $responseStatus['transaction_id'];
        $skrillOrderTransactionRelation->responseStatus = json_encode($responseStatus);

        $skrillOrderTransactionRelation->assignedAt = $skrillOrderTransactionRelation->createdAt = $skrillOrderTransactionRelation->updatedAt = $now;

        $skrillOrderTransactionRelation = $this->database->save($skrillOrderTransactionRelation);
        return $skrillOrderTransactionRelation;
    }

    /**
     * updateSkrillOrderTransactionIdRelation
     * @param int $skrillOrderTransactionRelation
     * @return boolean
     *
     */
    public function updateSkrillOrderTransactionIdRelation($skrillOrderTransactionRelation): SkrillOrderTransaction
    {
        if ($skrillOrderTransactionRelation->id !== null) {
            $skrillOrderTransactionRelation = $this->database->save($skrillOrderTransactionRelation);
        }

        /**
         * @var SkrillOrderTransaction $skrillOrderTransactionRelation
         */
        return $skrillOrderTransactionRelation;
    }

    /**
     * getSkrillOrderTransactionByTransactionId
     * @param string $transactionId
     * @return object
     *
     */
    public function getSkrillOrderTransactionByTransactionId(string $transactionId)
    {
        $result =  $this->database->query(SkrillOrderTransaction::class)
            ->where(SkrillOrderTransaction::FIELD_TRANSACTION_ID, '=', $transactionId)
            ->get();

        return $result[0];
    }

    /**
     * createOrUpdateRelation
     * @param int $orderId
     * @param array $responseStatus
     * @return boolean
     *
     */
    public function createOrUpdateRelation(int $orderId = 0, array $responseStatus)
    {
        $skrillOrderTransactionRelation = $this->getSkrillOrderTransactionByTransactionId($responseStatus['transaction_id']);
        if (!$skrillOrderTransactionRelation instanceof SkrillOrderTransaction) {
            $skrillOrderTransactionRelation = $this->createSkrillOrderTransaction($orderId, $responseStatus);
        } else {
            $id = $skrillOrderTransactionRelation->id;
            if ($id <= 0) {
                $skrillOrderTransactionRelation->id = $id;
                $skrillOrderTransactionRelation->order_id = $orderId;
                $skrillOrderTransactionRelation->plentyId = $responseStatus['plentyId'];
                $skrillOrderTransactionRelation->transaction_id = $responseStatus['transaction_id'];
                $skrillOrderTransactionRelation->responseStatus = json_encode($responseStatus);
            }
            $skrillOrderTransactionRelation = $this->updateSkrillOrderTransactionIdRelation($skrillOrderTransactionRelation);
        }
        return $skrillOrderTransactionRelation;
    }
}
