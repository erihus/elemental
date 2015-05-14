<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;

class CloneCollection extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:clone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone a collection and its attributes. Optionally clone all attached elements as well.';

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
        $newNickname = trim($this->option('new_nickname'));
        $newSlug = ($this->option('new_slug')) ? trim($this->option('new_slug')) : trim(strtolower(str_replace(' ', '_', $newNickname)));
        $include_elements = $this->option('with_elements');

        if(Collection::copy($sourceSlug, $newNickname, $newSlug, $include_elements)){
            $this->info('Collection cloned.');
        } else {
            $this->displayErrors(Collection::errors());
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
            ['slug', InputArgument::REQUIRED, 'The slug of the collection you want to clone.'],
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
            ['new_nickname', 'k', InputOption::VALUE_REQUIRED, 'Nickname to apply to the cloned collection'],
            ['new_slug', 's', InputOption::VALUE_OPTIONAL, 'A slug used to extract the collection from the database. If none provided it will be generated from the nickname.'],
            ['with_elements', 'e',  InputOption::VALUE_NONE, 'Whether to do a deep copy of elements attached to this collection.']
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
