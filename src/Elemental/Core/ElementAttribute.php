<?php namespace Elemental\Core;

use Illuminate\Database\Eloquent\Model;

class ElementAttribute extends Model {

	public function element()
    {
        return $this->belongsTo('Elemental\Core\Element');
    }

}
