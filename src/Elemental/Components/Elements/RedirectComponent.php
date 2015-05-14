<?php namespace Elemental\Components\Elements;


class RedirectComponent extends BaseElement {

    public function __construct() {

        $this->fields = [
            'redirect_from' => 'text',
            'redirect_to' => 'text',
            'status_code' => 'text',
        ];

        $this->labels = [
            'redirect_from' => 'Redirect From (Format: some/url/path/file)',
            'redirect_to' => 'Redirect To (Format: redirect/to-here)',
            'status_code' => 'Status Code (eg, 301 or 302)',
        ];

        $this->rules = [
            'redirect_from' => 'required',
            'redirect_to' => 'required',
            'status_code' => 'required|numeric'
        ];


    }
}