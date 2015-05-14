<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Element;


class DeleteElement extends Command {


    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'element:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an element and its attributes from the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $slug = $this->argument('slug');

        if(Element::delete($slug)){
            $this->info('Element deleted');
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
            ['slug', InputArgument::REQUIRED, 'The slug of the element you want to delete.'],
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