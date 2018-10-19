<?php

use Laracasts\TestDummy\Factory;
use Laracasts\TestDummy\DbTestCase;
use App\Services\ElementService;
use App\Elemental\Core\ElementInterface;
use App\Elemental\Core\CollectionInterface;
use App\Elemental\Core\CollectionAttributeInterface;
use App\Elemental\Core\Validator;

class CollectionServiceTest extends DbTestCase {


    protected $service;
    protected $validator;
    protected $element;
    protected $collection;
    protected $attributes;
    protected $dummyElement;


    public function mock($class) {
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);
        return $mock;
    }

    public function setUp() {
        parent::setUp();

        $this->validator = $this->mock('App\Elemental\Core\Validator');
        $this->element = $this->mock('App\Elemental\Core\ElementRepository');
        $this->collection = $this->mock('App\Elemental\Core\CollectionRepository');
        $this->attributes = $this->mock('App\Elemental\Core\CollectionAttributeRepository');
        $this->service = new App\Elemental\Services\CollectionService($this->validator, $this->collection, $this->element, $this->attributes);

        $this->dummyElement = [
           "id"         => "1",
           "nickname"   => "Test",
           "slug"       => "test",
           "type"       => "page",
           "created_at" => "2015-02-19 16:40:41",
           "updated_at" => "2015-02-19 16:40:41",
           "deleted_at" => null
       ];

    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();
    }


    public function test_it_validates_input_and_creates_collections_and_attributes_based_on_component_class()
    {
    
        $input =[
            'nickname' => 'Tester',
            'slug' => 'tester',
            'type' => 'page',
            'reorderable' => false,
            'addable' => false,
            'attributes' => [
                'display_title' => 'Home',
                'meta_title' => 'Home Page',
                'meta_description' => 'This is a page'
            ]
        ];

        
        $this->validator->shouldReceive('run')
        ->once()
        ->with('collection', 'page', $input['attributes'], true)
        ->andReturn(true);
        
        $this->collection->shouldReceive('create')
        ->once()
        ->with(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type'], 'reorderable' => $input['reorderable'], 'addable' => $input['addable']])
        ->andReturn(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type'], 'reorderable' => $input['reorderable'], 'addable' => $input['addable']]);
        
        $this->attributes->shouldReceive('createAndAttach')
        ->once()
        ->with('tester', $input['attributes'])
        ->andReturn(true);
        
        $this->assertTrue($this->service->create($input));

    }


    function test_it_provides_an_error_array_accessor_if_validation_fails() {
        $input = [
            'nickname' => 'Tester',
            'slug' => 'tester',
            'type' => 'page',
            'attributes' => []
        ];

        $this->validator->shouldReceive('run')
        ->with('collection', 'page', $input['attributes'], true)
        ->andReturn(false);


        $this->validator->shouldReceive('getErrors')
        ->once()
        ->andReturn(['display_title' => 'Field required']);

        $this->assertFalse($this->service->create($input));
        $errors = $this->service->errors();
        $this->assertArrayHasKey('display_title', $errors[0]);
        
    }

   
     public function test_it_validates_input_and_updates_attributes() {       
        $dummy = Factory::create('App\Elemental\Core\CollectionAttribute');
        $col = $dummy->collection->load('attributes');
        $colArray = $col->toArray();

        $slug = 'tester';
        $updates = [
            'display_title' => 'My Page'
        ];

        $this->collection->shouldReceive('find')
        ->once()
        ->andReturn($colArray);

        $this->validator->shouldReceive('run')
        ->once()
        ->with('collection', 'page', $updates, true)
        ->andReturn(true);

        $this->attributes->shouldReceive('update')
        ->with($slug, $updates)
        ->once()
        ->andReturn(true);

        $this->assertTrue($this->service->update($slug, $updates));
        
     }


     public function test_it_updates_a_collections_slug_or_nickname()
     {
        $slug = 'test';
        $input = [
            'slug' => 'foo',
            'nickname' => "New Nickname"
        ];

        $this->collection->shouldReceive('edit')
        ->once()
        ->with($slug, $input)
        ->andReturn(true);

        $this->assertTrue($this->service->updateMeta($slug, $input));
     }


     public function test_it_fetches_a_collection_and_its_attributes()
     {
        
        $dummy = Factory::create('App\Elemental\Core\CollectionAttribute');
        $dummy->load('collection');
        $col = $dummy->collection->load('attributes');
        $colArray = $col->toArray();
        $slug = $col->slug;        
        $this->collection->shouldReceive('find')->once()->andReturn($colArray);
        $response = $this->service->read($slug);
        $this->assertContains($colArray['nickname'], $response);
        $this->assertArrayHasKey('attributes', $response);
     }



     public function test_it_clones_a_collection_and_its_attributes()
     {
        $dummy = Factory::create('App\Elemental\Core\CollectionAttribute');
        $dummy->load('collection');
        $col = $dummy->collection->load('attributes');
        $colArray = $col->toArray();
        $sourceSlug = $col['slug'];
        
        $newNickname = 'Test Clone';
        $newSlug = 'test_clone';
        $newInput = [
            'nickname' => $newNickname,
            'slug' => $newSlug,
            'type' => $colArray['type'],
            'reorderable' => false,
            'addable' => false
        ];
        $newAttributes = [];
        foreach ($colArray['attributes'] as $attr) {
            $newAttributes[$attr['key']] = $attr['value'];
        }

        $this->collection->shouldReceive('find')
        ->once()
        ->with($sourceSlug)
        ->andReturn($col->toArray());

        $this->collection->shouldReceive('create')
        ->once()
        ->with($newInput)
        ->andReturn($newInput);
        

        $this->attributes->shouldReceive('createAndAttach')
        ->once()
        ->with('test_clone', $newAttributes)
        ->andReturn(true);

        $this->assertTrue($this->service->copy($sourceSlug, $newNickname, $newSlug));

     }



     // public function test_it_optionally_clones_all_attached_elements_and_child_collections()
     // {
     //    //generate dummy collection to clone
     //    $dummy = Factory::Create('App\Elemental\Core\CollectionAttribute');
     //    $dummy->load('collection');
     //    $col = $dummy->collection->load('attributes');
     //    $colArray = $col->toArray();
     //    $sourceSlug = $col['slug'];

     //    //generate a dummy child collection
     //    //$childCollection = Factory::create('child_collection');

     //    //setup input for creating clone
     //    $newNickname = 'Test Clone';
     //    $newSlug = 'test_clone';
     //    $newInput = [
     //        'nickname' => $newNickname,
     //        'slug' => $newSlug,
     //        'type' => $colArray['type'],
     //        'reorderable' => $colArray['reorderable'],
     //        'addable' => $colArray['addable']
     //    ];
     //    $newAttributes = [];
     //    foreach ($colArray['attributes'] as $attr) {
     //        $newAttributes[$attr['key']] = $attr['value'];
     //    }


     //    //generate an element
     //    $elAttr= Factory::create('App\Elemental\Core\ElementAttribute');
     //    $elAttr->load('element');
     //    $el = $elAttr->element->load('attributes');
     //    $elArray = $el->toArray();
     //    $makeElArray = $elArray;
     //    unset($makeElArray['attributes']);

     //    //Looks up the collection
     //    $this->collection->shouldReceive('find')
     //    ->once()
     //    ->with($sourceSlug)
     //    ->andReturn($col->toArray());

     //    //Creates a new  Collection
     //    $this->collection->shouldReceive('create')
     //    ->once()
     //    ->with($newInput)
     //    ->andReturn($newInput);

     //    //Creates copies of all the collection attributes
     //    $this->attributes->shouldReceive('createAndAttach')
     //    ->once()
     //    ->with('test_clone', $newAttributes)
     //    ->andReturn(true);
        

     //    //Fetches attached elements
     //    $this->collection->shouldReceive('fetch_elements')
     //    ->once()
     //    ->with($sourceSlug)
     //    ->andReturn([$elArray]);

     //    //fetches attached child collections
     //    $this->collection->shouldReceive('fetch_children')
     //    ->once()
     //    ->with($sourceSlug)
     //    ->andReturn([]);

    
     //    //creates copies of the elements
     //    $this->element->shouldReceive('find')
     //    ->once()
     //    ->with($elArray['slug'])
     //    ->andReturn($elArray);

     //    $this->element->shouldReceive('create')
     //    ->once()
     //    ->andReturn($elArray);

     //    //looks up element for attachment rules
     //    $this->element->shouldReceive('find')
     //    ->once()
     //    ->with($elArray['slug'])
     //    ->andReturn($elArray);

     //    //attaches Elements to the clone parent
     //    $this->collection->shouldReceive('attachElement')
     //    ->once()
     //    ->andReturn(true);

     
     //    //subroutines to clone children and attach them to parent clone
     //    $this->collection->shouldReceive('find')
     //    ->once()
     //    ->andReturn([]);

     //    $this->assertTrue($this->service->copy($sourceSlug, $newNickname, $newSlug, true));

     // }


     public function test_it_delete_a_collection_and_associated_attributes() {
        $slug = 'test';

        $this->collection->shouldReceive('delete')
        ->once()
        ->with($slug)
        ->andReturn(true);

        $this->attributes->shouldReceive('delete')
        ->once()
        ->with($slug)
        ->andReturn(true);

        $this->assertTrue($this->service->delete($slug));
     }


     public function test_it_attaches_a_collection_to_another_collection() {
         $parent = Factory::create('App\Elemental\Core\Collection');
         $child = Factory::create('child_collection');

         $this->collection->shouldReceive('find')
         ->once()
         ->with($parent->slug)
         ->andReturn($parent->toArray());

          $this->collection->shouldReceive('find')
         ->once()
         ->with($child->slug)
         ->andReturn($child->toArray());

         $this->collection->shouldReceive('attachCollection')
         ->once()
         ->with($child->slug, $parent->slug)
         ->andReturn(true);

         $this->assertTrue($this->service->attach($child->slug, $parent->slug));
     }

     public function test_it_detaches_a_collection_from_another_collection() {
        $parent = 'foo';
        $child = 'bar';

        $this->collection->shouldReceive('detachCollection')
        ->once()
        ->with($child, $parent)
        ->andReturn(true);

        $this->assertTrue($this->service->detach($child, $parent));
     }

}
