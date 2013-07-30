<?php namespace Apollo\Expedia\Request;

use Apollo\Expedia\ExpediaRequest;

class RoomAvailability extends ExpediaRequest{
    
    protected $_allowed_fields = array('hotelId','arrivalDate','departureDate','numberOfBedRooms','supplierType','rateKey','roomTypeCode','rateCode','includeDetails','includeRoomImages','includeHotelFeeBreakdown','options');
    
    protected $_request_url = '/ean-services/rs/hotel/v3/avail';
    
    public $data = array();
    public $rooms = array();
    
    protected function _handle_result()
    {
        $this->data = $this->_result;
        
        $res = $this->_result;
        
        if (isset($res->HotelRoomAvailabilityResponse)) {
            $hrar = $res->HotelRoomAvailabilityResponse;
            if (isset($hrar->EanWsError)) {
                // TODO: обработать ошибку
            }
            
            if (isset($hrar->HotelRoomResponse) AND is_array($hrar->HotelRoomResponse) AND count($hrar->HotelRoomResponse) > 0) {
                foreach ($hrar->HotelRoomResponse AS $room) {
                    
                    $BedTypes  = array();
                    $RateInfos = array();
                    
                    if (isset($room->RateInfos) AND isset($room->RateInfos->RateInfo)) {
                        if (is_array($room->RateInfos->RateInfo) AND count($room->RateInfos->RateInfo) > 0) {
                            foreach ($room->RateInfos->RateInfo AS $rate) {
                                //
                            }
                        }
                        elseif (is_object($room->RateInfos->RateInfo)) {
                            $rate = $room->RateInfos->RateInfo;
                            
                            $ChargeableRateInfo  = array();
                            
                            
                            if (isset($rate->ChargeableRateInfo)) {
                                $cri = (array) $rate->ChargeableRateInfo;
                                
                                $NightlyRatesPerRoom = array();
                                
                                $ChargeableRateInfo = array(
                                    'total'                  => $this->g('@total', $cri),
                                    'currencyCode'           => $this->g('@currencyCode', $cri),
                                    'commissionableUsdTotal' => $this->g('@commissionableUsdTotal', $cri),
                                    'averageRate'            => $this->g('@averageRate', $cri),
                                    'averageBaseRate'        => $this->g('@averageBaseRate', $cri),
                                    'maxNightlyRate'         => $this->g('@maxNightlyRate', $cri),
                                    'nightlyRateTotal'       => $this->g('@nightlyRateTotal', $cri),
                                    'surchargeTotal'         => $this->g('@surchargeTotal', $cri),
                                    'NightlyRatesPerRoom'    => $NightlyRatesPerRoom
                                );
                            }
                            
                            $RateInfos[] = array(
                                'taxRate'            => $rate->taxRate,
                                'rateType'           => $rate->rateType,
                                'nonRefundable'      => $rate->nonRefundable,
                                'guaranteeRequired'  => $rate->guaranteeRequired,
                                'depositRequired'    => $rate->depositRequired,
                                'currentAllotment'   => $rate->currentAllotment,
                                'ChargeableRateInfo' => $ChargeableRateInfo,
                            );
                        }
                    }
                    
                    $item = array(
                        'roomTypeDescription' => $this->g('roomTypeDescription', $room),
                        'roomTypeCode'        => $this->g('roomTypeCode', $room),
                        'rateDescription'     => $this->g('rateDescription', $room),
                        'smokingPreferences'  => $this->g('smokingPreferences', $room),
                        'supplierType'        => $this->g('supplierType', $room),
                        'quotedOccupancy'     => $this->g('quotedOccupancy', $room),
                        'minGuestAge'         => $this->g('minGuestAge', $room),
                        'rateCode'            => $this->g('rateCode', $room),
                        'BedTypes'            => $BedTypes,
                        'RateInfos'           => $RateInfos,
                    );
                    
                    $this->rooms[] = $item;
                }
            }
        }
    }
    
}


