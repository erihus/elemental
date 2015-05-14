<?php namespace Elemental\Core\Contracts;


interface HubInterface {
    
    /**
     * Create a new entity and attach valid attributes
     *
     * @return boolean
     */
    public function create($input);


    /**
     * Create a new entity and attach valid attributes
     *
     * @return array
     */
    public function read($slug);


     /**
     * Show all the entities
     *
     * @return array
     */
    public function readAll($type=null);



     /**
     * Create copy of an entity and attributes
     *
     * @return boolean
     */
    public function copy($sourceSlug, $newNickname, $newSlug);


    /**
     * Update an existing entity with valid attributes
     *
     * @return boolean
     */
    public function update($slug, $updates);



    /**
     * Update an existing entity's nickname or slug
     *
     * @return boolean
     */
    public function updateMeta($slug, $updates);


    /**
     * Delete an entity and its attribtes
     *
     * @return boolean
     */
    public function delete($slug);


    /**
     * Attach an entity to another entity
     *
     * @return boolean
     */
    public function attach($childSlug, $parentSlug);


    /**
     * Detach an entity from another entity
     *
     * @return boolean
     */
    public function detach($childSlug, $parentSlug);


    /**
     * Fetch error messages
     *
     * @return array
     */
    public function errors();


}

