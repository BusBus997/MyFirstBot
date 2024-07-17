<?php

namespace Base;

use Connection\Database;
use Exception;


class BaseModel
{
    public $dbLink;
    public $type;
    public $tableName;
    public $kind;
    public $insertTable;
    public $typeOfData;


    public function __construct($type = null, $tableName, $kind, $insertTable, $typeOfData)
    {
        $this->dbLink = Database::getInstance()->getConnection();
        $this->type = $type;
        $this-> tableName = $tableName;
        $this-> kind = $kind;
        $this-> insertTable = $insertTable;
        $this->typeOfData = $typeOfData;
    }

    public function get($kind, $tableName): array
    {
        $sql = "SELECT  message_id, chat_id, $kind, processed FROM $tableName WHERE processed = 0";
        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }




    public function insert($data, $insertTable , $typeOfData , $type)
    {
        $sql = "INSERT INTO $insertTable (message_id, $typeOfData, type, processed) VALUES (?, ?, ?, ?)";
        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->bind_param('ssii', $data['message_id'], $data[$typeOfData], $type, $data['processed']);

        $result = $stmt->execute();

        if (!$result) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        return $result;
    }



    public function markMessageProcessed(string $tableName, string $message_id): void
    {
        $stmt = $this->dbLink->prepare("UPDATE $tableName SET processed = 1 WHERE message_id = ?");
        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }
        $stmt->bind_param('s', $message_id);
        $stmt->execute();
        $stmt->close();
    }
}



