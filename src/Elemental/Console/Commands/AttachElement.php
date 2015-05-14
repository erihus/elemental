<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Element;

class AttachElement extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'element:attach';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach element(s) to a collection.';

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
        $elements = $this->option('element');
        
        if(empty($elements)) {
            $this->error('You must provide at least one element to attach.');
            exit;
        }

        foreach($elements as $elSlug) {
            if(Element::attach($elSlug, $colSlug)){
                $this->info('Element '.$elSlug.' attached to '.$colSlug);
            } else {
                $this->displayErrors(Element::errors());
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
            ['collection_slug', InputArgument::REQUIRED, 'The slug of the collection you want to attach an element to.'],
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
            ['element', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Slug of element you want to attach to the collection', null],
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
