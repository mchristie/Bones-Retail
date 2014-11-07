<?php
namespace Christie\Retail;

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
		\Bones::registerFieldType('Retail', 'price', 'Christie\Retail\Fieldtypes\PriceField');
		\Bones::registerFieldType('Retail', 'order_details', 'Christie\Retail\Fieldtypes\OrderDetailsField');

		\Bones::registerWidget('basket', 'Christie\Retail\Widgets\BasketWidget');

		\Bones::registerComponent(
			'retail',
			'\Christie\Retail\Components\RetailComponent',
			'\Christie\Retail\RetailController@showIndex'
		);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('retail', function($app) {
            return new Components\RetailComponent($app['bones']);
        });
        $this->app->bindShared('basket', function($app) {
            return new Libraries\Basket();
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
