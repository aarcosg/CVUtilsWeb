<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class TrafficSignClass extends Eloquent {

    protected $table = "traffic_sign_class";
    public $timestamps = false;

    public function _subclass(){
        return $this->belongsTo("TrafficSignSubclass");
    }
}