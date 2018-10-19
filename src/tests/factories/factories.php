<?php 

$factory('App\Elemental\Core\Element', [
    'nickname' => 'Tester',
    'slug' => 'tester',
    'type' => 'text'
]);


$factory('App\Elemental\Core\ElementAttribute', [
    'element_id' => 'factory:App\Elemental\Core\Element',
    'key' => 'text',
    'value' => 'Foo'
]);

$factory('App\Elemental\Core\Collection', [
    'nickname' => 'Dummy Page',
    'slug' => 'dummy',
    'type' => 'page',
    'reorderable' => false,
    'addable' => false
]);


$factory('App\Elemental\Core\CollectionAttribute', [
    'collection_id' => 'factory:App\Elemental\Core\Collection',
    'key' => 'display_text',
    'value' => 'Dummy Page'
]);


$factory('App\Elemental\Core\Collection', 'child_collection', [
    'nickname' => 'My Gallery',
    'slug' => 'my_gallery',
    'type' => 'gallery',
    'reorderable' => false,
    'addable' => false
]);



?>