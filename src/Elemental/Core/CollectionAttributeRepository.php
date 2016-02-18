<?php namespace Elemental\Core;


use Elemental\Core\Contracts\CollectionAttributeInterface;
use Elemental\Core\Collection;
use Elemental\Core\CollectionAttribute;
use Elemental\Events\CMSContentSaved;
use Event;


class CollectionAttributeRepository implements CollectionAttributeInterface {

    public function createAndAttach($collectionSlug, $attributes) {
         try{
            $collection = $this->_fetchCollection($collectionSlug);
            $attributeModels =[];
            foreach($attributes as $key => $val) {
                $attr = new CollectionAttribute;
                $attr->key = $key;
                 if(is_array($val)) {
                    $val = implode(',', $val);
                }
                $attr->value = $val;
                array_push($attributeModels, $attr);
            }

            $collection->attributes()->saveMany($attributeModels);
            Event::fire(new CMSContentSaved(['prototype' => 'collection', 'type'=>$collection->type, 'slug' => $collection->slug, 'action' => 'update']));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($collectionSlug, $input)
    {
        try {
            $collection = $this->_fetchCollection($collectionSlug);
            foreach($input as $key => $val) {
                $attribute = $this->_fetchAttribute($collection, $key);

                if(is_null($attribute)) { // if attribute does not exist on the colleciton  try to create it
                    if($this->createAndAttach($collectionSlug, [$key => $val])) {
                        $attribute = $this->_fetchAttribute($collection, $key);
                    } else {
                        //die('Could not add new attribute');
                        throw new Exception('Could not add new attribute');
                        return false;
                    }
                }

                if(is_array($val)) {
                    $val = implode(',', $val);
                }
                $attribute->value = $val;
                $attribute->save();
            }
            Event::fire(new CMSContentSaved(['prototype' => 'collection', 'type'=>$collection->type, 'slug' => $collection->slug, 'action' => 'update']));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($collectionSlug)
    {
        try{
            $collection = $this->_fetchCollection($collectionSlug);
            Event::fire(new CMSContentSaved(['prototype' => 'collection', 'type'=>$collection->type, 'slug' => $collection->slug, 'action' => 'delete']));
            $collection->attributes()->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    private function  _fetchCollection($slug) {
        return Collection::where('slug', $slug)->first();
    }

    private function _fetchAttribute($collection, $attributeKey) {
        return CollectionAttribute::where('collection_id', $collection->id)->where('key', $attributeKey)->first();
    }   

}
