<?php namespace Elemental\Core;

use Illuminate\Validation\Validator as BaseValidator;
use Symfony\Component\Translation\TranslatorInterface;

class Validator extends BaseValidator{

    protected $component;
    protected $data = [];
    protected $rules = [];
  

    public function __construct(TranslatorInterface $translator)
    {
        $data = [];
        $rules = [];
        parent::__construct($translator, $data, $rules, $messages = array(), $customAttributes = array());
    }

    public function run($namespace, $component, $input, $is_cli = false)
    {   
        $rules = [];
        $this->loadComponent($namespace, $component);

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


    protected function loadComponent($namespace, $component)
    {
        $componentClass = $this->getComponentClass($namespace, $component);
        $this->component = new $componentClass;
    }   


    protected function getComponentClass($namespace, $component)
    {
        return "Elemental\\Components\\".ucfirst($namespace)."s\\".ucfirst($component)."Component";
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
