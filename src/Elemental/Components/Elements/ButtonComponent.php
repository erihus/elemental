<?php namespace Elemental\Components\Elements;


class ButtonComponent extends BaseElement {

    public function __construct() {
        $this->fields = [
            'button_text' => 'text',
            'button_link_type' => 'radio',
            'button_link_internal' => 'select',
            'button_link_external' => 'text'
        ];

        $this->rules = [
            'button_text' => 'required',
            'button_link_external' => 'url'            
        ];

        $this->labels = [
            'button_text' => 'Button Text',
            'button_link_type' => 'Button Link Type',
            'button_link_internal' => 'Button Internal Link',
            'button_link_external' => 'Button External Link'
        ];

        $this->options = [
            'button_link_type' => [
                ['name' => 'Internal', 'value' => 'internal'], 
                ['name' => 'External', 'value' => 'external']
            ],
            'button_link_internal' => 'api/collection/type/page'
        ];

        
    }
}