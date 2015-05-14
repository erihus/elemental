<?php namespace Elemental\Components\Elements;


class TagComponent extends BaseElement {

    public function __construct() {

        $this->fields = [
            'title' => 'text',
            'url_slug' => 'text',
        ];

        $this->labels = [
            'title' => 'Tag Name',
            'url_slug' =>  'URL Slug',
        ];

        $this->rules = [
            'title' => 'required'
        ];



    }
}