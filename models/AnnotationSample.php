<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class AnnotationSample extends Eloquent {

    protected $table = "annotation_sample";
    public $timestamps = false;

    public function _class(){
        return $this->hasOne("TrafficSignClass",'id','class_id');
    }

}