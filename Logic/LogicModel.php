<?php

namespace Logic;

use Base\BaseModel;
use Exception;
class LogicModel extends BaseModel
{


    public function __construct()
    {

        parent::__construct(null,"in_messages", 'message','requests', 'ready_requests'); // Вызываем конструктор родительского класса для инициализации соединения с БД
    }

/*
    public function insert($data)
    {
        // Добавляем дополнительную логику или переопределяем поведение
        $data['ready_out_messages'] = 'Ваш запрос некорректен: ' . $data['ready_out_messages'];

        // Вызываем метод родительского класса для выполнения базовой логики вставки
        return parent::insert($data);
    }
*/
}

