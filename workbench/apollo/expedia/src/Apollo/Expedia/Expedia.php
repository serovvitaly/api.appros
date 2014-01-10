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
    
    
    public static function make($action, $data, $callback = null)
    {   
        $output['success'] = false;
        $output['errors']  = array();
        
        $request_class  = "Apollo\\Expedia\\Request\\{$action}";
        $response_class = "Apollo\\Expedia\\Response\\{$action}";
        
        $request = new $request_class(static::inst()->_params);
        
        $request->sets($data);
        
        $request->execute();
        
        if ($request->check_errors()) {
            $output['errors']  = array_merge(static::inst()->_errors, $request->errors());
            $output['result']  = NULL;
        }
        else {
            if ($request->response() instanceof $response_class) {
                $output['success'] = true;
                $output['result']  = $request->response();
                $output['data']    = json_decode($request->net_result);
            }
        }
        
        if (empty($callback)) {
            $callback = $action;
        }
        
        $request = Response::make( $callback . '(' . json_encode($output) . ');');
        $request->header('Content-Type', 'text/javascript');
        
        return $request;
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
