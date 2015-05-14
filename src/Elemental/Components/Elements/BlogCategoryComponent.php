<?php namespace Elemental\Components\Elements;


class BlogCategoryComponent extends BaseElement {

    public function __construct() {

        $this->fields = [
            'title' => 'text',
            'url_slug' => 'text'
        ];

        $this->labels = [
            'title' => 'Category Title',
            'url_slug' => "URL Slug"
        ];

         

    }
}