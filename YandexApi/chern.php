<?php


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

// Функция для поиска позиции домена в результатах
function findDomainPosition($data, $domain)
{
    $position = 1; // Начальная позиция

    foreach ($data->response->results->grouping->group as $group) {
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

// Пример использования функции
$apiKey = 'AQVNy2oko4uKzf5Vh-NP_MfDaqqOFTNyS3ocmFMt'; // Замените на ваш API ключ
$folderId = 'b1g5u4djk3ag7tl8q14m'; // Замените на идентификатор вашего каталога
$query = 'купить билет на самолет'; // Замените на ваш поисковый запрос

$result = searchYandex($query, $apiKey, $folderId);

$domainToFind = 'skyscanner.net'; // Замените на домен, который вы ищете

// Преобразуем XML-ответ в объект SimpleXML
$data = simplexml_load_string($result);

// Ищем позицию и URL искомого домена
$domainPosition = findDomainPosition($data, $domainToFind);

if ($domainPosition) {
    echo 'Домен ' . $domainToFind . ' найден на странице ' . $domainPosition['page'] . ', место ' . $domainPosition['position'] . ', URL: ' . $domainPosition['url'];
} else {
    echo 'Домен ' . $domainToFind . ' не найден в результатах поиска.';
}

