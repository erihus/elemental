<?php namespace Elemental\Components\Collections;


class PageComponent extends BaseCollection {

    public function __construct() {
        
        $this->fields = [
            'url_slug' => 'text',
            'display_title' => 'text',
            'meta_title' => 'text',
            'meta_description' => 'textarea',
        ];

        $this->labels = [
            'url_slug' => 'URL Slug',
            'display_title' => 'Title',
            'meta_title' => 'Meta Title',
            'meta_description' => 'Meta Description',
        ];


        $this->rules = [
            'display_title' => 'required',
            'url_slug' => 'required'
        ];

    }

}