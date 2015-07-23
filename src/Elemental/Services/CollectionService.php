<?php namespace Elemental\Services;

use Elemental\Core\Contracts\HubInterface;
use Element;


class CollectionService implements HubInterface {

    use \Illuminate\Console\AppNamespaceDetectorTrait;

    protected $element;
    protected $attributes;
    protected $collection;
    protected $validator;
    protected $input;
    protected $errors = [];
    
    public $component;
    

    
    public function __construct($validator, $collection, $element, $attributes) {
        $this->validator = $validator;
        $this->collection = $collection;
        $this->element = $element;
        $this->attributes = $attributes;
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

            $col = $this->collection->create(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type'], 'reorderable' => $input['reorderable'], 'addable' => $input['addable']]);
            $this->attachAttributes($col['slug'], $input['attributes']);
            return true;  

        } catch (Exception $e) {
            array_push($this->errors, $e->getMessage().'on line '.$e-getLine().' in '.$e->getFile());
            return false;
        }
    }


    public function read($slug) {
        return $this->collection->find($slug);
    }

    public function readAll($type = null) {
        return $this->collection->findAll($type);
    }


    public function query($params, $normalizeAttributes = true){
        return $this->collection->findBy($params, $normalizeAttributes);
    }


    public function findByAttribute($collectionParams = [], $attrKey, $attrValue) {
        $collections  = $this->query($collectionParams);

        $return = [];
        foreach($collections as $collection) {
            foreach($collection['attributes'] as $key => $val) {
                if($key == $attrKey && $val == $attrValue) {
                    array_push($return, $collection);
                }
            }
        }

        return $return;
    }


    public function copy($sourceSlug, $newNickname, $newSlug, $include_elements = false) {

        try {
            $sourceCollection = $this->collection->find($sourceSlug);
            $cloneAttributes = [];
            foreach ($sourceCollection['attributes'] as $attr) {
                $cloneAttributes[$attr['key']] = $attr['value'];
            }
            $cloneInput = [
                'nickname' => $newNickname,
                'slug' => $newSlug,
                'type' => $sourceCollection['type'],
                'reorderable' => $sourceCollection['reorderable'],
                'addable' => $sourceCollection['addable'],
                'attributes' => $cloneAttributes
            ];

            $this->create($cloneInput, true);

            if($include_elements) {
                $elements = $this->collection->fetch_elements($sourceSlug);
                $collections = $this->collection->fetch_children($sourceSlug);

                foreach($elements as $element) {
                    
                    $sourceElementSlug =  $element['slug'];
                    $newElementSlug = $sourceElementSlug.'_copy_'.$newSlug;
                    $newElementNickname = $element['nickname'].' Copy '.$newNickname;
                    Element::copy($sourceElementSlug, $newElementNickname, $newElementSlug);
                    Element::attach($newElementSlug, $newSlug);
                }

                foreach($collections as $col) {
                    $sourceColSlug = $col['slug'];
                    $newColSlug = $sourceColSlug.'_copy_'.$newSlug;
                    $newColNickname = $col['nickname'].' Copy '.$newNickname;
                    $this->copy($sourceColSlug, $newColNickname, $newColSlug, true);
                    $this->attach($newColSlug, $newSlug);
                }
            }

            
            return true;
        } catch (Exception $e) {
            array_push($this->errors, $e->getMessage().'on line '.$e-getLine().' in '.$e->getFile());
            return false;
        }
    }


    public function update($slug, $updates) {
        try {
            $collection = $this->collection->find($slug);
            $this->component = $this->loadComponent($collection['type']);

            if(!$this->runValidation($this->component->prototype, $collection['type'], $updates)) {
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
        return $this->collection->edit($slug, $updates);
    }


    public function updateOrder($slug, $childType, $childId, $childOrder)
    {
        return $this->collection->order($slug, $childType, $childId, $childOrder);
    }

    public function delete($slug) {
        if(!$this->attributes->delete($slug) || !$this->collection->delete($slug)) {
            return false;
        }

        return true;
    }


    public function attach($childSlug, $parentSlug) {


        $child = $this->collection->find($childSlug);
        $parent = $this->collection->find($parentSlug);

        $attachablePrototype = $parent['component']['attachablePrototype'];
        $attachableComponent = $parent['component']['attachableComponent'];

        //Some collections can only have certain types of collections attached, check if that's the case and if we're are running afoul of that 
        if(!is_null($attachablePrototype) && !is_null($attachableComponent)) {
            if(is_array($attachableComponent)) {

                $componentMatch = (in_array($child['type'], $attachableComponent));

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
                $componentMatch = ($child['type'] == $attachableComponent || $child['component']['extendsFrom'] == $attachableComponent.'Component');
            }

             if(is_array($attachablePrototype)) {
                $prototypeMatch = true;
            } else {
                $prototypeMatch = $child['component']['prototype'] == $parent['component']['attachablePrototype'];
            }

            if(!$componentMatch || !$prototypeMatch) {
                // var_dump($componentMatch);
                // var_dump($prototypeMatch);
                array_push($this->errors, ['errors' => 'This type of collection cannot be attached to the parent collection.']);
                return false;
            }
        }

        return $this->collection->attachCollection($childSlug, $parentSlug);
    }


    public function detach($childSlug, $parentSlug) {
        return $this->collection->detachCollection($childSlug, $parentSlug);
    }



    public function attachAttributes($collectionSlug, $attributes) {
        return $this->attributes->createAndAttach($collectionSlug, $attributes);
    }


    public function normalizeChildren($children)
    {
        $normalizedChildren = [];
        foreach($children as $child) {
            $normalizedChildren[$child['slug']] = [
                'nickname' => $child['nickname'],
                'attributes' => $child['attributes']
            ];

            if(isset($child['children']) && count($child['children']) > 0) {
                $normalizedChildren[$child['slug']]['children'] = $this->normalizeChildren($child['children']);
            }
        }

        return $normalizedChildren;
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
        
        $vendorClassString = "Elemental\\Components\\Collections\\".$type.'Component';
        $appNamespace = $this->getAppNamespace();
        $userClassString = $appNamespace.$vendorClassString; 
        $className = null; 

        if(class_exists($vendorClassString)) { //check if selected component exists in vendor dir
            $className = $vendorClassString;
        } 

        if(class_exists($userClassString)) { //check if selected component is a custom user component
            $className = $userClassString;
        }

        // $className = "Elemental\\Components\\Collections\\".ucfirst($type)."Component";
        return new $className;
    }

}