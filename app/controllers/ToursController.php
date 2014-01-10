<?php

class ToursController extends \ApiController {
    
    /**
     * 
     */
    public function getIndex()
    {        
        //return 'README.md';
    }
    
    
    public function anyList()
    {         
        $this->result = Offer::all()->toEmbedsArray();
    }
    
    
	/**
	 * 
	 */
	public function anyList2()
	{
        header('Content-Type: text/plain; charset=UTF-8');
        
        $providers = Input::get('providers', 'ALL');
        
        $login    = 'appros';
        $password = 'jTzzFb1PElzvxOwsNvGiE7s1Vxw=';
        
        $query = "http://api.mouzenidis-travel.com/search/tourml.asmx/GetValidTourList?Login={$login}&Password={$password}";
        
        $query_hash = md5($query);
        
        $tour_key = 'tour_' . $query_hash;
        
        $xml_content = Cache::get($tour_key, function() use ($tour_key, $login, $password) {
            
            $xml_content = file_get_contents("http://api.mouzenidis-travel.com/search/tourml.asmx/GetValidTourList?Login={$login}&Password={$password}");
            
            Cache::add($tour_key, $xml_content, 600);
            
            return $xml_content;
        });
        
        $xml = simplexml_load_string($xml_content);
        
        $references = $xml->references;
        $sources    = $xml->sources;
        
        // СТРАНЫ
        $countries = array();
        if ($references->countries) {
            foreach ($references->countries->country AS $country) {
                $attributes = $country->attributes();
                
                $fake = $attributes->fake;
                settype($fake, 'boolean');
                
                if ($fake !== true) {
                    $key = intval($attributes->key);
                    $countries[$key] = array(
                        'name'    => strval($attributes->name),
                        'nameLat' => strval($attributes->nameLat),
                        'code'    => strval($attributes->code),
                    );
                }
            }
        }
        
        // ТИПЫ ТУРОВ
        $tourTypes = array();
        if ($references->tourTypes) {
            foreach ($references->tourTypes->tourType AS $tourType) {
                $attributes = $tourType->attributes();
                
                $fake = $attributes->fake;
                settype($fake, 'boolean');
                
                if ($fake !== true) {
                    $key = intval($attributes->key);
                    $tourTypes[$key] = array(
                        'name'    => strval($attributes->name),
                        'nameLat' => strval($attributes->nameLat),
                    );
                }
            }
        }
        
        $items = array();
        foreach ($sources->source->packets->packet AS $item) {
            
            $tour = $item->packetHeader->tour->attributes();
            
            $spo  = $item->packetHeader->spo->attributes();
            
            $items[] = array(
                'tour' => array(
                    'key'         => intval($tour->key),
                    'name'        => trim(strval($tour->name)),
                    'nameLat'     => trim(strval($tour->nameLat)),
                    'tourTypeKey' => $tourTypes[intval($tour->tourTypeKey)]['name'],
                    'countryKey'  => $countries[intval($tour->countryKey)]['name'],
                ),
                'spo' => array(
                    'key'         => intval($spo->key),
                    'name'        => trim(strval($spo->name)),
                    'nameLat'     => trim(strval($spo->nameLat)),
                )
            );
        }
        
        print_r($references);
        
        return;
        
        $data = array(
            'success' => true,
            'total' => count($list),
            'list' => $list,
        );
        
        return Response::json($data);
	}

}