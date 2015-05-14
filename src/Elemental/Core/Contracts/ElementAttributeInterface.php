<?php namespace Elemental\Core\Contracts;


interface ElementAttributeInterface {

    public function createAndAttach($element, $attributes);

    public function update($elementSlug, $input);

    public function delete($elementSlug);
    
}