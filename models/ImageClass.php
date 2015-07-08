<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ImageClass extends Eloquent {

    protected $table = "class";

    public function _subclass(){
        return $this->belongsTo("ImageSubclass");
    }
}