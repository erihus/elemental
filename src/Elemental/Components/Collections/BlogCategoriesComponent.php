<?php namespace Elemental\Components\Collections;


class BlogCategoriesComponent extends BaseCollection {

    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent =  "BlogCategory";

    }
}