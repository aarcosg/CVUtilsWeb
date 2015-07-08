<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ImageSubclass extends Eloquent {

    protected $table = "subclass";
    public $timestamps = false;

    public function _class(){
        return $this->belongsTo("ImageClass");
    }
}