<?php

class ApiController extends Controller {
    
    protected $result = NULL;
    
    public function __construct()
    {
        $self = $this;
        
        $this->beforeFilter(function() {
            ClassLoader::addDirectories(array(
                app_path().'/models/Tours',

            ));
        });
        
        $this->afterFilter(function() use ($self) {
            $self->outputJson();
        });
    }
    
    public function outputJson()
    {
        $json = array();
        
        $json['result'] = $this->result;
        
        echo json_encode($json);
    }

}