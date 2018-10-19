<?php

use Laracasts\TestDummy\Factory;
use Laracasts\TestDummy\DbTestCase;
use App\Services\ElementService;
use App\Elemental\Core\ElementInterface;
use App\Elemental\Core\ElementAttributeInterface;
use App\Elemental\Core\CollectionInterface;
use App\Elemental\Core\CollectionAttributeInterface;
use App\Elemental\Core\Validator;

class ElementServiceTest extends DbTestCase {


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
        $this->attributes = $this->mock('App\Elemental\Core\ElementAttributeRepository');
        $this->service = new App\Elemental\Services\ElementService($this->validator, $this->element, $this->collection, $this->attributes);

        $this->dummyElement = [
           "id"         => "1",
           "nickname"   => "Test",
           "slug"       => "test",
           "type"       => "text",
           "created_at" => "2015-02-19 16:40:41",
           "updated_at" => "2015-02-19 16:40:41",
           "deleted_at" => null
       ];

    }

    public function tearDown() {
        parent::tearDown();
        Mockery::close();
    }


    public function test_it_validates_input_and_creates_elements__and_attributes_based_on_component_class()
    {
    
        $input =[
            'nickname' => 'Tester',
            'slug' => 'tester',
            'type' => 'text',
            'attributes' => [
                'text' => 'Foo'
            ]
        ];

        
        $this->validator->shouldReceive('run')
        ->once()
        ->with('element', 'text', $input['attributes'], true)
        ->andReturn(true);
        
        $this->element->shouldReceive('create')
        ->once()
        ->with(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type']])
        ->andReturn(['nickname' => $input['nickname'], 'slug' => $input['slug'], 'type' => $input['type']]);
        
        $this->attributes->shouldReceive('createAndAttach')
        ->once()
        ->with('tester', $input['attributes'])
        ->andReturn(true);
        
        $this->assertTrue($this->service->create($input));

    }


    function test_it_provides_an_error_array_accessor_if_validation_fails() {
        $input =[
            'nickname' => 'Tester',
            'slug' => 'tester',
            'type' => 'image',
            'attributes' => []
        ];

        $this->validator->shouldReceive('run')
        ->once()
        ->andReturn(false);


        $this->validator->shouldReceive('getErrors')
        ->once()
        ->andReturn(['field_name' => 'Field required']);

        $this->assertFalse($this->service->create($input));
        $errors = $this->service->errors();
        $this->assertArrayHasKey('field_name', $errors[0]);
        
    }

   
     public function test_it_validates_input_and_updates_attributes() {
        $slug = 'test';
        $input = [
            'text' => 'Bar'
        ];
        
        $this->element->shouldReceive('find')
        ->once()
        ->andReturn($this->dummyElement);

        $this->validator->shouldReceive('run')
        ->once()
        ->with('element', 'text', $input, true)
        ->andReturn(true);

        $this->attributes->shouldReceive('update')
        ->with($slug, $input)
        ->once()
        ->andReturn(true);

        $this->assertTrue($this->service->update($slug, $input));
        
     }



     public function test_it_updates_an_elements_slug_or_nickname()
     {
        $slug = 'test';
        $input = [
            'slug' => 'foo',
            'nickname' => "New Nickname"
        ];

        $this->element->shouldReceive('edit')
        ->once()
        ->with($slug, $input)
        ->andReturn(true);

        $this->assertTrue($this->service->updateMeta($slug, $input));
     }


     public function test_it_fetches_an_element_and_its_attributes()
     {
        
        $dummy = Factory::create('App\Elemental\Core\ElementAttribute');
        $dummy->load('element');
        $el = $dummy->element->load('attributes');
        $slug = $el->slug;        
        $this->element->shouldReceive('find')->once()->andReturn($el->toArray());
        $response = $this->service->read($slug);
        $this->assertArrayHasKey('attributes', $response);
     }



     public function test_it_copies_an_element_and_its_attributes()
     {
        $dummy = Factory::create('App\Elemental\Core\ElementAttribute');
        $dummy->load('element');
        $el = $dummy->element->load('attributes');
        $elArray = $el->toArray();
        $sourceSlug = $el['slug'];
        
        $newNickname = 'Test Clone';
        $newSlug = 'test_clone';
        $newInput = [
            'nickname' => $newNickname,
            'slug' => $newSlug,
            'type' => $elArray['type']
        ];
        $newAttributes = [];
        foreach ($elArray['attributes'] as $attr) {
            $newAttributes[$attr['key']] = $attr['value'];
        }

        $this->element->shouldReceive('find')
        ->once()
        ->with($sourceSlug)
        ->andReturn($el->toArray());

        $this->element->shouldReceive('create')
        ->once()
        ->with($newInput)
        ->andReturn($newInput);
        

        $this->attributes->shouldReceive('createAndAttach')
        ->once()
        ->with('test_clone', $newAttributes)
        ->andReturn(true);

        $this->assertTrue($this->service->copy($sourceSlug, $newNickname, $newSlug));

     }


     public function test_it_deletes_elements_and_associated_attributes() {
        $slug = 'test';

        $this->element->shouldReceive('delete')
        ->once()
        ->with($slug)
        ->andReturn(true);

        $this->attributes->shouldReceive('delete')
        ->once()
        ->with($slug)
        ->andReturn(true);

        $this->assertTrue($this->service->delete($slug));
     }


     public function test_it_attaches_an_element_to_a_collection() {
        $dummyElAttr = Factory::create('App\Elemental\Core\ElementAttribute');
        $dummyElAttr->load('element');
        $el = $dummyElAttr->element;

        $dummyColAttr = Factory::create('App\Elemental\Core\CollectionAttribute');
        $dummyColAttr->load('collection');
        $col = $dummyColAttr->collection;

        $this->collection->shouldReceive('find')
        ->once()
        ->with($col->slug)
        ->andReturn($col->toArray());

        $this->element->shouldReceive('find')
        ->once()
        ->with($el->slug)
        ->andReturn($el->toArray());

        $this->collection->shouldReceive('attachElement')
        ->once()
        ->with($el->slug, $col->slug)
        ->andReturn(true);

        $this->assertTrue($this->service->attach($el->slug, $col->slug));

     }


     // public function test_it_detaches_an_element_from_a_collection() {
     //   $dummyElAttr = Factory::create('App\Elemental\Core\ElementAttribute');
     //    $dummyElAttr->load('element');
     //    $el = $dummyElAttr->element;

     //    $dummyColAttr = Factory::create('App\Elemental\Core\CollectionAttribute');
     //    $dummyColAttr->load('collection');
     //    $col = $dummyColAttr->collection;

     //    $this->collection->shouldReceive('detachElement')
     //    ->once()
     //    ->with($el->slug, $col->slug)
     //    ->andReturn(true);

     //    $this->assertTrue($this->service->detach($el->slug, $col->slug));
     // }


}
