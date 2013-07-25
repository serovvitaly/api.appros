<?php

class ExpediaController extends Controller {
    
    
    public function __construct()
    {        
        Expedia::set('cid', '55505')
               ->set('apiKey', '9j5yzzmywf936na49yjc66t4')
               ->set('locale', 'ru_RU')
               ->set('currencyCode', 'RUB')
               ->set('minorRev', 25)
               ->set('currencyCode', 'USD');
    }

    public function getIndex()
    {      
        return View::make('expedia.index');
    }
    
    
    /**
    * Выполняет поиск отелей
    * 
    */
    public function getSearch()
    {                  
        Expedia::set('numberOfResults', 20);
        
        $filter = Input::all();
        
        $filter['sort']   = 'PRICE';
        $filter['room1']  = '2';
        
        $callback = $filter['callback'];
        unset($filter['callback']);
        
        
        if (isset($filter['cacheKey']) AND !empty($filter['cacheKey']) AND isset($filter['cacheLocation']) AND !empty($filter['cacheLocation'])) {
            $filter = array(
                'cacheKey'      => $filter['cacheKey'],
                'cacheLocation' => $filter['cacheLocation']
            );
        }
        
        Expedia::filter($filter);
        
        
        $output['success']  = true;
        $output['result']   = Expedia::search();
        $output['errors']   = Expedia::errors();
        $output['addition'] = Expedia::addition();
        $output['data']     = json_decode(Expedia::inst()->net_result);
        $output['result2']   = Expedia::result();
        
        $res = json_decode(Expedia::inst()->net_result);
        
        $HotelListResponse = isset($res->HotelListResponse) ? $res->HotelListResponse : NULL;
        
        if ($HotelListResponse !== NULL) {
            $HotelList = isset($HotelListResponse->HotelList) ? $HotelListResponse->HotelList : NULL;
            
            if ($HotelList !== NULL) {
                $HotelSummary = isset($HotelList->HotelSummary) ? $HotelList->HotelSummary : NULL;
            }
        }
        
        $response = Response::make( $callback . '(' . json_encode($output) . ');');
        $response->header('Content-Type', 'text/javascript');
        
        return $response;
    }
    
    
    /**
    * Возвращает подробную информацию об отеле
    * 
    */
    public function action_hotel_info()
    {
        $data = Input::all();
        
        $output['success']  = true;
        $output['result']   = Expedia::hotel_info($data);
        $output['errors']   = Expedia::errors();
        $output['addition'] = Expedia::addition();
        $output['data']     = json_decode(Expedia::inst()->net_result);
        
        return json_encode($output);
    }
    
    
    /**
    * Возвращает данные о доступных комнатах для указанного отеля
    * 
    */
    public function action_rooms()
    {
        $data = Input::all();
        
        $data['includeRoomImages'] = true;
        
        $output['success']  = true;
        $output['result']   = Expedia::rooms($data);
        $output['errors']   = Expedia::errors();
        $output['addition'] = Expedia::addition();
        $output['data']     = json_decode(Expedia::inst()->net_result);
        
        return json_encode($output);
    }
    
    
    /**
    * Выполняет бронирование номера
    * 
    */
    public function action_book()
    {
        $data = Input::all();
        
        $data['room1']                     = '2';
        $data['room1FirstName']            = 'test';
        $data['room1LastName']             = 'testers';
        $data['room1BedTypeId']            = '23';
        $data['room1SmokingPreference']    = 'NS';
        
        $output['success']  = true;
        $output['result']   = Expedia::book($data);
        $output['errors']   = Expedia::errors();
        $output['addition'] = Expedia::addition();
        
        return json_encode($output);
    }

}