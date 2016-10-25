<?php

function processTestSingleImage(){

    $response = ["success" => 0, "sample" => "", "msg" => "Missing fields", "predictions" => ""];

    if(isset($_FILES["file"]["name"])){

        $target_file = DIR_SIGNS_CLASSIFIER_UPLOADS.basename($_FILES["file"]["name"]);

        $image_file_type = pathinfo($target_file,PATHINFO_EXTENSION);

        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if($check !== false) {
            $msg = "File is an image - " . $check["mime"] . ".";
            $success = 1;
        } else {
            $msg = "File is not an image.";
            $success = 0;
        }

        // Check file size
        if ($_FILES["file"]["size"] > 500000) {
            $msg = "Sorry, your file is too large.";
            $success = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $msg = "Sorry, file already exists.";
            $success = 0;
        }

        // Allow certain file formats
        if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"
            && $image_file_type != "gif" ) {
            $msg = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $success = 0;
        }

        if(!$success){
            $msg = "Sorry, your file was not uploaded.";
        }else{
            $file_name = uniqid('img_').".".$image_file_type;
            if (move_uploaded_file($_FILES["file"]["tmp_name"], DIR_SIGNS_CLASSIFIER_UPLOADS.$file_name)) {
                $msg = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                $response["sample"] = $file_name;

                $client = new GuzzleHttp\Client();
                $digits_response = $client->post(
                    'http://arcos.io:5000/models/images/classification/classify_one.json',
                    [
                        "form_params" => [
                            'job_id' => '20161024-081150-5437',
                            'image_path' => 'http://arcos.io/cv_tools/'.DIR_SIGNS_CLASSIFIER_UPLOADS.$file_name
                        ]
                    ]
                );

                $digits_predictions = json_decode($digits_response->getBody())->predictions;

                $traffic_signs = array();
                foreach ($digits_predictions as $prediction){
                    $spain_id = $prediction[0];
                    $confidence = $prediction[1];
                    $traffic_sign = ImageClass::where('spain_id', '=', $spain_id)->first();
                    $traffic_signs[] = array(
                        'traffic_sign' => $traffic_sign,
                        'confidence' => $confidence
                    );
                }

                $response['predictions'] = $traffic_signs;

            } else {
                $msg = "Sorry, there was an error uploading your file.";
            }
        }

        $response["success"] = $success;
        $response["msg"] = $msg;

    }

    return $response;
}
