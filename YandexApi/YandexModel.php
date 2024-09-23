<?php

namespace Yandex;

use Base\BaseModel;
use Connection\Database;
use Exception;

class YandexModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct(1, 'requests', 'ready_requests', 'responses', 'response' );
    }

    public function get(): array
    {
        $sql = "SELECT r.id, r.message_id, r.kind, r.processed, m.chat_id 
                FROM {$this->tableName} r
                LEFT JOIN in_messages m ON r.message_id = m.message_id
                WHERE r.type = ?";

        $stmt = $this->dbLink->prepare($sql);

        if (!$stmt) {
            throw new Exception($this->dbLink->error);
        }

        $stmt->bind_param('i', $this->type);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $data;
    }
}
