<?php namespace Apollo\Expedia\Request;

use Apollo\Expedia\ExpediaRequest;

class HotelList extends ExpediaRequest{
    
    protected $_allowed_fields = array('city','arrivalDate','departureDate','minStarRating','maxStarRating','numberOfBedRooms','paging','cacheKey','cacheLocation','sort','room1');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/list';
    
    public $data;
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelListResponse)) {
            $hlr = $res->HotelListResponse;
            if (isset($hlr->HotelList)) {
                $hl = $hlr->HotelList;
                if (isset($hl->HotelSummary) AND is_array($hl->HotelSummary) AND count($hl->HotelSummary) > 0) {
                    $items = array();
                    foreach ($hl->HotelSummary AS $hs) {
                        
                        $totalRate = 0;
                        
                        if (isset($hs->RoomRateDetailsList) AND isset($hs->RoomRateDetailsList->RoomRateDetails) AND isset($hs->RoomRateDetailsList->RoomRateDetails->RateInfos) AND isset($hs->RoomRateDetailsList->RoomRateDetails->RateInfos->RateInfo)) {
                            
                            $RateInfo = $hs->RoomRateDetailsList->RoomRateDetails->RateInfos->RateInfo;
                            if (isset($RateInfo->ChargeableRateInfo)) {
                                $ChargeableRateInfo = (array) $RateInfo->ChargeableRateInfo;
                                if (isset($ChargeableRateInfo['@total'])) {
                                    $totalRate = number_format($ChargeableRateInfo['@total'], 2);
                                }
                            }
                            
                        }
                        
                        $item = array(
                            'hotelId'             => $this->g('hotelId', $hs),
                            'name'                => $this->g('name', $hs),
                            'thumbNailUrl'        => str_replace('_t.jpg', '_b.jpg', $this->g('thumbNailUrl', $hs)),
                            'locationDescription' => html_entity_decode($this->g('locationDescription', $hs)),
                            //'shortDescription'    => html_entity_decode( str_replace('&lt;p&gt;&lt;b&gt;EAN Location 1 and 2&lt;/b&gt; &lt;br /&gt;', '', $this->g('shortDescription', $hs)) ),
                            'airportCode'         => $this->g('airportCode', $hs),
                            'airportName'         => 'Домодедово', // $this->g('airportCode', $hs),
                            'hotelRating'         => $this->g('hotelRating', $hs),
                            'supplierType'        => $this->g('supplierType', $hs),
                            'rateCurrencyCode'    => $this->g('rateCurrencyCode', $hs),
                            'lowRate'             => number_format($this->g('lowRate', $hs), 2),
                            'highRate'            => number_format($this->g('highRate', $hs), 2),
                            'totalRate'           => $totalRate,
                            'likes'               => 12,
                            'comments'            => 3,
                        );
                        
                        $items[] = $item;
                    }
                    
                    
                    $response = new \Apollo\Expedia\Response\HotelList;
                    
                    $response->total = count($items);
                    $response->items = $items;
                    
                    $this->_response = $response;
                    
                }
            }
            
        }
    }
    
}


