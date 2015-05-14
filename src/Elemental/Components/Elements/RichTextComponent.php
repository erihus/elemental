<?php namespace Elemental\Components\Elements;


class RichTextComponent extends BaseElement {
    

     public function __construct() {
        
        $this->normalName = "Rich Text";

        $this->fields = [
            'text' => 'wysiwyg'
        ];

        $this->labels = [
            'text' => 'Text'
        ];

    }

}