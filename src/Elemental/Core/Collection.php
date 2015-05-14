<?php namespace Elemental\Core;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model {

    protected $fillable = ['nickname', 'slug', 'type', 'reorderable', 'addable', 'status'];

	public function elements() {
        return $this->belongsToMany('Elemental\Core\Element', 'parent_child', 'parent_id', 'child_id')->withPivot('order')->withPivot('child_type'); //belongsToMany looks weird here but its necessary to get our pivot table to work
    }

    public function attributes() {
        return $this->hasMany('Elemental\Core\CollectionAttribute');
    }

    public function collections() {
        return $this->belongsToMany('Elemental\Core\Collection', 'parent_child', 'parent_id', 'child_id')->withPivot('order')->withPivot('child_type'); //belongsToMany looks weird here but its necessary to get our pivot table to work
    }

    public function scopeStatus($query, $status){
        if (!is_null($status)) {
           return $query->whereStatus($status);
        }
        return $query;
    }

}
