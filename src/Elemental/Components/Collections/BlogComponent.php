<?php namespace Elemental\Components\Collections;


class BlogComponent extends PageComponent {

    public function __construct() {

        parent::__construct();

        $this->attachablePrototype = "collection";
        $this->attachableComponent = ["BlogCategories", "BlogPosts"];

    }
}