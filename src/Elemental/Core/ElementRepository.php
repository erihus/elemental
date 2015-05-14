<?php namespace Elemental\Core;

use \ReflectionClass;
use Elemental\Core\Contracts\ElementInterface;
use Elemental\Core\Element;
use Elemental\Core\Collection;
use DB;

class ElementRepository implements ElementInterface {

    public function create(array $input) {
        try {
            $element = Element::create($input)->toArray();
            return $element;
        } catch (Exception $e) {
            return false;
        }
        
    }

    public function find($slug) {
        $element =  Element::where('slug', $slug)->with('attributes')->first()->toArray();
        $element['component'] = $this->_bootstrapComponent('element', $element['type']);

        return $element;
    }

    public function edit($slug, $input) {
        try {
            $element = $this->_findRaw($slug);
            $element->fill($input)->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }



    public function findAll($type=null) {
        if(!is_null($type)) {
            $elements = Element::where('type', ucfirst($type))->with('attributes')->get()->toArray();
            return $elements;
        } else {
            return Element::with('attributes')->get()->toArray();
        }
    }


    public function findBy($params, $normalizeAttributes = true)
    {
        $elements = Element::where($params)->with('attributes')->get()->toArray();
        
        $status = (isset($params['status'])) ? $params['status'] : null;
        for($i=0; $i<count($elements); $i++) {
            if($normalizeAttributes) {
                $elements[$i]['attributes'] = $this->_normalizeAttributes($elements[$i]['attributes']);
            }
            $elements[$i]['component'] = $this->_bootstrapComponent('element', $elements[$i]['type']);
        }
        return $elements;
    }


    public function delete($slug)
    {
        try {
            $element = $this->_findRaw($slug);

            //remove element from any collections its attached to
            $associates = DB::table('parent_child')->where('child_id', $element->id)->get();
            foreach($associates as $assoc) {
                $col = Collection::find($assoc->parent_id);
                if(!is_null($col)) {
                    $col->elements()->detach($element->id);
                }   
            }

            $element->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function _findRaw($slug) {
        return Element::where('slug', $slug)->with('attributes')->first();
    }


    private function _bootstrapComponent($protoType, $componentType) {
        $proto = ucfirst($protoType).'s';
        $componentArray = [];
        $componentClassName = "Elemental\\Components\\".$proto."\\".$componentType."Component";
        $component = new $componentClassName;
        $reflector = new ReflectionClass($componentClassName);
        $extendsFrom = $reflector->getParentClass();
        $extendsFromClass = new ReflectionClass($extendsFrom->name);
        $extendsFromName = $extendsFrom->getShortName();
        $componentArray['extendsFrom'] = $extendsFromName;
        $properties = $reflector->getProperties();


        foreach($properties as $prop) {
            $propName = $prop->name;
            if($propName !== 'rules') {
                $componentArray[$propName] = $component->$propName;
            } else {
                $componentArray['rules'] = [];
                if(is_array($component->rules) && !empty($component->rules)) {
                    foreach($component->rules as $fieldName => $ruleList) {
                        $rules = explode('|', $ruleList);
                        $componentArray['rules'][$fieldName] = $rules;
                    }
                }
            }
        }
        return $componentArray;
    }

    private function _normalizeAttributes($attributes) {
        $returnArray = [];
        foreach($attributes as $attr) {
            $returnArray[$attr['key']] = $attr['value'];
        }
        return $returnArray;
    }

}
