<?php namespace Apollo\Expedia\Request;

use Apollo\Expedia\ExpediaRequest;

class HotelList extends ExpediaRequest{
    
    protected $_allowed_fields = array('city','arrivalDate','departureDate','minStarRating','maxStarRating','numberOfBedRooms','paging','cacheKey','cacheLocation','sort','room1');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/list';
    
    public $data;
    
    public $items = array();
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelListResponse)) {
            $hlr = $res->HotelListResponse;
            if (isset($hlr->HotelList)) {
                $hl = $hlr->HotelList;
                if (isset($hl->HotelSummary) AND is_array($hl->HotelSummary) AND count($hl->HotelSummary) > 0) {
                    foreach ($hl->HotelSummary AS $hs) {
                        $item = array(
                            'hotelId'             => $this->g('hotelId', $hs),
                            'name'                => $this->g('name', $hs),
                            'thumbNailUrl'        => $this->g('thumbNailUrl', $hs),
                            'locationDescription' => html_entity_decode($this->g('locationDescription', $hs)),
                            'shortDescription'    => html_entity_decode($this->g('shortDescription', $hs)),
                            'airportCode'         => $this->g('airportCode', $hs),
                            'supplierType'        => $this->g('supplierType', $hs),
                            'rateCurrencyCode'    => $this->g('rateCurrencyCode', $hs),
                            'lowRate'             => $this->g('lowRate', $hs),
                            'highRate'            => $this->g('highRate', $hs),
                        );
                        
                        $this->items[] = $item;
                    }
                }
            }
            
        }
    }
    
}


