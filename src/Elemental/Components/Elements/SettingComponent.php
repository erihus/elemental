<?php namespace Elemental\Components\Elements;


class SettingComponent extends BaseElement {
    

     public function __construct() {
        
        $this->normalName = "Plain Text";

        $this->fields = [
            'setting' => 'textarea'
        ];

        $this->labels = [
            'setting' => 'Setting Value'
        ];

        $this->rules = [
            'setting' => 'required'
        ];

    }

}