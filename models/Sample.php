<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Sample extends Eloquent {

    protected $table = "sample";

    public function _class(){
        return $this->hasOne("ImageClass",'id','class_id');
    }

}