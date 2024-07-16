<?php

namespace Yandex;

use Base\BaseModel;
use Connection\Database;
use Exception;

class YandexModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct(1,'requests'); // Устанавливаем тип 2 для CashModel
    }
}
//