<?php

namespace Cash;

use Base\BaseModel;
use Connection\Database;
use Exception;

class CashModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct(2,'requests'); // Устанавливаем тип 2 для CashModel
    }
}