<?php namespace Appros\ProDriver\Drivers;

use Illuminate\Support\Facades\Cache;

class TourML extends \Appros\ProDriver\AbstractDriver {
    
    protected $login    = 'appros';
    protected $password = 'jTzzFb1PElzvxOwsNvGiE7s1Vxw=';
    
    public function getCollection($collectionName)
    {
        $collectionName = trim($collectionName);
        $collectionName = strtolower($collectionName);
        
        $xml_content = $this->GetValidTourList();
        
        $xml = simplexml_load_string($xml_content);
        
        $references = (array) $xml->references;
        
        if (isset($references[$collectionName])) {
            
            $reference = (array) $references[$collectionName];
            $reference = current($reference);
            
            $collection = array();
            if (count($reference) > 0) {
                foreach ($reference AS $item) {
                    $attributes = current($item->attributes());
                    $collection[] = $attributes;
                }
            }
            
            return $collection;            
        } else {
            return false;
        }
    }
    
    /**
    * Получение списка всех доступных в системе туров.
    * 
    */
    protected function GetValidTourList()
    {
        $url = "http://api.mouzenidis-travel.com/search/tourml.asmx/GetValidTourList?Login={$this->login}&Password={$this->password}";
        
        $cacheKey = md5($url);
        
        $xml_content = Cache::get($cacheKey, function() use ($cacheKey, $url) {
            
            $xml_content = file_get_contents($url);
            
            Cache::add($cacheKey, $xml_content, 60);
            
            return $xml_content;
        });
        
        return $xml_content;
    }
    
}