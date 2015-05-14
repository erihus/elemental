<?php namespace Elemental\Components\Collections;


class TagsComponent extends BaseCollection {

    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent =  "Tag";

    }
}