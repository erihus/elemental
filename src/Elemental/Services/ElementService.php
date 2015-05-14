<?php namespace Elemental\Services;

use Elemental\Core\Contracts\HubInterface;


class ElementService implements HubInterface {

    
    protected $element;
    protected $attribte;
    protected $collection;
    protected $validator;
    protected $input;
    protected $errors = [];
    
    public $component;
    

    
    public function __construct($validator, $element, $collection, $attributes) {
        $this->validator = $validator;
        $this->element = $element;
        $this->attributes = $attributes;
        $this->collection = $collection;
    }




    public function create($input, $skip_validation = false) {
        try {

            $this->component = $this->loadComponent($input['type']);
            $this->input = $input;

            if(!$skip_validation) {
                if(!$this->runValidation($this->component->prototype, $this->input['type'], $input['attributes'])) {
                    return false;
                }
            }

            $element = $this->element->create(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type']]);
            $this->attachAttributes($element['slug'], $input['attributes']);
            return true;  

        } catch (Exception $e) {
            array_push($this->errors, $e->getMessage().'on line '.$e-getLine().' in '.$e->getFile());
            return false;
        }

    }


    public function read($slug) {
        return $this->element->find($slug);
    }


    public function readAll($type=null) {
        return $this->element->findAll($type);
    }


    public function query($params, $normalizeAttributes = true){
        return $this->element->findBy($params, $normalizeAttributes);
    }


    public function findByAttribute($elementParams = [], $attrKey, $attrValue) {
        $elements  = $this->query($elementParams);

        $return = [];
        foreach($elements as $element) {
            foreach($element['attributes'] as $key => $val) {
                if($key == $attrKey && $val == $attrValue) {
                    $return = $element;
                }
            }
        }

        return $return;
    }



    public function copy($sourceSlug, $newNickname, $newSlug) {
        $sourceElement = $this->element->find($sourceSlug);
        $cloneAttributes = [];
        foreach ($sourceElement['attributes'] as $attr) {
            $cloneAttributes[$attr['key']] = $attr['value'];
        }
        $cloneInput = [
            'nickname' => $newNickname,
            'slug' => $newSlug,
            'type' => $sourceElement['type'],
            'attributes' => $cloneAttributes
        ];
        return $this->create($cloneInput, true);
    }



    public function update($slug, $updates) {
        try {
            $element = $this->element->find($slug);
            $this->component = $this->loadComponent($element['type']);

            if(!$this->runValidation($this->component->prototype, $element['type'], $updates)) {
                return false;
            }

            $this->attributes->update($slug, $updates);
            return true;   

        } catch (Exception $e) {
            array_push($this->errors, $e->getMessage().'on line '.$e-getLine().' in '.$e->getFile());
            return false;
        }
    }


    public function updateMeta($slug, $updates)
    {
        return $this->element->edit($slug, $updates);
    }



    public function delete($slug) {
        if(!$this->attributes->delete($slug) || !$this->element->delete($slug)) {
            return false;
        }

        return true;
    }



    public function attach($elSlug, $colSlug) {
        $parent = $this->collection->find($colSlug);
        $child = $this->element->find($elSlug);

        $attachablePrototype = $parent['component']['attachablePrototype'];
        $attachableComponent = $parent['component']['attachableComponent'];

        //Some collections can only have certain types of collections attached, check if that's the case and if we're are running afoul of that 
        if(!is_null($attachablePrototype) && !is_null($attachableComponent)) {
            if(is_array($attachableComponent)) {
                $componentMatch = ( in_array($child['type'], $attachableComponent) );

                //if it doesn't match directly, check the types inherited
                if(!$componentMatch) {
                    foreach($attachableComponent as $comp) {
                        if($comp == $child['type']) {
                            $componentMatch = true;
                            break;
                        }
                    }
                }
            } else {
                $componentMatch = ( $child['type'] == $attachableComponent || $child['component']['extendsFrom'] == $attachableComponent.'Component' );
            }


             if(is_array($attachablePrototype)) {
                $prototypeMatch = true;
            } else {
                $prototypeMatch = $child['component']['prototype'] == $parent['component']['attachablePrototype'];
            }

            if(!$componentMatch || !$prototypeMatch) {
                // var_dump($componentMatch);
                // var_dump($prototypeMatch);
                array_push($this->errors, ['errors' => 'This type of element cannot be attached to the parent collection.']);
                return false;
            }
        }

        return $this->collection->attachElement($elSlug, $colSlug);
    }


    public function detach($elSlug, $colSlug) {
        return $this->collection->detachElement($elSlug, $colSlug);
    }


    public function attachAttributes($elementSlug, $attributes) {
        return $this->attributes->createAndAttach($elementSlug, $attributes);
    }



    public function runValidation($prototype, $componentType, $data){
        $cli = (php_sapi_name() == 'cli') ? true : false;

         if(!$this->validator->run($prototype, $componentType, $data, $cli)){
            array_push($this->errors, $this->validator->getErrors());
            return false;
         }

         return true;
    }


    public function errors() {
        return $this->errors;
    }   



    protected function loadComponent($type) {
        $className = "Elemental\\Components\\Elements\\".ucfirst($type)."Component";
        return new $className;
    }

    

    
}
