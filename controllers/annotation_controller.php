<?php

function processClassifySample(){

    $response = ["success" => 0, "msg" => "Ops! Sample not classified"];

    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0
        && isset($_POST["selected_class"]) && is_numeric($_POST["selected_class"]) && $_POST["selected_class"] > 0){
        $sample_id = $_POST["sample"];
        $class_id = $_POST["selected_class"];

        $sample = AnnotationSample::find($sample_id);
        $sample->class_id = $class_id;
        $sample->lock = 0;
        if($sample->save()){
            $_SESSION[SESSION_KEY_CLASS_COUNTER]++;
            $response = ["success" => 1, "msg" => "Sample classified succesfully"];
        }
    }

    return json_encode($response);
}

function loadNextSampleToClassify(){

    $response = ["success" => 0, "id" => 0, "image" => ""];
    $sample = AnnotationSample::where('class_id',0)->where('lock',0)->take(1)->get();
    if($sample[0]){
        lockSampleToClassify($sample[0]);
        $response = ["success" => 1, "id" => $sample[0]->id, "image" => $sample[0]->image];
    }
    return json_encode($response);
}

function lockSampleToClassify($sample){
    if($sample->lock == 0){
        $sample->lock = 1;
        $sample->save();
    }
}

function unlockSampleToClassify(){
    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0){
        $sample = AnnotationSample::find($_POST["sample"]);
        if($sample->lock == 1){
            $sample->lock = 0;
            $sample->save();
        }
    }
}

use GuzzleHttp\Client;
function recommendSampleClass(){
    $response = ["success" => 0, "id" => 0, "msg" => "Unknown class","image" => ""];
    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0){
        $sample = AnnotationSample::find($_POST["sample"]);
        if($sample){
            $client = new Client([
                "base_uri" => "http://alvaroarcos.co:8080/classify-ts/"
            ]);
            $svm_class_id = intval($client->get("germany/".$sample->image)->getBody()->getContents());
            if($svm_class_id >= 0){
                $class = TrafficSignClass::where('germany',$svm_class_id)->first();
                if($class){
                    $response = ["success" => 1, "id" => $class->id, "msg" => "Class predicted", "image" => $class->image];
                }
            }
        }
    }
    return json_encode($response);
}
