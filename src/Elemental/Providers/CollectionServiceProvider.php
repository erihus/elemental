<?php namespace Elemental\Providers;

use Illuminate\Support\ServiceProvider;



class CollectionServiceProvider extends ServiceProvider {

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
        $this->app->bind('collection', function($app)
        {
            $validator = $app->make('Elemental\Core\Validator');
            $element = $app->make('Elemental\Core\Contracts\ElementInterface');
            $attributes = $app->make('Elemental\Core\Contracts\CollectionAttributeInterface');
            $collection = $app->make('Elemental\Core\Contracts\CollectionInterface');

            return new \Elemental\Services\CollectionService($validator, $collection, $element, $attributes);
        });

    }

}
