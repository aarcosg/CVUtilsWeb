<?php

use GuzzleHttp\Exception\ServerException;

function processTestSingleImage(){

    $response = ["success" => 0, "sample" => "", "msg" => "Missing fields", "predictions" => ""];

    if(isset($_FILES["file"]["name"]) && file_exists($_FILES["file"]["tmp_name"])){

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

        if($success){
            $file_name = DIR_SIGNS_CLASSIFIER_UPLOADS.uniqid('img_').".".$image_file_type;
            save_uploaded_image($file_name);
            $response["sample"] = 'http://arcos.io/cv_tools/'.$file_name;
            try{
                $response['predictions'] = getPredictions($response["sample"]);
                $success = 1;
            } catch (ServerException $e){
                $response["success"] = 0;
                $response["msg"] = "Image not found";
            }
        }

        $response["success"] = $success;
        $response["msg"] = $msg;

    } else if(isset($_POST['url']) && !filter_var($_POST['url'], FILTER_VALIDATE_URL) === false){
        $url = $_POST['url'];
        $response['sample'] = $url;
        try{
            $response['predictions'] = getPredictions($response['sample']);
            $response["success"] = 1;
        }catch (ServerException $e){
            $response["success"] = 0;
            $response["msg"] = "Image not found";
        }
    }

    return $response;
}

function getPredictions($url = NULL){

    $client = new GuzzleHttp\Client();
    $digits_response = $client->post(
        'http://arcos.io:5000/models/images/classification/classify_one.json',
        [
            "form_params" => [
                'job_id' => '20161024-081150-5437',
                'image_path' => $url
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

    return $traffic_signs;
}

function save_uploaded_image($file_name){
    $maxDim = 28;
    list($width, $height) = getimagesize( $_FILES['file']['tmp_name'] );
    if ( $width > $maxDim || $height > $maxDim ) {
        $target_filename = $file_name;
        $fn = $_FILES['file']['tmp_name'];
        $size = getimagesize( $fn );
        $ratio = $size[0]/$size[1]; // width/height
        if( $ratio > 1) {
            $width = $maxDim;
            $height = $maxDim/$ratio;
        } else {
            $width = $maxDim*$ratio;
            $height = $maxDim;
        }
        $src = imagecreatefromstring( file_get_contents( $fn ) );
        $dst = imagecreatetruecolor( $width, $height );
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
        imagedestroy($src);
        imagepng($dst, $target_filename);
        imagedestroy($dst);
    }
}
