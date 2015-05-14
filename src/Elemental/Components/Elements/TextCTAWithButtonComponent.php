<?php namespace Elemental\Components\Elements;


class TextCTAWithButtonComponent extends BaseElement {

    public function __construct() {
        $this->normalName = "Call To Action";
    
        $this->fields = [
            'headline' => 'text',
            'blurb' => 'textarea',
            'button_text' => 'text',
            'button_link_type' => 'radio',
            'button_link_internal' => 'select',
            'button_link_external' => 'text'
        ];

        $this->rules = [
            'headline' => 'required',
            'button_link_external' => 'url'            
        ];

        $this->labels = [
            'headline' => 'Headline',
            'blurb' => 'Body',
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

        
    }
}