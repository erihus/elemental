<?php namespace Elemental\Core;

use Illuminate\Validation\Validator as BaseValidator;
use Symfony\Component\Translation\TranslatorInterface;

class Validator extends BaseValidator{

    use \Illuminate\Console\AppNamespaceDetectorTrait;

    protected $component;
    protected $data = [];
    protected $rules = [];
  

    public function __construct(TranslatorInterface $translator)
    {
        $data = [];
        $rules = [];
        parent::__construct($translator, $data, $rules, $messages = array(), $customAttributes = array());
    }

    public function run($prototype, $component, $input, $is_cli = false)
    {   
        $rules = [];
        $this->loadComponent($prototype, $component);

        //Check if input field types are allowed on component
        $inFields = '';
        foreach($this->component->fields as $key => $type) {
            $inFields .= $key.',';
        }

        foreach($input as $key => $val) {
            $rules[$key] = 'inKeys:'.$inFields;
        }
        $this->setCustomMessages(['in_keys' => 'Field type :attribute not allowed on this component']);

        //Check which set of validation rules to use
        if(!$is_cli && !empty($this->component->rules)) {
            $rules = $this->component->rules;
        } elseif($is_cli && !empty($this->component->generator_rules)) {
            $rules = $this->component->generator_rules;
        }

        
        $this->setRules($rules);
        $this->setData($input);        

        return $this->passes(); 
    }
    
    public function getErrors()
    {
        return $this->errors()->getMessages();
    }


    protected function loadComponent($prototype, $component)
    {
        $componentClass = $this->getComponentClass($prototype, $component);
        $this->component = new $componentClass;
    }   


    protected function getComponentClass($prototype, $component)
    {

        $vendorClassString = "Elemental\\Components\\".ucfirst($prototype)."s\\".ucfirst($component).'Component';
        $appNamespace = $this->getAppNamespace();
        $userClassString = $appNamespace.$vendorClassString; 

        if(class_exists($vendorClassString)) { //check if selected component exists in vendor dir
            return $vendorClassString;
        } elseif(class_exists($userClassString)) { //check if selected component is a custom user component
            return $userClassString;
        }
    }

    
    /**
     * Validate an attribute is contained within a list of values.
     *
     * @param  string  $attribute
     * @param  mixed   $value
     * @param  array   $parameters
     * @return bool
     */
    protected function validateInKeys($attribute, $value, $parameters)
    {
        return in_array((string) $attribute, $parameters);
    }

        


}
