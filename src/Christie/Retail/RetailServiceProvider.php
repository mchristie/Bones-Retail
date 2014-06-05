<?php namespace Christie\Retail;

use Illuminate\Support\ServiceProvider;

class RetailServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('christie/retail');

		// $bones = \App::make('bones');
		\Bones::registerFieldType('Price', 'Christie\Retail\Fieldtypes\PriceField');
		\Bones::registerWidget('basket', 'Christie\Retail\Widgets\BasketWidget');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{

        $this->app->bindShared('basket', function($app) {
            return new Libraries\Basket();
        });
		$this->app->bindShared('retail', function($app) {
            return new Libraries\Retail( $app['basket'] );
        });
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

}
