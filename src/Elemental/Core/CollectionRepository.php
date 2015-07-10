<?php namespace Elemental\Core;


use Elemental\Core\Contracts\CollectionInterface;
use Elemental\Core\Collection;
use Elemental\Core\Element;
use DB;
use \ReflectionClass;

class CollectionRepository implements CollectionInterface{

    use \Illuminate\Console\AppNamespaceDetectorTrait;

   public function create(array $input) {
        try{
            $collection = Collection::create($input)->toArray();
            return $collection;
        } catch (Exception $e) {
            return false;
        }
    }


    public function find($slug) {
        $collection = Collection::where('slug', $slug)->with('attributes')->first()->toArray();
        $collection['component'] = $this->_bootstrapComponent('collection', $collection['type']);
        $collection['children'] = $this->_bootstrapChildren($collection['id']);
        return $collection;
    }

    public function findAll($type=null) {
        if(!is_null($type)) {
            $collections = Collection::where('type', ucfirst($type))->with('attributes')->get()->toArray();
        } else {
            $collections = Collection::with('attributes')->get()->toArray();
        }

        $colSlugs = [];

        foreach($collections as $collection) {
            array_push($colSlugs, $collection['slug']);
        }

        for($i=0; $i<count($collections); $i++) {
            $collections[$i]['component'] = $this->_bootstrapComponent('collection', $collections[$i]['type']);
            $collections[$i]['children'] = $this->_bootstrapChildren($collections[$i]['id']);
        
        }

        //Remove attached collections from the top level array
        foreach($collections as $col) {
            foreach($col['children'] as $child) {
                $childSlug = $child['slug'];
                $key = array_search($childSlug, $colSlugs);
                if($key) {
                    unset($collections[$key]);
                }
            }
          
        }

        return $collections;
    }


    public function findBy($params, $normalizeAttributes = true)
    {

        $collections = Collection::where($params)->with('attributes')->get()->toArray();
        
        $status = (isset($params['status'])) ? $params['status'] : null;
        for($i=0; $i<count($collections); $i++) {
            if($normalizeAttributes) {
                $collections[$i]['attributes'] = $this->_normalizeAttributes($collections[$i]['attributes']);
            }
            $collections[$i]['component'] = $this->_bootstrapComponent('collection', $collections[$i]['type']);
            $collections[$i]['children'] = $this->_bootstrapChildren($collections[$i]['id'], $status, $normalizeAttributes);
        }
        return $collections;
    }   


    public function edit($slug, $input) {
        try {
            $collection = $this->_findRaw($slug);
            $collection->fill($input)->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function order($slug, $childType, $childId, $childOrder) {
        try {
           $collection = $this->_findRaw($slug);
           if($childType == 'element') {
                $child = $collection->elements()
                    ->wherePivot('child_type', '=', $childType)
                    ->wherePivot('child_id', '=', $childId);
           } elseif ($childType == 'collection') {
                $child = $collection->collections()
                    ->wherePivot('child_type', '=', $childType)
                    ->wherePivot('child_id', '=', $childId);
           }

            $child->updateExistingPivot($childId, ['order' => $childOrder]);
           return true;

        } catch (Exception $e) {
            return false;
        }
    }


    public function fetch_elements($slug) {
        $collection = $this->_findRaw($slug);
        $collection->load(['elements' => function($query) use ($collection) {
            $query->where('parent_id', $collection->id)->where('child_type', 'element')->orderBy('order', 'asc');
        }]);
        $colArray= $collection->toArray();
        
        return $colArray['elements'];
    }

    public function fetch_children($slug) {
        $collection = $this->_findRaw($slug);

        $collection->load(['collections' => function($query) use ($collection) {
            $query->where('parent_id', $collection->id)->where('child_type', 'collection')->orderBy('order', 'asc');
        }]);
        $colArray = $collection->toArray();

        return $colArray['collections'];
    }

    public function attachElement($elementSlug, $collectionSlug)
    {   
        try {
            $el = Element::where('slug', $elementSlug)->first();
            $col = $this->_findRaw($collectionSlug);
            $order = $order = $this->_determineOrder($col);
            
            $col->elements()->attach($el, ['order' => $order, 'child_type' => 'element']);
            return true;
        } catch (Exception $e) {
            return false;
        } 
    }

    public function detachElement($elementSlug, $collectionSlug)
    {
        try {
            $el = Element::where('slug', $elementSlug)->first();
            $col = $this->_findRaw($collectionSlug);
            $col->elements()->detach($el->id);
            return true;
        } catch (Exception $e) {
            return false;   
        }
    }


    public function attachCollection($childSlug, $parentSlug)
    {
        try {
            $parent = $this->_findRaw($parentSlug);
            $child = $this->_findRaw($childSlug);
            $order = $this->_determineOrder($parent);
            $parent->collections()->save($child, ['order' => $order, 'child_type' => 'collection']);
            return true;
        }
        catch (Exception $e) {
            return false;
        } 
    }


    public function detachCollection($childSlug, $parentSlug) {
        try {
            $parent = $this->_findRaw($parentSlug);
            $child = $this->_findRaw($childSlug);
            $parent->collections()->detach($child);
            return true;
        }
        catch (Exception $e) {
            return false;
        } 
        return false;
    }


    public function delete($slug)
    {
        try {
            $collection = $this->_findRaw($slug);

            //detach any attached elements
            $collection->elements()->detach();

            //remove collection from any collections its attached to
            $associates = DB::table('parent_child')->where('child_id', $collection->id)->get();
            foreach($associates as $assoc) {
                $col = Collection::find($assoc->parent_id);
                if(!is_null($col)){
                    $col->collections()->detach($collection->id);
                }                
            }

            $collection->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    private function _bootstrapChildren($collection_id, $status = null, $normalizeAttributes = false){
        $attachments = DB::table('parent_child')->where('parent_id', $collection_id)->orderBy('order', 'asc')->get();
        $return = [];

        for($i = 0; $i<count($attachments); $i++) {
            if($attachments[$i]->child_type == 'element') {
                $element = Element::with('attributes')->status($status)->find($attachments[$i]->child_id);
                if(!is_null($element)) {
                    $element = $element->toArray();
                    if($normalizeAttributes) {
                        $element['attributes'] = $this->_normalizeAttributes($element['attributes']);
                    }
                    $element['component'] = $this->_bootstrapComponent('element', $element['type']);
                    array_push($return, $element);
                }
            } else if($attachments[$i]->child_type == 'collection') {
                $collection = Collection::status($status)->with('attributes')->find($attachments[$i]->child_id);
                if(!is_null($collection)){
                    $collection  = $collection->toArray();
                    if($normalizeAttributes) {
                        $collection['attributes'] = $this->_normalizeAttributes($collection['attributes']);
                    }
                    $collection['component'] = $this->_bootstrapComponent('collection', $collection['type']);
                    $collection['children'] = $this->_bootstrapChildren($collection['id'], $status, $normalizeAttributes);
                    array_push($return, $collection);
                }
            }
        }
        return $return;
    }


    private function _normalizeAttributes($attributes) {
        $returnArray = [];
        foreach($attributes as $attr) {
            $returnArray[$attr['key']] = $attr['value'];
        }
        return $returnArray;
    }


    private function _bootstrapComponent($protoType, $componentType) {
        $proto = ucfirst($protoType).'s';
        $componentArray = [];
        $vendorClassString = "Elemental\\Components\\".$proto."\\".$componentType."Component";
        $appNamespace = $this->getAppNamespace();
        $userClassString = $appNamespace.$vendorClassString; 

        if(class_exists($vendorClassString)) { //check if selected component exists in vendor dir
            $componentClassName = $vendorClassString;
        } 

        if(class_exists($userClassString)) { //check if selected component is a custom user component
            $componentClassName = $userClassString;
        }

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
                if(is_array($component->rules)) {
                    foreach($component->rules as $fieldName => $ruleList) {
                        $rules = explode('|', $ruleList);
                        $componentArray['rules'][$fieldName] = $rules;
                    }
                }
            }
        }
        return $componentArray;
    }   


    private function _determineOrder($collection) {
        $children = $this->_bootstrapChildren($collection->id);
        $order = count($children) + 1;
        return $order;
    }


    private function _findRaw($slug) {
        return Collection::where('slug', $slug)->with('attributes')->first();
    }

}
