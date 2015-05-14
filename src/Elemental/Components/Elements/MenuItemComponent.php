<?php namespace Elemental\Components\Elements;


class MenuItemComponent extends BaseElement {
    
    public function __construct() {

        $this->normalName = "Menu Item";
        
        $this->fields = [
            'title' => 'text',
            'link_type' => 'radio',
            'link_internal' => 'select',
            'link_external' => 'text'
        ];

        $this->labels = [
            'title' => 'Title',
            'link_type' => 'Link Type',
            'link_internal' => 'Page Link',
            'link_external' => 'External Link'
        ];

        $this->rules = [
            'title' => 'required',
            'link_type' => 'required',
            'link_external' => 'url'
        ];

        $this->options = [
            'link_type' => [
                ['name' => 'Internal', 'value' => 'internal'], 
                ['name' => 'External', 'value' => 'external']
            ],
            'link_internal' => 'api/collection/type/page'
        ];

    }

}