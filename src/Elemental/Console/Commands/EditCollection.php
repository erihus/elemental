<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;

class EditCollection extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:edit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit the attributes of a collection.';

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
        $slug = $this->argument('slug');

        $newNickname =  trim($this->option('nickname'));
        $newSlug =  trim($this->option('slug'));
        $inputAttributes = $this->option('attribute');
        $reorderable = (strlen($this->option('reorderable'))) ? filter_var($this->option('reorderable'), FILTER_VALIDATE_BOOLEAN) : null;
        $addable = (strlen($this->option('addable'))) ? filter_var($this->option('addable'), FILTER_VALIDATE_BOOLEAN) : null;

        if(!strlen($newNickname) && !strlen($newSlug) && is_null($addable) && is_null($reorderable) && empty($inputAttributes)) {
            $this->error('No input provided');
            exit;
        }


        if(!empty($inputAttributes)) {
            $attributes = array();
            foreach($inputAttributes as $attr) {
                $arr = explode(':', $attr);
                $attributes[str_replace('=', '', $arr[0])] = $arr[1];
            }

            if(Collection::update($slug, $attributes, true)){
                $this->info('Collection updated');
            } else {
                $this->displayErrors(Collection::errors());
            }
        }


        if(strlen($newNickname) || strlen($newSlug) || !is_null($reorderable) || !is_null($addable)) {
            $updates = [];
            if(strlen($newNickname)) {
                $updates['nickname'] = $newNickname;
                if(!strlen($newSlug)) {
                    $createSlug = $this->confirm('You are assigning a new nickname but not a new slug. Do you me to create a new slug from the new nickname? [yes|no]');
                    if($createSlug) {
                        $updates['slug'] = strtolower(str_replace(' ', '_', $newNickname));
                    }
                }
            }

            if(strlen($newSlug)) {
                $updates['slug'] = $newSlug;
            }

            if(!is_null($reorderable)) {
                $updates['reorderable'] = $reorderable;
            }


            if(!is_null($addable)) {
                $updates['addable'] = $addable;
            }



            if(Collection::updateMeta($slug, $updates)) {
                $this->info('Collection metadata updated');
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
            ['slug', InputArgument::REQUIRED, 'The slug of the collection you want to edit.'],
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
            ['attribute', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Attributes to modify, expressed as --attribute "key:value"', null],
            ['reorderable', 'r', InputOption::VALUE_OPTIONAL, 'Whether or not the collection items should be reorderable. true|false'],
            ['addable', 'd', InputOption::VALUE_OPTIONAL, 'Whether or not the collection can have new elements added in the CMS. true|false'],
            ['nickname', 'k', InputOption::VALUE_OPTIONAL, 'Updates the collection nickname.'],
            ['slug', 's', InputOption::VALUE_OPTIONAL, 'Updates the collection slug.'],
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
