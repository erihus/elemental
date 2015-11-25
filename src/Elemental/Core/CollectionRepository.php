<?php namespace Elemental\Core;

use Illuminate\Support\Collection as LaravelCollection;
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
        $collection = Collection::where('slug', $slug)->with('attributes')->first();
        $collection['component'] = $this->_bootstrapComponent('collection', $collection->type);
        $collection['children'] = $this->_bootstrapChildren($collection, false);
        $collection = $collection->toArray();
        return $collection;
    }

    public function findAll($type=null) {
        if(!is_null($type)) {
            $collections = Collection::where('type', ucfirst($type))->with('attributes')->get();
        } else {
            $collections = Collection::with('attributes')->get();
        }

        $colSlugs = [];

        foreach($collections as $collection) {
            array_push($colSlugs, $collection->slug);
        }

        for($i=0; $i<count($collections); $i++) {
            $collections[$i]['component'] = $this->_bootstrapComponent('collection', $collections[$i]->type);
            $collections[$i]['children'] = $this->_bootstrapChildren($collections[$i], false, null, 1);
        
        }

        //Remove attached collections from the top level array
        foreach($collections as $col) {
            foreach($col->children as $child) {
                $childSlug = $child['slug'];
                $key = array_search($childSlug, $colSlugs);
                if($key) {
                    unset($collections[$key]);
                }
            }
          
        }
        $collections = $collections->toArray();

        return $collections;
    }


    public function query($collectionParams, $normalizeAttributes = true, $bootstrapChildren = true, $limit = null) {
        $filteredCollections = Collection::where($collectionParams)->with('attributes');
        if(!is_null($limit)) {
            $filteredCollections->limit($limit);
        }
        $filteredCollections = $filteredCollections->get();

        $results = [];
        //normalize attributes and recursively attach children
        $filteredCollections->each(function($collection) use(&$results, $normalizeAttributes, $bootstrapChildren) {
            if($normalizeAttributes) {
                $normalizedAttrs = $this->_normalizeAttributes($collection);
            }        
            if($bootstrapChildren) {
                $collection->children = $this->_bootstrapChildren($collection, $normalizeAttributes, 'published');
            }

            $collection = $collection->toArray();
            if($normalizeAttributes) {
                $collection['attributes'] = $normalizedAttrs;
            }
            array_push($results, $collection);
        });
   
        return $results;
    }


    public function findByAttribute($collectionParams, $attributeParams, $normalizeAttributes = true, $bootstrapChildren = true, $limit = null, $recursionDepth = null)
    {
        $collections = [];
        $status = null;
        
        $attributes = CollectionAttribute::where('key', $attributeParams['key'])->where('value', $attributeParams['value']);
        if(!is_null($limit)) {
            $attributes->limit($limit);
        }
        $attributes = $attributes->get();
        $attributes->each(function($attribute) use(&$collections) {
            $attribute->load('collection');
            
            $collection = $attribute->collection;
            $collection->load('attributes');
            array_push($collections, $collection);
            
        });
                $filteredCollections = LaravelCollection::make($collections);
        if(!empty($collectionParams)) { //perform collection level filters on attribute search results
            $search = [];
            foreach($collectionParams as $key => $value) {
                $search['key'] = $key;
                $search['value'] = $value;
            }
            if($search['key'] == 'status') {
                $status = $search['value'];
            }
            $filteredCollections = $filteredCollections->where($search['key'], $search['value']);
        }

        $results = [];
        //normalize attributes and recursively attach children
        $filteredCollections->each(function($collection) use(&$results, $normalizeAttributes, $bootstrapChildren, $status, $recursionDepth) {
            if($normalizeAttributes) {
                $normalizedAttrs = $this->_normalizeAttributes($collection);
            }        
            if($bootstrapChildren && $recursionDepth > 0) {
                $collection->children = $this->_bootstrapChildren($collection, $normalizeAttributes, $status, $recursionDepth);
            }

            $collection = $collection->toArray();
            if($normalizeAttributes) {
                $collection['attributes'] = $normalizedAttrs;
            }
            array_push($results, $collection);

        });
   
        return $results;
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
           DB::table('parent_child')
                ->where('parent_id', $collection->id)
                ->where('child_id', $childId)
                ->where('child_type', $childType)
                ->update(['order'=> $childOrder]);
           
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
            
            $attach = $col->elements()->attach($el, ['order' => $order, 'child_type' => 'element']);
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


    private function _bootstrapChildren($collection, $normalizeAttributes = false, $status = null, $recursionDepth = null){
    
        if(!is_null($recursionDepth)) {
            // dump('recursion depth:');
            // dump($recursionDepth);
            if($recursionDepth === 0) {
                return;
            } else {
                $recursionDepth--;
            }
            
        }
        $children = [];
        $collection->load([
            'collections' => function($query) use ($status){
                if(!is_null($status)) {
                    $query->where('status', '=', $status);
                }
        }])->load([
            'elements' => function($query) use ($status) {
                if(!is_null($status)) {
                    $query->where('status', '=', $status);
                }
        }]);

        $collection->elements->each(function($element) use (&$children, $normalizeAttributes) {
            $element->load('attributes');
            if($normalizeAttributes) {
                $normalizedAttrs = $this->_normalizeAttributes($element);
            }
            $element = $element->toArray();
            if($normalizeAttributes) {
                $element['attributes'] = $normalizedAttrs;
            }
            $element['component'] = $this->_bootstrapComponent('element', $element['type']);
            array_push($children, $element);
        });

        $collection->collections->each(function($child) use (&$children, $normalizeAttributes, $status, $recursionDepth) {
            //dump($recursionDepth);
            if($recursionDepth >= 0) {
                $child->load('attributes');
                if($normalizeAttributes) {
                    $normalizedAttrs = $this->_normalizeAttributes($child);
                }
                
                $child->children = $this->_bootstrapChildren($child, $normalizeAttributes, $status, $recursionDepth);
                
                $child = $child->toArray();
                if($normalizeAttributes) {
                    $child['attributes'] = $normalizedAttrs;
                }

                $child['component'] = $this->_bootstrapComponent('collection', $child['type']);
                // dump($child);
                array_push($children, $child);
            }
        });

        //sort according to pivot table order
        $children = LaravelCollection::make($children);
        // $children->sortBy(function($a, $b){
        //     return ($a['pivot']['order'] < $b['pivot']['order']) ? -1 : 1;
        // });
        // dump($children);
        $children = $children->toArray();
        usort($children, function($a, $b){
            return ($a['pivot']['order'] < $b['pivot']['order']) ? -1 : 1;
        });
        return $children;

    }


    private function _normalizeAttributes($collection) {
        $returnArray = [];
        $attributes= $collection->attributes->toArray();
        foreach($attributes as &$attr) {
            
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
        $count = DB::table('parent_child')->where('parent_id', $collection->id)->count();
        $order = $count + 1;
        return $order;
    }


    private function _findRaw($slug) {
        return Collection::where('slug', $slug)->with('attributes')->first();
    }

}
