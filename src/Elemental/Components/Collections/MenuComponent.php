<?php namespace Elemental\Components\Collections;


class MenuComponent extends BaseCollection {
    
    public function __construct() {
        
        $this->normalName = "Menu";
        $this->attachablePrototype = ["collection", "element"];
        $this->attachableComponent = ["SubMenu", "MenuItem"];

    }

}