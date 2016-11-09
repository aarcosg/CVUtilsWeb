<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ClassifyManySample extends Eloquent {

    protected $table = "classify_many_sample";
    public $timestamps = false;

    public function _class(){
        return $this->hasOne("TrafficSignClass",'id','class_id');
    }

    public function _classify_many(){
        return $this->hasOne("ClassifyMany",'id','classify_many_id');
    }

}