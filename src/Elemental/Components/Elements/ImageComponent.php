<?php namespace Elemental\Components\Elements;


class ImageComponent extends BaseElement {

    public function __construct() {
        $this->normalName = "Image";

        $this->fields = [
            'width' => 'meta',
            'height' => 'meta',
            'path' => 'image',
            'alt_text' => 'text',
            'link' => 'text'
        ];

         $this->rules = [
            'path' => 'required',            
        ];

        $this->generator_rules = [
            'width' => 'sometimes|required',
            'height' => 'sometimes|required',
        ];

        $this->labels = [
            'path' => 'Image (Recommended File Type: .jpg or .png)',
            'alt_text' => 'Alt Text',
            'link' => 'Link (optional)'
        ];
    }

}