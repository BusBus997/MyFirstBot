<?php

namespace Base;

use Exception;

use Logic\ValidationData;
use Base\BaseModel;


class BaseController
{
    private $baseModel;
    private $tableName;

    public function __construct()
    {
        $this->baseModel = new BaseModel();
    }

    public function processMessages(): void
    {
        $messages = $this->baseModel->getUnprocessedMessages();

        while ($message = $messages->fetch_assoc()) {
            $id = intval($message['id']);
            $messageId = $message['message_id'];
            $chatId = $message['chat_id'];
            $messageText = $message['message'];
            $processed = intval($message['processed']);


            $requestData = $this->extractDataFromMessage($messageText);


            if ($requestData['type'] == 1 || $requestData['type'] == 2) {
                $valid = new ValidationData($id, $messageId, '', $requestData, '', $requestData['type'], $processed);
                $data = $valid->createRequest();
                $this->baseModel->insertRequest($data, $requestData['type']);
            } else {
                $valid = new ValidationData($id, $messageId, $chatId, [], $messageText, null, $processed);
                $data = $valid->createOutMessage();
                $this->baseModel->insertOutMessage($data);
            }
            $this->baseModel->markMessageProcessed($tableName, $id);
        }
    }




    public function processRequests(): void
    {
        $messages = $this->baseModel->getRequests();

        while ($message = $messages->fetch_assoc()) {
            $id = intval($message['id']);
            $messageId = $message['message_id'];
            $chatId = $message['chat_id'];
            $readyRequests = json_decode($message['ready_requests'], true);
            $processed = intval($message['processed']);
            // обработка данных будет переобперделяться в CashController  и YandexController

            $valid = new ValidationData($id, $messageId, $chatId, [], $ready_out_messages, null, $processed);
            $data = $valid->createOutMessage();

            $this->baseModel->insertOutMessage($data);

            $this->baseModel->markMessageProcessed('request',$id);
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


