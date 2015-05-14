<?php namespace Elemental\Components\Collections;


class SubMenuComponent extends BaseCollection {
    
    public function __construct() {

        $this->normalName = "Submenu";
        $this->attachablePrototype = "element";
        $this->attachableComponent = "MenuItem";

        $this->fields = [
            'title' => 'text',
            'link' => 'select',
        ];

        $this->labels = [
            'title' => 'Title',
            'link' => 'Page Link',
        ];

        $this->rules = [
            'title' => 'required',
            'link' => 'required'
        ];

        $this->options = [
            'link' => 'api/collection/type/page'
        ];

    }

}