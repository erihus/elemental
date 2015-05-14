<?php namespace Elemental\Core;

use Illuminate\Database\Eloquent\Model;

class Element extends Model {

    protected $fillable = ['nickname', 'slug', 'type', 'status'];

	public function attributes() {
        return $this->hasMany('Elemental\Core\ElementAttribute');
    }

    public function collections() {
        return $this->belongsToMany('Elemental\Core\Collection', 'parent_child',  'parent_id', 'child_id')->withPivot('order')->withPivot('child_type');
    }

    public function scopeStatus($query, $status){
        if (!is_null($status)) {
           return $query->whereStatus($status);
        }
        return $query;
    }

}
