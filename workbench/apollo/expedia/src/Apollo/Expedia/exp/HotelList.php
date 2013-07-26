<?php namespace Apollo\Expedia\Exp;

use Apollo\Expedia\ExpediaRequest;

class HotelList extends ExpediaRequest{
    
    protected $_allowed_fields = array('city','arrivalDate','departureDate','minStarRating','maxStarRating','numberOfBedRooms','paging','cacheKey','cacheLocation','sort','room1');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/list';
    
    public $items = array();
    
    protected function _handle_result()
    {
        $res = $this->_result;
        
        if (isset($res->HotelListResponse)) {
            $hlr = $res->HotelListResponse;
            if (isset($hlr->HotelList)) {
                $hl = $hlr->HotelList;
                if (isset($hl->HotelSummary) AND is_array($hl->HotelSummary) AND count($hl->HotelSummary) > 0) {
                    foreach ($hl->HotelSummary AS $hs) {
                        $item = array(
                            'hotelId' => $hs->hotelId
                        );
                        
                        $this->items[] = $item;
                    }
                }
            }
            
        }
    }
    
}


