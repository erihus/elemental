<?php namespace Elemental\Components\Collections;


class BlogPostsComponent extends BaseCollection {

    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent =  "BlogPost";

        $this->fields = [
            'attachable_width' => 'meta',
            'attachable_height' => 'meta'
        ];
    }
}