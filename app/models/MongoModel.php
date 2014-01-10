<?php

use \Purekid\Mongodm\Model as Eloquent;

class MongoModel extends Eloquent {
    
    protected $connection = 'api_tours';
    
    protected static function getModelTypes()
    {
        return array();
    }
    
}