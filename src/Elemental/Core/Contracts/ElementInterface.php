<?php namespace Elemental\Core\Contracts;


interface ElementInterface {

    public function create(array $input);

    public function find($slug);

    public function edit($slug, $input);

    public function findAll();

    public function delete($slug);

}