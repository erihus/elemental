<?php namespace Elemental\Core;

use Illuminate\Database\Eloquent\Model;

class CollectionAttribute extends Model {

	public function collection()
    {
        return $this->belongsTo('Elemental\Core\Collection');
    }

}
