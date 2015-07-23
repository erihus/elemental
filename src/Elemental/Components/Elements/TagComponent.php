<?php namespace Elemental\Components\Elements;


class TagComponent extends BaseElement {

    public function __construct() {

        $this->fields = [
            'display_title' => 'text',
            'url_slug' => 'text',
        ];

        $this->labels = [
            'display_title' => 'Tag Name',
            'url_slug' =>  'URL Slug',
        ];

        $this->rules = [
            'display_title' => 'required'
        ];



    }
}