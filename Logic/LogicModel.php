<?php

namespace Logic;

use Base\BaseModel;
use Exception;
class LogicModel extends BaseModel
{


    public function __construct()
    {

        parent::__construct(null,"in_messages"); // Вызываем конструктор родительского класса для инициализации соединения с БД
    }


    public function insertOutMessage($data)
    {
        // Добавляем дополнительную логику или переопределяем поведение
        $data['ready_out_messages'] = 'Ваш запрос некорректен: ' . $data['ready_out_messages'];

        // Вызываем метод родительского класса для выполнения базовой логики вставки
        return parent::insertOutMessage($data);
    }
}
