<?php

class Provider extends Eloquent {

    /**
    * Возвращает коллекцию статических данных.
    * 
    * @param mixed $name
    */
    public function collection($name)
    {
        $name = trim($name);
        $name = strtolower($name);
        
        return $this->_getCollection($name);
    }

    
    /**
    * Возвращает список коллекция статических данных.
    * 
    * @param mixed $list
    */
    public function collections($list)
    {
        if (!is_array($list)) {
            $list = explode(',', $list);
        }
        
        if (count($list) > 0) {
            
            $results = array();
            
            foreach ($list AS $name) {
                $name = trim($name);
                $name = strtolower($name);
                
                $results[$name] = $this->collection($name);
            }
            
            if (count($results) > 0) {
                return $results;
            }
        }
        
        return NULL;
    }
    
    
    /**
    * Возвращает содержимое коллекции из кэша, если кэш устарел или не существует,
    * то коллекция предварительно запрашивается у драйвера провайдера.
    * 
    * @param mixed $collectionName
    */
    protected function _getCollection($collectionName)
    {
        $self = $this;
        
        $cacheKey = 'collection_' . $collectionName . '_' . $this->id;
        
        $collection = Cache::get($cacheKey, function() use ($self, $cacheKey, $collectionName) {
            
            $driver = ProDriver::make($self->driver);
            
            if ($driver instanceof Appros\ProDriver\AbstractDriver) {
                
                $collection = $driver->getCollection($collectionName);
                
                Cache::add($cacheKey, $collection, $self->collection_update_period);
                
                return $collection;
            }
                        
        });
        
        return $collection;
    }

}