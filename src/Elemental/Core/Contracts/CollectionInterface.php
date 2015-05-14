<?php namespace Elemental\Core\Contracts;


interface CollectionInterface {

    public function create(array $input);

    public function find($slug);

    public function edit($slug, $input);

    public function order($slug, $childType, $childId, $childOrder);

    public function findAll();

    public function attachElement($elementSlug, $collectionSlug);

    public function detachElement($elementSlug, $collectionSlug);

    public function attachCollection($childSlug, $parentSlug);

    public function detachCollection($childSlug, $parentSlug);

    public function fetch_elements($slug);

    public function fetch_children($slug);

    public function delete($slug);

}