<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Translation\TranslatorInterface;
use Elemental\Core\Validator;
use Element;


class CreateElement extends Command {

	use \Illuminate\Console\AppNamespaceDetectorTrait;

	protected $filesystem;
	protected $validator;
	protected $classString;
	protected $componentType;
	protected $component;
	protected $attributes;
	protected $vendorDir;
	protected $appDir;

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'element:create';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate an element of a certain component.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */


	public function __construct(Filesystem $filesystem, TranslatorInterface $translator)
	{
		parent::__construct();
		$this->filesystem = $filesystem;	
		$this->validator = new Validator($translator);
		$this->vendorDir = __DIR__.'/../../Components/Elements';
        $this->appDir = app_path().'/Elemental/Components/Elements';
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$componentList = $this->getComponents(); 
		$this->componentType = $this->choice("What kind of element do you want to create?", $componentList);
		$this->setComponent();
		// $classString = "Elemental\\Components\\Elements\\".$this->componentType.'Component';
		// $this->component = new $classString;		
		
		$this->loadAttributes();

		$nickname = trim($this->argument('nickname')); 	
		$slug = ($this->option('slug')) ? trim($this->option('slug')) : trim(strtolower(str_replace(' ', '_', $nickname)));

		$input = [
			'nickname' => $nickname,
			'slug' => $slug,
			'type' => $this->componentType,
			'attributes' => []
		];

		foreach($this->attributes as $key => $val) {
			$input['attributes'][$key] = $val;	
		}

		if(Element::create($input)) {
			$this->info('Element created');
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
        $vendorComponents = [];
        $vendorComponents = $this->filesystem->allFiles($this->vendorDir);
        $appComponents = [];
        if($this->filesystem->exists($this->appDir)) {
            $appComponents = $this->filesystem->allFiles($this->appDir);
        }
        $components = array_merge($vendorComponents, $appComponents); //TODO - figure out class inheritance/overload

        foreach($components as $componentFile) {
            $name = $this->filesystem->name($componentFile);

            if(preg_match('/^(.+)Component$/', $name, $matches)) {
                array_push($componentList, $matches[1]);
            }
        }

        return $componentList;
    }
	
	protected function setComponent() 
	{
       
        $vendorClassString = "Elemental\\Components\\Elements\\".$this->componentType.'Component';
        $appNamespace = $this->getAppNamespace();
        $userClassString = $appNamespace.$vendorClassString; 
		$className = null;

        if(class_exists($vendorClassString)) { //check if selected component exists in vendor dir
            $className = $vendorClassString;
        } 

        if(class_exists($userClassString)) { //check if selected component is a custom user component
            $className = $userClassString;
        }

        $this->component = new $className;

    }

	protected function runValidation()
	{
		return $this->validator->run($this->component->prototype, $this->componentType, $this->attributes, $cli = true);
	}


	protected function displayLiveErrors()
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
			$this->displayLiveErrors();
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
		$attributes = null;
		foreach($this->component->fields as $key => $val) {
			if(!empty($filled) && strlen($filled[$key]) ) {
				$attributes[$key] = $filled[$key];
			} else {
				$attributes[$key] = $this->ask($key." value: ");
			}
		}
		return $attributes;
	}
}
