<?php namespace Elemental\Components\Collections;


class ResponsiveGalleryComponent extends BaseCollection {
    
    public function __construct() {

        $this->attachablePrototype = "element";
        $this->attachableComponent = "ResponsiveImage";

        $this->fields = [            
            'attachable_sml_width' => 'meta',
            'attachable_sml_height' => 'meta',
            'attachable_med_width' => 'meta',
            'attachable_med_height' => 'meta',
            'attachable_lrg_width' => 'meta',
            'attachable_lrg_height' => 'meta'
        ];

        $this->generator_rules = [
            'attachable_sml_width' => 'sometimes|required',
            'attachable_sml_height' => 'sometimes|required',
            'attachable_med_width' => 'sometimes|required',
            'attachable_med_height' => 'sometimes|required',
            'attachable_lrg_width' => 'sometimes|required',
            'attachable_lrg_height' => 'sometimes|required',
        ];
    }

}