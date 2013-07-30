<?php namespace Apollo\Expedia\Request;

use Apollo\Expedia\ExpediaRequest;

class HotelInformation extends ExpediaRequest{
    
    protected $_allowed_fields = array('hotelId','arrivalDate','departureDate');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/info';
    
    public $data;
    
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
    public $images = array();     // картинки
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
    
    
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelInformationResponse)) {
            $hir = $res->HotelInformationResponse;
            
            if (isset($hir->HotelDetails)) {
                $hd = $hir->HotelDetails;
                
                $this->dt_amenitiesDescription         = $this->g('amenitiesDescription', $hd);
                $this->dt_areaInformation              = $this->g('areaInformation', $hd);
                $this->dt_businessAmenitiesDescription = $this->g('businessAmenitiesDescription', $hd);
                $this->dt_checkInInstructions          = $this->g('checkInInstructions', $hd);
                $this->dt_drivingDirections            = $this->g('drivingDirections', $hd);
                $this->dt_locationDescription          = $this->g('locationDescription', $hd);
                $this->dt_numberOfFloors               = $this->g('numberOfFloors', $hd);
                $this->dt_numberOfRooms                = $this->g('numberOfRooms', $hd);
                $this->dt_propertyDescription          = $this->g('propertyDescription', $hd);
                $this->dt_propertyInformation          = $this->g('propertyInformation', $hd);
                $this->dt_roomDetailDescription        = $this->g('roomDetailDescription', $hd);
                $this->dt_roomFeesDescription          = $this->g('roomFeesDescription', $hd);
                $this->dt_roomInformation              = $this->g('roomInformation', $hd);
            }
            if (isset($hir->HotelImages)) {
                $hi = (array) $hir->HotelImages;
                if (isset($hi['@size']) AND (int) $hi['@size'] > 0) {
                    if ($hi['@size'] == 1) {
                        //
                    }
                    else {
                        if (isset($hi['HotelImage']) AND is_array($hi['HotelImage']) AND count($hi['HotelImage']) > 0) {
                            foreach ($hi['HotelImage'] AS $image) {
                                $this->images[] = array(
                                    //'hotelImageId' => $this->g('hotelImageId', $image),
                                    //'caption'      => $this->g('caption', $image),
                                    //'category'     => $this->g('category', $image),
                                    //'height'       => $this->g('height', $image),
                                    //'width'        => $this->g('width', $image),
                                    'thumbnailUrl' => $this->g('thumbnailUrl', $image),
                                    'url'          => $this->g('url', $image),
                                );
                            }
                        }
                    }
                }
            }
            if (isset($hir->HotelSummary)) {
                $hs = $hir->HotelSummary;
                
                $this->hotelId     = $this->g('hotelId', $hs);
                $this->name        = html_entity_decode($this->g('name', $hs));
                $this->city        = $this->g('city', $hs);
                $this->address     = $this->g('address1', $hs);
                $this->postalCode  = $this->g('postalCode', $hs);
                $this->countryCode = $this->g('countryCode', $hs);
                $this->hotelRating = $this->g('hotelRating', $hs);
                $this->latitude    = $this->g('latitude', $hs);
                $this->longitude   = $this->g('longitude', $hs);
                
                $this->lowRate     = $this->g('lowRate', $hs);
                $this->highRate    = $this->g('highRate', $hs);
                
                $this->locationDescription = $this->g('locationDescription', $hs);
                
                //
                
            }
            if (isset($hir->PropertyAmenities)) {
                $pa = (array) $hir->PropertyAmenities;
                if (isset($pa['@size']) AND (int) $pa['@size'] > 0) {
                    if ($pa['@size'] == 1) {
                        //
                    }
                    else {
                        if (isset($pa['PropertyAmenity']) AND is_array($pa['PropertyAmenity']) AND count($pa['PropertyAmenity']) > 0) {
                            foreach ($pa['PropertyAmenity'] AS $property) {
                                $amenity   = $this->g('amenity', $property);
                                $amenityId = $this->g('amenityId', $property);
                                if (!empty($amenity)) {
                                    $this->amenities[] = array(
                                        'amenity'   => $amenity,
                                        'amenityId' => $amenityId,
                                    );
                                }
                            }
                        }
                    }
                }
            }
            if (isset($hir->RoomTypes)) {
                $rt = (array) $hir->RoomTypes;
                if (isset($rt['@size']) AND (int) $rt['@size'] > 0) {
                    if ($rt['@size'] == 1) {
                        //
                    }
                    else {
                        if (isset($rt['RoomType']) AND is_array($rt['RoomType']) AND count($rt['RoomType']) > 0) {
                            foreach ($rt['RoomType'] AS $roomtype) {
                                $this->roomtypes[] = array(
                                    'description'     => $this->g('description', $roomtype),
                                    'descriptionLong' => $this->g('descriptionLong', $roomtype),
                                   // TODO: 'roomAmenities' => roomAmenities - доработать
                                );
                            }
                        }
                    }
                }
            }
            if (isset($hir->Suppliers)) {
                $ss = $hir->Suppliers;
                //
            }
        }
    }
    
}


