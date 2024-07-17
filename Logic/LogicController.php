<?php

namespace Logic;

use Base\BaseController;
use Base\BaseModel;
use Logic\LogicModel;
use Logic\ValidationData;

class Logic extends BaseController
{
    private $logicModel;

    public function __construct()
    {
        parent::__construct('in_messages', 'message', 'request', 'ready_requests');
        $this->logicModel = new LogicModel();
    }

    public function handleMessage($messageId, $chatId, $messageText, $requestData, $processed): void
    {
        if ($requestData['type'] == 1 || $requestData['type'] == 2) {
            $valid = new ValidationData($messageId, '', $chatId, $requestData, '', $requestData['type'], $processed);
            $data = $valid->createRequest();
            $this->logicModel->insertRequest($data, $requestData['type']);
        } else {
            $valid = new ValidationData($messageId, '', $chatId, [], $messageText, null, $processed);
            $data = $valid->createOutMessage();
            $this->logicModel->insertOutMessage($data);
        }

        $this->logicModel->markMessageProcessed($this->tableName, $messageId);
    }

public function extractDataFromMessage($messageText): array
    {

        $pattern1 = '/^(.*?)\s+(\S+\.(?:com|ru|net|org))$/i';
        $pattern2 = '/^(\d+(?:\.\d+)?)(?:\s*)?(\$|rub|грн|eur)$/i';
        $type = false;
        $result = array();
        if (preg_match($pattern1, $messageText, $matches)) {
            $query = $matches[1];
            $site = $matches[2];
            $type = 1; // Indicate type 1

            $result = [
                'query' => $query,
                'site' => $site,
            ];
        } elseif (preg_match($pattern2, $messageText, $matches)) {
            $anotherQuery = $matches[1];
            $type = 2; // Indicate type 2

            $result = [
                'amounts' => $anotherQuery,
            ];
        }
        return [
            'result' => $result,
            'type' => $type
        ];
    }
}




