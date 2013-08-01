<?php

class ExpediaController extends Controller {
    
    
    public function __construct()
    {        
        Expedia::set('cid', '55505')
               ->set('apiKey', '9j5yzzmywf936na49yjc66t4')
               ->set('locale', 'ru_RU')
               ->set('minorRev', 25)
               ->set('currencyCode', 'RUB');
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
        Expedia::set('numberOfResults', 21);
        
        $filter = Input::all();
        
        $filter['sort']   = 'PRICE';
        $filter['room1']  = '2';
        $filter['room2']  = '1';
        
        return Expedia::make('HotelList', $filter, Input::get('callback'));
    }
    
    
    /**
    * Возвращает подробную информацию об отеле
    * 
    */
    public function getHotelInfo()
    {
        $data = Input::all();
                
        return Expedia::make('HotelInformation', $data, Input::get('callback'));
    }
    
    
    /**
    * Возвращает данные о доступных комнатах для указанного отеля
    * 
    */
    public function getRoomAvailability()
    {
        $data = Input::all();
        
        $data['includeDetails'] = true;
        $data['includeRoomImages'] = true;
        $data['room1'] = '2';
                
        return Expedia::make('RoomAvailability', $data, Input::get('callback'));
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