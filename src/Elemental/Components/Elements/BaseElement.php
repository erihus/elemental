<?php namespace Elemental\Components\Elements;


abstract class BaseElement {
    
    public $prototype = 'element';
    public $fields = [];
    public $labels = [];
    public $options = [];
    public $rules = []; 
    public $generator_rules = [];
    public $slug_generator = [];

}