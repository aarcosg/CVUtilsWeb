<?php

function processClassifySample(){

    $response = ["success" => 0, "msg" => "Ops! Sample not classified"];

    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0
        && isset($_POST["selected_class"]) && is_numeric($_POST["selected_class"]) && $_POST["selected_class"] > 0){
        $sample_id = $_POST["sample"];
        $class_id = $_POST["selected_class"];

        $sample = Sample::find($sample_id);
        $sample->class_id = $class_id;
        if($sample->save()){
            $response = ["success" => 1, "msg" => "Sample classified succesfully"];
        }
    }

    return json_encode($response);
}

function loadNextSampleToClassify(){

    $response = ["success" => 0, "id" => 0, "image" => ""];
    $sample = Sample::where('class_id',0)->take(1)->get();
    if($sample[0]){
        $response = ["success" => 1, "id" => $sample[0]->id, "image" => $sample[0]->image];
    }
    return json_encode($response);
}
