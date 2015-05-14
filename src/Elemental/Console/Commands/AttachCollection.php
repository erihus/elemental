<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;

class AttachCollection extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:attach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach collection(s) to another collection.';

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
        $parentSlug = $this->argument('parent_collection');
        $children = $this->option('child_collection');
        
        if(empty($children)) {
            $this->error('You must provide at least one colleciton to attach.');
            exit;
        }

        foreach($children as $childSlug) {
            if(Collection::attach($childSlug, $parentSlug)){
                $this->info('Collection '.$childSlug.' attached to '.$parentSlug);
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
            ['parent_collection', InputArgument::REQUIRED, 'The slug of the collection you want to attach another collection to.'],
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
            ['child_collection', 'c', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Slug of child collection you want to attach to the parent collection', null],
        ];
    }


    protected function displayErrors($errors)
    {
        $errorList = '';
        foreach ($errors[0] as $key => $msg) {
           if(is_array($msg)) {
                $errorList .= $msg[0]."\n";
            } else {
                $errorList .= $msg;
            } 
        }
        $this->error("Error: \n".$errorList);
    }

}
