<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Element;

class ShowElements extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'element:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show a list of elements in the database, optionally with their attributes';

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
        $with_attributes = $this->option('with_attributes');
        $slug = $this->option('element_slug');

        if(!is_null($slug)) {
            $elements[] = Element::read($slug); 
        } else {
            $elements = Element::readAll();
        }


        foreach($elements as $el) {
            $this->info($el['nickname'].' ('.$el['slug'].')');

            if($with_attributes) {
                foreach($el['attributes'] as $attr) {
                    $this->info("   --".$attr['key'].": ".$attr['value']);
                }
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
            ['with_attributes', 'a', InputOption::VALUE_NONE , 'Show elements with their attributes',],
            ['element_slug', 's', InputOption::VALUE_OPTIONAL, 'Fetch one element by slug']
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
