<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;

class ShowCollections extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a list of collections in the database, optionally with their attributes';

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
        $with_attributes = $this->option('attributes');
        $with_children = $this->option('children');
        $slug = $this->option('slug');

        if(!is_null($slug)) {
            $collections[] = Collection::read($slug); 
        } else {
            $collections = Collection::readAll();
        }

        foreach($collections as $col) {
            //print_r($col);
            $this->comment($col['nickname'].' ('.$col['slug'].')');

            if($with_attributes) {
                $this->info('  ====== Attributes ======');
                foreach($col['attributes'] as $attr) {
                    $this->line("   --".$attr['key'].": ".$attr['value']);
                }
            }

            if($with_children) {
                $this->info('  ====== Children ======');
                $this->_showChildren($col);
            }

            $this->line('');
        }

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['attributes', 'a', InputOption::VALUE_NONE , 'Show collections with their attributes',],
            ['children', 'c', InputOption::VALUE_NONE , 'Show child elements and collections',],
            ['slug', 's', InputOption::VALUE_OPTIONAL, 'Fetch a collection by slug']
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

    private function _showChildren($collection, $recursion = false) {
        $spacer = ($recursion) ? '      ' : '  ';
        foreach($collection['children'] as $child) {
            if(isset($child['children'])) {
                $this->comment($spacer."--".$child['nickname'].' ('.$child['slug'].')');
                $this->_showChildren($child, true);
            }  else {
                $this->line($spacer."--".$child['nickname'].' ('.$child['slug'].')');
            }
        }
    }

}
