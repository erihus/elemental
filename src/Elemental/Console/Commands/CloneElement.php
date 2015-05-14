<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Element;

class CloneElement extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'element:clone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone an element and its attributes.';

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
        $sourceSlug = $this->argument('slug');
        $newNickname =  trim($this->option('new_nickname'));
        $newSlug = ($this->option('new_slug')) ? trim($this->option('new_slug')) : trim(strtolower(str_replace(' ', '_', $newNickname)));
            

        if(Element::copy($sourceSlug, $newNickname, $newSlug)){
            $this->info('Element cloned.');
        } else {
            $this->displayErrors(Element::errors());
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
            ['slug', InputArgument::REQUIRED, 'The slug of the element you want to clone.'],
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
            ['new_nickname', 'k', InputOption::VALUE_REQUIRED, 'Nickname to apply to the cloned element', null],
            ['new_slug', 's', InputOption::VALUE_OPTIONAL, 'A slug used to extract the element from the database. If none provided it will be generated from the nickname.'],
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
