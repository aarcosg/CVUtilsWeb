<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class TrafficSignSubclass extends Eloquent {

    protected $table = "traffic_sign_subclass";
    public $timestamps = false;

    public function _class(){
        return $this->belongsTo("TrafficSignClass");
    }
}