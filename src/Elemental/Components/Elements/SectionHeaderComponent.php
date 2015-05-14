<?php namespace Elemental\Components\Elements;


class SectionHeaderComponent extends BaseElement {
    

     public function __construct() {
        
        $this->normalName = "Section Header";

        $this->fields = [
            'headline' => 'text',
            'lede' => 'textarea'
        ];

        $this->labels = [
            'headline' => 'Headline',
            'lede' => 'Lede Text'
        ];

    }

}