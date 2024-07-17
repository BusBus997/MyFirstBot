<?php

namespace Base;

use Exception;
use Logic\ValidationData;
use Base\BaseModel;

class BaseController
{
    protected $baseModel;
    protected $tableName;
    protected $kind;
    protected $insertTable;
    protected $typeOfData;

    public function __construct($tableName, $kind, $insertTable, $typeOfData)
    {
        $this->baseModel = new BaseModel(null, $tableName, $kind, $insertTable, $typeOfData);
        $this->tableName = $tableName;
        $this->kind = $kind;
        $this->insertTable = $insertTable;
        $this->typeOfData = $typeOfData;
    }

    public function process(): void
    {
        $messages = $this->baseModel->get($this->kind, $this->tableName);

        foreach ($messages as $message) {
            $messageId = $message['message_id'];
            $chatId = $message['chat_id'];
            $messageText = $message[$this->kind];
            $processed = intval($message['processed']);

            $requestData = $this->extractDataFromMessage($messageText);

            $this->handleMessage($messageId, $chatId, $messageText, $requestData, $processed);
        }
    }

    protected function extractDataFromMessage($messageText): array
    {
        return []; // Здесь нужно будет реализовать конкретную логику извлечения данных
    }

    protected function handleMessage($messageId, $chatId, $messageText, $requestData, $processed): void
    {
        // Этот метод должен быть переопределен в дочернем классе
    }
}
