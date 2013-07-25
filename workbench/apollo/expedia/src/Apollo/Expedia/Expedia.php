<?php namespace Apollo\Expedia;

/**
 * 
 */
class Expedia {
    
    protected static $_instatce = NULL;
    
    protected $_filter = array();
    
    /**
    * Содержит результат
    */
    protected $_result = array();
     
    /**
    * Содержит дополнительные данные результата запроса
    */
    protected $_addition = array();
    
    /**
    * Содержит список ошибок
    */
    protected $_errors = array();
    
    /**
    * Содержит общие параметры API Expedia
    */
    protected $_params = array();
    
    
    public $net_result = NULL;  // TODO: тестовый вариант, потом удалить
    
    
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
    
    
    /**
    * Устанавливает параметры фильтрации
    * 
    * @param mixed $filter
    * @return Expedia
    */
    public static function filter(array $filter)
    {
        if (count($filter) > 0) {
            static::inst()->_filter = $filter;
        }
        
        return static::inst();
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
    * Возвращает результат поиска из кэша, не вошедший в первую выдачу - для пагинации
    * 
    */
    public static function cache()
    {
        $self = static::inst();
        
        $_result = $self->_query('http://api.ean.com/ean-services/rs/hotel/v3/list', $self->_filter);
        
        return $_result;
    }
    
    
    /**
    * Выполняет запрос подробной информации об отеле
    * 
    */
    public static function hotel_info($data)
    {
        if ($data['hotelId'] < 1) {
            return false;
        }
        
        $self = static::inst();
        
        $_result = $self->_query('http://api.ean.com/ean-services/rs/hotel/v3/info', $data);
        
        if ($_result AND isset($_result->HotelInformationResponse)) {
            
            $_result = $_result->HotelInformationResponse;
            
            $data['includeRoomImages'] = true;
            $_result = (object) array_merge((array) $_result, array('HotelRoomAvailabilityResponse' => static::rooms($data)));
            
            $self->_result = $_result;
            
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
    * Выполняет запрос к серверу API Expedia
    * 
    * @param mixed $url
    * @param mixed $fields
    * @param resource $curl
    * @param mixed $post
    * @return Expedia
    */
    protected function _query($url, array $fields, $curl = false, $post = false)
    {
        $url = trim($url, '?');
        
        $_post_fields = array_merge($this->_params, $fields);
        
        $_fields = array();
        foreach ($_post_fields AS $f_key => $f_value) {
            if (is_array($f_value)) {
                $f_value = implode(',', $f_value);
            }
            $_fields[] = $f_key . '=' . $f_value; 
        }
        $_fields = implode('&', $_fields);
        
        $_result = $this->_result;
        
        if ($curl) {
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL            => $url,
                CURLOPT_POST           => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS     => $_post_fields,
            ));
            
            $response = curl_exec($curl);
            
            
            $info = curl_getinfo($curl);
            
            if ($info['http_code'] != 200) {
                $this->_set_error('Response code - ' . $info['http_code'], 1000);
            }
            else {
                $_result = $response;
            }
            //print_r(curl_errno($curl));
            //print_r(curl_error($curl));
            
        } else {
            try{
                if ($post === true) {
                    $context = stream_context_create(array(
                        'http' => array(
                            'method' => 'POST',
                            'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                            'content' => $_fields,
                        ),
                    ));
                    
                    $_result = file_get_contents($url, false, $context);
                    
                } else {
                    $_result = file_get_contents($url . '?' . $_fields);
                }
                
            }
            catch (Exception $e) {
                $this->_set_error($e->message, $e->code);
            }
            
        }
        $this->net_result = $_result; // TODO: тестовый вариант, потом удалить
        
        return json_decode($_result);
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
