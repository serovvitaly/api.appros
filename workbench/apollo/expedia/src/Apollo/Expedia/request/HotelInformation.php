<?php namespace Apollo\Expedia\Request;

use Apollo\Expedia\ExpediaRequest;

class HotelInformation extends ExpediaRequest{
    
    protected $_allowed_fields = array('hotelId','arrivalDate','departureDate');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/info';
    
    public $data;    
    
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelInformationResponse)) {
            $hir = $res->HotelInformationResponse;
            
            
            $response = new \Apollo\Expedia\Response\HotelInformation;
            
            
            if (isset($hir->HotelSummary)) {
                $hs = $hir->HotelSummary;
                
                $response->hotelId     = $this->g('hotelId', $hs);
                $response->name        = html_entity_decode($this->g('name', $hs));
                $response->city        = $this->g('city', $hs);
                $response->address     = $this->g('address1', $hs);
                $response->postalCode  = $this->g('postalCode', $hs);
                $response->countryCode = $this->g('countryCode', $hs);
                $response->hotelRating = $this->g('hotelRating', $hs);
                $response->latitude    = $this->g('latitude', $hs);
                $response->longitude   = $this->g('longitude', $hs);
                
                $response->lowRate     = $this->g('lowRate', $hs);
                $response->highRate    = $this->g('highRate', $hs);
                
                $response->locationDescription = $this->g('locationDescription', $hs);
                
                //
                
            }
            
            if (isset($hir->HotelDetails)) {
                $hd = $hir->HotelDetails;
                
                $response->dt_amenitiesDescription         = $this->g('amenitiesDescription', $hd);
                $response->dt_areaInformation              = $this->g('areaInformation', $hd);
                $response->dt_businessAmenitiesDescription = $this->g('businessAmenitiesDescription', $hd);
                $response->dt_checkInInstructions          = $this->g('checkInInstructions', $hd);
                $response->dt_drivingDirections            = $this->g('drivingDirections', $hd);
                $response->dt_locationDescription          = $this->g('locationDescription', $hd);
                $response->dt_numberOfFloors               = $this->g('numberOfFloors', $hd);
                $response->dt_numberOfRooms                = $this->g('numberOfRooms', $hd);
                $response->dt_propertyDescription          = $this->g('propertyDescription', $hd);
                $response->dt_propertyInformation          = $this->g('propertyInformation', $hd);
                $response->dt_roomDetailDescription        = $this->g('roomDetailDescription', $hd);
                $response->dt_roomFeesDescription          = $this->g('roomFeesDescription', $hd);
                $response->dt_roomInformation              = $this->g('roomInformation', $hd);
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
                                $response->images[] = array(
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
                                    $response->amenities[] = array(
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
                                $response->roomtypes[] = array(
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
            
            
            $this->_response = $response;
            
        }
    }
    
}


