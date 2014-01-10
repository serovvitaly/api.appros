<?php namespace Apollo\Expedia\Response;

class HotelInformation{
    
    public $hotelId = NULL;       // ID отеля в системе Expedia
    public $name;                 // название отеля
    public $city;                 // город
    public $address;              // адрес
    public $postalCode;           // почтовый индекс
    public $countryCode;          // код страны
    public $latitude;             // широта
    public $longitude;            // долгода
    public $hotelRating;          // рейтинг отеля
    public $lowRate;              // наименьшая цена
    public $highRate;             // наибольшая цена
    public $locationDescription;  // описание местоположения
    public $images    = array();  // картинки
    public $roomtypes = array();  // типы комнат
    public $amenities = array();  // опции отеля
    
    public $dt_amenitiesDescription;          // 
    public $dt_areaInformation;               // 
    public $dt_businessAmenitiesDescription;  // 
    public $dt_checkInInstructions;           // 
    public $dt_drivingDirections;             // 
    public $dt_locationDescription;           // 
    public $dt_numberOfFloors;                // 
    public $dt_numberOfRooms;                 // 
    public $dt_propertyDescription;           // 
    public $dt_propertyInformation;           // 
    public $dt_roomDetailDescription;         // 
    public $dt_roomFeesDescription;           // 
    public $dt_roomInformation;               // 
    
}


