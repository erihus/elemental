<?php namespace Elemental\Components\Collections;


class GalleryComponent extends BaseCollection {
    
    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent = "Image";

        $this->fields = [
            'attachable_width' => 'meta',
            'attachable_height' => 'meta',
        ];

        $this->generator_rules = [
            'attachable_width' => 'sometimes|required',
            'attachable_height' => 'sometimes|required',
        ];
    }

}