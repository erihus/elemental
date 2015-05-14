<?php namespace Elemental\Core\Contracts;


interface CollectionAttributeInterface {

    public function createAndAttach($collection, $attributes);

    public function update($collectionSlug, $input);

    public function delete($collectionSlug);
    
}