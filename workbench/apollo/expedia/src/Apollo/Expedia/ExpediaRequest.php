<?php namespace Apollo\Expedia;


abstract class ExpediaRequest {
    
    /**
    * Содержит результат
    */
    protected $_result   = array();
    
    protected $_response = NULL;
     
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
    
    
    protected $_allowed_fields = array();
    
    protected $_allowed_params = array('cid', 'apiKey', 'locale', 'currencyCode', 'minorRev');
    
    protected $_data = array();
    
    protected $_host = 'http://api.ean.com';
    
    protected $_secure_host = 'https://book.api.ean.com';
    
    public $net_result = '';
    
    
    /**
    * 
    */
    public function __construct($params)
    {
        $this->sets($params, '_allowed_params');
    }
    
    
    /**
    * Устанавливает параметры запроса
    * 
    * @param mixed $data
    */
    public function sets(array $data, $allowed_fields_var = '_allowed_fields')
    {
        if (count($this->$allowed_fields_var) > 0) {
            foreach ($this->$allowed_fields_var AS $field) {
                if (isset($data[$field])) {
                    if (is_array($data[$field])) {
                        $data[$field] = implode(',', $data[$field]);
                    }
                    $this->_data[$field] = $data[$field];
                }
            }
        }
    }
    
    
    public function execute()
    {
        $_url = trim($this->_host, '/') . '/' . trim($this->_request_url, '/');
        
        $this->_result = $this->_query($_url, $this->_data);
        
        $this->_handle_result();
    }
    
    
    public function response()
    {
        return $this->_response;
    }
    
    
    /**
    * Проверяет наличие ошибок
    * 
    */
    public function check_errors()
    {
        if (is_array($this->_errors) AND count($this->_errors) > 0) {
            return true;
        }
        
        return false;
    }
    
    
    /**
    * Возвращает массив ошибок
    * 
    */
    public function errors()
    {
        return $this->_errors;
    }
    
    
    /**
    * Обрабатывает результат запроса
    * 
    */
    abstract protected function _handle_result();
    
    
    /**
    * Хелпер. Проверяет, есть ли в массиве (объекте) параметр и возвращает его, иначе возвращает дефултовое значение
    * 
    * @param mixed $name
    * @param mixed $mix
    * @param mixed $default
    */
    protected function g($name, $mix, $default = NULL)
    {
        if (is_array($mix) AND isset($mix[$name])) {
            return $mix[$name];
        }
        elseif (is_object($mix) AND isset($mix->$name)) {
            return $mix->$name;
        }
        
        return $default;
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
        $url = rtrim($url, '?');
        
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
                    
                    //echo $url . "?" . $_fields . '<hr>'; return;
                    
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
    
}