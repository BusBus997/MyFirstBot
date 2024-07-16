<?php

namespace Base;

use Connection\Database;
use Exception;


class BaseModel
{
    public $dbLink;
    protected $type;
    protected $tableName;

    public function __construct($type = null, $tableName)
    {
        $this->dbLink = Database::getInstance()->getConnection();
        $this->type = $type;
        $this-> tableName = $tableName;
    }

    public function getUnprocessedMessages()
    {
        $sql = "SELECT id, message_id, chat_id, message, processed FROM In_messages WHERE processed = 0";
        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function getRequests()
    {
        $sql = "SELECT r.id, r.message_id, r.ready_requests, r.processed, m.chat_id 
                FROM requests r
                LEFT JOIN in_messages m ON r.message_id = m.message_id
                WHERE r.type = ?";

        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->bind_param('i', $this->type);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        return $result;
    }

    public function insertRequest($data, $type)
    {
        $sql = "INSERT INTO requests (message_id, ready_requests, type, processed) VALUES (?, ?, ?, ?)";
        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->bind_param('ssii', $data['message_id'], $data['ready_requests'], $type, $data['processed']);

        $result = $stmt->execute();

        if (!$result) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    public function insertOutMessage($data)
    {
        $sql = "INSERT INTO Out_messages (message_id, chat_id, ready_out_messages, processed) VALUES (?, ?, ?, ?)";
        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception('Request preparation error: ' . $this->dbLink->error);
        }


        $stmt->bind_param('sssi', $data['message_id'], $data['chat_id'], $data['ready_out_messages'], $data['processed']);

        $result = $stmt->execute();

        if (!$result) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        return $result;
    }

    public function markMessageProcessed(string $tableName, int $id): void
    {
        $stmt = $this->dbLink->prepare("UPDATE $tableName SET processed = 1 WHERE id = ?");
        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
}



