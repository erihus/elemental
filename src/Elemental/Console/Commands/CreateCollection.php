<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Translation\TranslatorInterface;
use Elemental\Core\Validator;
use Collection;


class CreateCollection extends Command {


    protected $validator;
    protected $filesystem;
    protected $classString;
    protected $componentType;
    protected $component;
    protected $attributes;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'collection:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an collection.';

    /**
     * Create a new command instance.
     *
     * @return void
     */


    public function __construct(Filesystem $filesystem, TranslatorInterface $translator)
    {
        parent::__construct();        
        $data = [];
        $rules = [];
        $this->filesystem = $filesystem;
        $this->validator = new Validator($translator, $data, $rules, $messages = array(), $customAttributes = array());
        $this->dir = __DIR__.'/../../Components/Collections';
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $componentList = $this->getComponents(); 
        
        $this->componentType = $this->choice("What kind of collection do you want to create?", $componentList);
        $classString = "Elemental\\Components\\Collections\\".$this->componentType.'Component';
        $this->component = new $classString;        
        
        $this->loadAttributes();

        $nickname = trim($this->argument('nickname'));    
        $slug = ($this->option('slug')) ? trim($this->option('slug')) : trim(strtolower(str_replace(' ', '_', $nickname)));
        $reorderable = (strlen($this->option('reorderable'))) ? filter_var($this->option('reorderable'), FILTER_VALIDATE_BOOLEAN) : false;
        $addable = (strlen($this->option('addable'))) ? filter_var($this->option('addable'), FILTER_VALIDATE_BOOLEAN) : false;

        $input = [
            'nickname' => $nickname,
            'slug' => $slug,
            'type' => $this->componentType,
            'reorderable' => $reorderable,
            'addable' => $addable,
            'attributes' => []
        ];

        foreach($this->attributes as $key => $val) {
            $input['attributes'][$key] = $val;  
        }

        if(Collection::create($input)) {
            $this->info('Collection created');
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
            ['nickname', InputArgument::REQUIRED, 'A nickname for the element.'],
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
            ['reorderable', 'r', InputOption::VALUE_OPTIONAL, 'Whether or not the collection items should be reorderable. true|false'],
            ['addable', 'd', InputOption::VALUE_OPTIONAL, 'Whether or not the collection can have new elements added in the CMS. true|false'],
            ['slug', 's', InputOption::VALUE_OPTIONAL, 'A slug used to extract the element from the database. If none provided it will be generated from the nickname.']
        ];
    }



    /**
     * Build an array of component options to show the user
     *
     * @return array
     */
    public function getComponents()
    {
        $componentList = [];
        $components = $this->filesystem->allFiles($this->dir);
        foreach($components as $componentFile) {
            $name = $this->filesystem->name($componentFile);

            if(preg_match('/^(.+)Component$/', $name, $matches)) {
                array_push($componentList, $matches[1]);
            }
        }

        return $componentList;
    }

    protected function runValidation()
    {
        return $this->validator->run($this->component->prototype, $this->componentType, $this->attributes, $cli = true);
    }


    protected function displayInlineErrors()
    {
        $messagesArray = $this->validator->getErrors();
        $errorList = '';
        foreach ($messagesArray as $key => $msg) {
            $errorList .= $msg[0]."\n"; 
        }
        $this->error("Fields required: \n".$errorList);
    }



    protected function displayErrors($errors)
    {
        $errorList = '';
        foreach ($errors[0] as $key => $msg) {
            $errorList .= $msg[0]."\n"; 
        }
        $this->error("Error: \n".$errorList);
    }


    protected function loadAttributes()
    {
        $this->attributes = $this->buildAttributesArray();

        if(!$this->runValidation()) {
            $this->displayInlineErrors();
            $this->loadAttributes();
        }       
    }

    /**
     * Loop thru fields in the prototype class to build a key value array
     *
     * @return array
     */
    protected function buildAttributesArray(){
        $filled = $this->attributes;
        $attributes = [];
        foreach($this->component->fields as $key => $val) {
            if(!empty($filled) && strlen($filled[$key]) ) {
                $attributes[$key] = $filled[$key];
            } else {
                if($val == 'boolean') {
                    $attributes[$key] = $this->confirm($key."[true|false]: ", true);
                } else {
                    $attributes[$key] = $this->ask($key." value: ");
                }
            }
        }

        return $attributes;
    }


}
