<?php namespace Apollo\Expedia\Response;

class RoomAvailability{
    
    /**
    * Общее количество найденных записей
    */
    public $total  = 0;
    
    
    /**
    * Смещение относительно начала списка
    */
    public $offset = 0;
    
    
    /**
    * Количество записей в текущей выдаче
    */
    public $limit  = 0;
    
    /**
    * Список найденных результатов
    */
    public $items  = array();
    
}