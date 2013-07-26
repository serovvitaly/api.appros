<?php namespace Apollo\Expedia;


use Illuminate\Support\Facades\Response;

/**
 * 
 */
class Expedia {
    
    protected static $_instatce = NULL;
    
    
    /**
    * Возвращает текущий экземпляр класса
    */
    public static function inst()
    {
        if (static::$_instatce == NULL) {
            static::$_instatce = new self;
        }
        
        return static::$_instatce;
    }
    
    
    public static function init(array $data)
    {
        static::set($data);
    }
    
    
    /**
    * Устанавливает один или не несколько параметров
    * 
    * @param mixed $name
    * @param mixed $value
    */
    public static function set($mix, $value = NULL)
    {
        if (is_array($mix)) {
            static::inst()->_params = array_merge(static::inst()->_params, $mix);
        } else {
            static::inst()->_params[$mix] = $value;
        }
        
        return static::inst();
    }
    
    
    public static function make($request, $data, $callback = null)
    {   
        $output['success'] = false;
        $output['errors']  = array();
        
        $request_class = "Apollo\\Expedia\\Exp\\{$request}";
        
        $response = new $request_class(static::inst()->_params);
        
        $response->sets($data);
        
        $response->execute();
        
        if ($response->check_errors()) {
            $output['errors']  = array_merge(static::inst()->_errors, $response->errors());
            $output['result']  = NULL;
        }
        else {
            $output['success'] = true;
            $output['result']  = $response;
        }
        
        //$output['errors']   = Expedia::errors();
        //$output['addition'] = Expedia::addition();
        //$output['data']     = json_decode(Expedia::inst()->net_result);
        
        if (empty($callback)) {
            $callback = $request;
        }
        
        $response = Response::make( $callback . '(' . json_encode($output) . ');');
        $response->header('Content-Type', 'text/javascript');
        
        return $response;
    }
    
    
    /**
    * Выполняет поисковый запрос
    * 
    */
    public static function search()
    {
        $self = static::inst();
        
        $_result = $self->_query('http://api.ean.com/ean-services/rs/hotel/v3/list', $self->_filter);
        
        if ($_result AND isset($_result->HotelListResponse)) {
            if (isset($_result->HotelListResponse->EanWsError)) {
                $self->_set_error($_result->HotelListResponse->EanWsError->verboseMessage);
            }
            elseif (isset($_result->HotelListResponse->HotelList)) {
                $hotellist = $_result->HotelListResponse->HotelList->HotelSummary;
                $hotellist_array = (array) $_result->HotelListResponse->HotelList;
                
                if (count($hotellist) > 0) {
                    $self->_result = $hotellist;
                    
                    // устанавливаем флаг, что отелей больше чем в результате запроса и нужно выводить пагинатор
                    if (isset($_result->HotelListResponse->moreResultsAvailable)) {
                        $self->_addition['moreResultsAvailable'] = $_result->HotelListResponse->moreResultsAvailable;
                    }
                    
                    // указываем общее количество найденных отелей
                    if (isset($hotellist_array['@activePropertyCount']) AND $hotellist_array['@activePropertyCount'] > 0) {
                        $self->_addition['activePropertyCount'] = (int) $hotellist_array['@activePropertyCount'];
                        $self->_addition['cacheKey']      = $_result->HotelListResponse->cacheKey;
                        $self->_addition['cacheLocation'] = $_result->HotelListResponse->cacheLocation;
                    }
                    
                } else {
                    $self->_set_error('По данному запросу ничего не найдено');
                }
            }
            else {
                $self->_set_error('Не удалось получить результат');
            }
        } else {
            $self->_set_error('Не удалось выполнить запрос');
        }
        
        return $self->_result;
    }
    
    
    
    

    
    
    /**
    * Выполняет запрос доступных комнатах для указанного отеля
    * 
    */
    public static function rooms($data)
    {
        if ($data['hotelId'] < 1) {
            return false;
        }
        
        $self = static::inst();
        
        $_result = $self->_query('http://api.ean.com/ean-services/rs/hotel/v3/avail', $data);
        
        if ($_result AND isset($_result->HotelRoomAvailabilityResponse)) {
            
            $self->_result = $_result->HotelRoomAvailabilityResponse;
            
        } else {
            $self->_set_error('Не удалось выполнить запрос');
        }
        
        return $self->_result;
    }
    
    
    /**
    * Выполняет запрос на бронирование комнаты
    * 
    * @param mixed $data
    */
    public static function book($data)
    {
        $self = static::inst();
        
        $_result = $self->_query('https://book.api.ean.com/ean-services/rs/hotel/v3/res', $data, false, true);
        
        if ($_result AND isset($_result->HotelRoomReservationResponse)) {
            
            if (isset($_result->HotelRoomReservationResponse->EanWsError)) {
                $self->_set_error($_result->HotelRoomReservationResponse->EanWsError->presentationMessage);
            }
            else {
                $self->_result = $_result->HotelRoomReservationResponse;
            }
            
        } else {
            $self->_set_error('Не удалось выполнить запрос');
        }
        
        return $self->_result;
    }
    
    
    /**
    * Возарвщвет результат поиска
    * 
    */
    public static function result()
    {
        return static::inst()->_result;
    }
    
    
    /**
    * Возвращает дополнительные данные результата запроса
    * 
    */
    public static function addition()
    {
        return static::inst()->_addition;
    }
    
    
    /**
    * Возвращает список ошибок
    * 
    */
    public static function errors()
    {
        return static::inst()->_errors;
    }
    
    

    
    
    /**
    * Устанавливает ошибку
    * 
    * @param mixed $message
    * @param mixed $code
    */
    protected function _set_error($message, $code = NULL)
    {
        static::inst()->_errors[] = array(
            'code'    => $code,
            'message' => $message
        );
        
        return static::inst(); 
    }

}
