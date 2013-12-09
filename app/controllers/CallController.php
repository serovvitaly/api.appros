<?php

class CallController extends BaseController {

    public function anyIndex()
    {
        $result = array(
            'success' => false
        );
        
        $method    = Input::get('method', NULL);
        
        if ($method !== NULL) {
            
            $providers = Input::get('providers', NULL);
            
            if (!empty($providers)) {
                $providers = explode(',', $providers);
            } else {
                $providers = NULL;
            }
            
            if (!is_array($providers) OR count($providers) < 1) {
                $providers = NULL;
            }
            
            if ($providers !== NULL) {
                
                $params = 'cities';
                
                $results = array();
                
                foreach ($providers AS $provider) {
                    $provider = trim($provider);
                    if (is_numeric($provider)) {
                        $provider = intval($provider);
                        
                        $pro = Provider::find($provider);
                        
                        if (method_exists($pro, $method)) {
                            $results[$provider] = $pro->$method($params);
                        }
                        
                        //print_r($results);
                        
                        $result['list'] = $results;
                    }
                }
                
            }
            
        }

        
        return Response::json($result);
    }

}