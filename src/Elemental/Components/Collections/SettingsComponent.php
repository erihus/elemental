<?php namespace Elemental\Components\Collections;


class SettingsComponent extends BaseCollection {
    
    public function __construct() {
       
       $this->attachablePrototype = "element";
        $this->attachableComponent =  "Setting";
       
    }

}