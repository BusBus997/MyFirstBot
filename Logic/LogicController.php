<?php

namespace Logic;

use Exception;
use Logic\LogicModel;
use Logic\ValidationData;
use Base\BaseModel;

class Logic
{
    private $logicModel;

    public function __construct()
    {
        $this->logicModel = new LogicModel();
    }

    public function processMessages(): void
    {
        $messages = $this->logicModel->getUnprocessedMessages();

        while ($message = $messages->fetch_assoc()) {
            $id = intval($message['id']);
            $messageId = $message['message_id'];
            $chatId = $message['chat_id'];
            $messageText = $message['message'];
            $processed = intval($message['processed']);



            $requestData = $this->extractDataFromMessage($messageText);



            if ( $requestData['type'] == 1 || $requestData['type'] == 2 ) {
                $valid = new ValidationData($id, $messageId, '', $requestData, '', $requestData['type'], $processed);
                $data = $valid->createRequest();
                $this->logicModel->insertRequest($data, $requestData['type']);
            } else {
                $valid = new ValidationData($id, $messageId, $chatId, [], $messageText,null, $processed);
                $data = $valid->createOutMessage();
                $this->logicModel->insertOutMessage($data);
            }

            $this->logicModel->markMessageProcessed('in_messages', $id);
        }
    }


    private function extractDataFromMessage(string $messageText): array
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




