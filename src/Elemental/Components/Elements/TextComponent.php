<?php namespace Elemental\Components\Elements;


class TextComponent extends BaseElement {
    

     public function __construct() {
        
        $this->normalName = "Plain Text";

        $this->fields = [
            'text' => 'textarea'
        ];

        $this->labels = [
            'text' => 'Text'
        ];

    }

}