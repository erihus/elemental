<?php namespace Elemental\Components\Collections;


class RedirectsComponent extends BaseCollection {

    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent =  "Redirect";

    }
}