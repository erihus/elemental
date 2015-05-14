<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;

class DetachCollection extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:detach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detach collection(s) from a collection.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $colSlug = $this->argument('collection_slug');
        $collections = $this->option('collection');
        
        if(empty($collections)) {
            $this->error('You must provide at least one collection to detach.');
            exit;
        }

        foreach($collections as $childSlug) {
            if(Collection::detach($childSlug, $colSlug)){
                $this->info('Collection '.$childSlug.' detached from '.$colSlug);
            } else {
                $this->displayErrors(Collection::errors());
            }
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['collection_slug', InputArgument::REQUIRED, 'The slug of the parent collection.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['collection', 'c', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Slug of collection(s) you want to detach from the parent collection', null],
        ];
    }


    protected function displayErrors($errors)
    {
        $errorList = '';
        foreach ($errors[0] as $key => $msg) {
            $errorList .= $msg[0]."\n"; 
        }
        $this->error("Error: \n".$errorList);
    }

}
