<?php namespace Elemental\Components\Collections;


abstract class BaseCollection {
    public $prototype = 'collection';
    public $attachablePrototype;
    public $attachableComponent;
    public $attachableAddable;
    public $attachableReorderable;
    public $fields = [];
    public $labels = [];
    public $options = [];
    public $rules = []; 
    public $generator_rules = [];
    public $slug_generator = [];
    public $childThumbView;
    public $childThumbField;
    public $batchCreate;
    public $batchCreateFields = [];
    public $batchCreateLabels = [];
}


