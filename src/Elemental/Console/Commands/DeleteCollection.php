<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Collection;


class DeleteCollection extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a collection and its attributes from the database.';

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

        if(Collection::delete($slug)){
            $this->info('Collection deleted');
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