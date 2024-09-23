<?php
namespace Yandex;

use Base\BaseController;
use Base\BaseModel;
use Cash\CashModel;
use Connection\Database;
use Exception;
use Config;
use Logic\ValidationData;

class YandexController extends BaseController
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = YANDEX_TOKEN;
        $this->YandexModel = new YandexModel();
    }

    public function processRequests(): void
    {

        $messages = $this->YandexModel->getRequests();
        while ($message = $messages->fetch_assoc()) {

            $query = json_decode($message['ready_requests']['query'], true);
            $domain = json_decode($message['ready_requests']['site'], true);


            $responseQuery = $this->searchYandex($query);
            $responseDomain = $this->searchDomainPosition($domain);

            $valid = new ValidationData($id, $messageId, $chatId,[],'', $responseDomain,null, $processed);
            $data = $valid->createResponse();
            $this->YandexModel->insertResponse($data);


            $this->YandexModel->markMessageProcessed('requests',$id);





        }

    }

    function searchYandex($query, $apiKey, $folderId)
    {
        // Формирование URL для отправки запроса
        $url = 'https://yandex.ru/search/xml?folderid=' . $folderId . '&apikey=' . $apiKey . '&query=' . urlencode($query);

        // Инициализация cURL
        $ch = curl_init();

        // Настройка cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Отправка запроса и получение ответа
        $response = curl_exec($ch);

        // Проверка на ошибки cURL
        if (curl_errno($ch)) {
            echo 'Ошибка cURL: ' . curl_error($ch);

        }

        // Закрытие cURL
        curl_close($ch);

        // Возврат ответа
        return $response;


    }

    function searchDomainPosition($responseQuery, $domain)
    {
        $position = 1; //

        foreach ($responseQuery->response->results->grouping->group as $group) {
            foreach ($group->doc as $doc) {
                // Получаем ссылку из документа
                $urlParts = parse_url((string)$doc->url);
                $docDomain = isset($urlParts['host']) ? $urlParts['host'] : '';

                // Сравниваем домены
                if (stripos($docDomain, $domain) !== false) {
                    return [
                        'page' => ceil($position / 10), // Определяем страницу
                        'position' => $position % 10 ?: 10, // Определяем место на странице (если 0, то 10)
                        'url' => (string)$doc->url
                    ];
                }

                // Увеличиваем позицию
                $position++;
            }
        }
        return false;
    }

}
