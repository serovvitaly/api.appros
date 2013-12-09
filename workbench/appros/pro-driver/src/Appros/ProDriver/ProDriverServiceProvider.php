<?php namespace Appros\ProDriver;

use Illuminate\Support\ServiceProvider;

class ProDriverServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}
    
    public static function make($driverName)
    {   
        $driverClass = __NAMESPACE__  . '\Drivers\\' . $driverName;
         
        if (class_exists($driverClass)) {
            return new $driverClass;
        } else {
            throw new ProDriverException("Driver '{$driverClass}' is not found");
        }
    }

}