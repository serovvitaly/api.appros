<?php namespace Apollo\Expedia\Exp;

use Apollo\Expedia\ExpediaRequest;

class HotelInfo extends ExpediaRequest{
    
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
    public $locationDescription;  // описание местоположения
    public $images = array();     // картинки
    public $amenities = array();  // опции отеля
    
    
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelInformationResponse)) {
            $hir = $res->HotelInformationResponse;
            
            if (isset($hir->HotelDetails)) {
                $hd = $hir->HotelDetails;
                //
            }
            if (isset($hir->HotelImages)) {
                $hi = $hir->HotelImages;
                //
            }
            if (isset($hir->HotelSummary)) {
                $hs = $hir->HotelSummary;
                
                $this->hotelId     = $this->g('hotelId', $hs);
                $this->name        = $this->g('name', $hs);
                $this->city        = $this->g('city', $hs);
                $this->address     = $this->g('address', $hs);
                $this->postalCode  = $this->g('postalCode', $hs);
                $this->countryCode = $this->g('countryCode', $hs);
                $this->latitude    = $this->g('latitude', $hs);
                $this->longitude   = $this->g('longitude', $hs);
                
                $this->locationDescription = $this->g('locationDescription', $hs);
                
                //
                
            }
            if (isset($hir->PropertyAmenities)) {
                $pa = $hir->PropertyAmenities;
                //
            }
            if (isset($hir->RoomTypes)) {
                $rt = $hir->RoomTypes;
                //
            }
            if (isset($hir->Suppliers)) {
                $ss = $hir->Suppliers;
                //
            }
        }
    }
    
}


