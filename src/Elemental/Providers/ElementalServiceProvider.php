<?php namespace Elemental\Providers;

use Illuminate\Support\ServiceProvider;

class ElementalServiceProvider extends ServiceProvider {


	protected $commands = [
        'Elemental\Console\Commands\CreateElement',
        'Elemental\Console\Commands\EditElement',
        'Elemental\Console\Commands\CloneElement',
        'Elemental\Console\Commands\AttachElement',
        'Elemental\Console\Commands\DetachElement',
        'Elemental\Console\Commands\DeleteElement',
        'Elemental\Console\Commands\ShowElements',
        
        'Elemental\Console\Commands\CreateCollection',
        'Elemental\Console\Commands\EditCollection',
        'Elemental\Console\Commands\CloneCollection',
        'Elemental\Console\Commands\AttachCollection',
        'Elemental\Console\Commands\DetachCollection',
        'Elemental\Console\Commands\DeleteCollection',
        'Elemental\Console\Commands\ShowCollections',
    ];


	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{



		$this->publishes([
		    __DIR__.'/../../database/migrations/' => database_path('/migrations')
		], 'migrations');

		$this->loadViewsFrom(__DIR__.'/../../views', 'elemental');

		$this->publishes([
            __DIR__.'/../../views' => base_path('resources/views/vendor/elemental'),
        ], 'views');
		
		$this->publishes([
            __DIR__.'/../../assets/bower.json' => public_path('js/bower.json'),
        ], 'bower');

		$this->publishes([
		    __DIR__.'/../../assets/elemental' => public_path('js/elemental'),
		], 'angular');



		include __DIR__.'/../../routes.php';
	}



	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		
        $this->commands($this->commands);
    

		$this->app->bind(
			'Elemental\Core\Contracts\ElementInterface',
			'Elemental\Core\ElementRepository'
		);

		$this->app->bind(
			'Elemental\Core\Contracts\ElementAttributeInterface',
			'Elemental\Core\ElementAttributeRepository'
		);

		$this->app->bind(
			'Elemental\Core\Contracts\CollectionInterface',
			'Elemental\Core\CollectionRepository'
		);


		$this->app->bind(
			'Elemental\Core\Contracts\CollectionAttributeInterface',
			'Elemental\Core\CollectionAttributeRepository'
		);
	}

}
