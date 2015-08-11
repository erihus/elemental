<?php namespace Elemental\Core;


use Elemental\Core\Contracts\ElementAttributeInterface;
use Elemental\Core\Element;
use Elemental\Core\ElementAttribute;


class ElementAttributeRepository implements ElementAttributeInterface {

    public function createAndAttach($elementSlug, $attributes) {
         try{
            $element = $this->_fetchElement($elementSlug);
            foreach($attributes as $key => $val) {
                $attr = new ElementAttribute;
                $attr->key = $key;
                if(is_array($val)) {
                    $val = implode(',', $val);
                }
                $attr->value = $val;
                $element->attributes()->save($attr);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($elementSlug, $input)
    {
        try {
            $element = $this->_fetchElement($elementSlug);
            foreach($input as $key => $val) {
                $attribute = $this->_fetchAttribute($element, $key);

                if(is_null($attribute)) { // if attribute does not exist on the colleciton  try to create it
                    if($this->createAndAttach($elementSlug, [$key => $val])) {
                        $attribute = $this->_fetchAttribute($element, $key);
                    } else {
                        die('Could not add new attribute');
                    }
                }

                if(is_array($val)) {
                    $val = implode(',', $val);
                }
                $attribute->value = $val;
                $attribute->save();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($elementSlug)
    {
        try{
            $element = $this->_fetchElement($elementSlug);
            $element->attributes()->delete();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    private function  _fetchElement($slug) {
        return Element::where('slug', $slug)->first();
    }

    private function _fetchAttribute($element, $attributeKey) {
        return ElementAttribute::where('element_id', $element->id)->where('key', $attributeKey)->first();
    }   

}
