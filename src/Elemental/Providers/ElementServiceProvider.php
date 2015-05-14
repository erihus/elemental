<?php namespace Elemental\Providers;

use Illuminate\Support\ServiceProvider;



class ElementServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('element', function($app)
		{
			$validator = $app->make('Elemental\Core\Validator');
			$element = $app->make('Elemental\Core\Contracts\ElementInterface');
			$attributes = $app->make('Elemental\Core\Contracts\ElementAttributeInterface');
			$collection = $app->make('Elemental\Core\Contracts\CollectionInterface');

			return new \Elemental\Services\ElementService($validator, $element, $collection, $attributes);
		});

	}

}
