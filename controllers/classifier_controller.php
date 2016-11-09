<?php

use GuzzleHttp\Exception\ServerException;

function processSingleImage(){

    $response = ["success" => 0, "sample" => "", "msg" => "Missing fields", "predictions" => ""];

    if(isset($_FILES["file"]["name"]) && file_exists($_FILES["file"]["tmp_name"])){

        $target_file = DIR_SIGNS_CLASSIFY_ONE.basename($_FILES["file"]["name"]);

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
            $file_name = DIR_SIGNS_CLASSIFY_ONE.uniqid('img_').".".$image_file_type;
            saveUploadedImage($file_name);
            $response["sample"] = 'http://arcos.io/cv_tools/'.$file_name;
            try{
                $response['predictions'] = classifyOne($response["sample"]);
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
            $response['predictions'] = classifyOne($response['sample']);
            $response["success"] = 1;
        }catch (ServerException $e){
            $response["success"] = 0;
            $response["msg"] = "Image not found";
        }
    }

    return $response;
}

function classifyOne($url = NULL){

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
        $traffic_sign = TrafficSignClass::where('spain_id', '=', $spain_id)->first();
        $traffic_signs[] = array(
            'traffic_sign' => $traffic_sign,
            'confidence' => $confidence
        );
    }

    return $traffic_signs;
}

function processImageList(){

    $success = 1;
    $msg = "";
    $response = ["success" => 0, "msg" => "Missing fields", "id" => -1, "predictions" => ""];

    if(isset($_FILES["zip_file"]["name"]) && file_exists($_FILES["zip_file"]["tmp_name"])){

        $source_file = $_FILES["zip_file"]["tmp_name"];
        $target_file = DIR_SIGNS_CLASSIFY_MANY.basename($_FILES["zip_file"]["name"]);
        $file_extension = pathinfo($target_file, PATHINFO_EXTENSION);
        $file_mimetype = $_FILES["zip_file"]["type"];

        // Check file size
        if ($_FILES["zip_file"]["size"] > 500000000) {
            $msg = "Sorry, your file is too large.";
            $success = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $msg = "Sorry, file already exists.";
            $success = 0;
        }

        // Allow certain file formats
        if($file_extension != "zip") {
            $msg = "Sorry, only ZIP files are allowed.";
            $success = 0;
        }

        // Check mimetypes
        $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed', 'application/octet-stream');
        if(!in_array($file_mimetype, $accepted_types)){
            $msg = "Mime type not supported: ".$file_mimetype;
            $success = 0;
        }

        if($success){
            if(move_uploaded_file($source_file, $target_file)){
                // Extract files
                $zip = new ZipArchive();
                if($zip->open($target_file)){

                    $classify_many_entity = new ClassifyMany();
                    $classify_many_entity->save();

                    $extract_folder = $classify_many_entity->id;
                    $extract_path = DIR_SIGNS_CLASSIFY_MANY.$extract_folder;
                    $zip->extractTo($extract_path);
                    $zip->close();
                    unlink($target_file);
                    $folders = glob($extract_path."/*", GLOB_ONLYDIR);
                    // Move files from extracted subfolders to root dir
                    if(count($folders) >= 1){
                        foreach ($folders as $folder){
                            shell_exec("find ".$folder." -type f -print0 | xargs -0 mv -t ".$extract_path);
                            shell_exec(escapeshellcmd("rm -r ".$folder));
                        }
                    }
                    // Remove files which are not images
                    $files = scandirNotImageFiles($extract_path);
                    foreach ($files as $file){
                        shell_exec(escapeshellcmd("rm '".$extract_path."/".$file."'"));
                    }

                    // Create txt with absolute paths to images extracted
                    $test_txt = getcwd().'/'.$extract_path.'/test_'.$extract_folder.'.txt';
                    shell_exec('ls -d -1 $PWD/'.$extract_path.'/*.* > '.$test_txt);

                    $classify_many_entity->test_filename = 'test_'.$classify_many_entity->id.'.txt';
                    $classify_many_entity->save();

                    try{
                        $predictions = classifyMany($classify_many_entity->id, $test_txt);
                        if(!is_null($predictions)){
                            $predictions_file = fopen(getcwd().'/'.$extract_path.'/predictions_'.$extract_folder.'.json', 'w') or die('Unable to open predictions file');
                            fwrite($predictions_file, json_encode($predictions));
                            fclose($predictions_file);
                            $classify_many_entity->predictions_filename = 'predictions_'.$classify_many_entity->id.'.json';
                            $classify_many_entity->save();
                            $response["id"] = $classify_many_entity->id;
                            $response['predictions'] = $predictions;
                            $success = 1;
                        }else{
                            $response["success"] = 0;
                            $response["msg"] = "NVIDIA Digits error";
                        }
                    } catch (ServerException $e){
                        $response["success"] = 0;
                        $response["msg"] = "Server error";
                    }
                }
            }
        }

        $response["success"] = $success;
        $response["msg"] = $msg;

    }

    return $response;
}

function classifyMany($classify_many_id, $image_list){

    ini_set('memory_limit','512M');

    $client = new GuzzleHttp\Client();
    $digits_response = $client->post(
        'http://arcos.io:5000/models/images/classification/classify_many.json', [
            'multipart' => [
                [
                    'name' => 'job_id',
                    'contents' => '20161024-081150-5437'
                ],
                [
                    'name' => 'image_list',
                    'contents' => fopen($image_list, 'r')
                ]
            ]
        ]);

    if($digits_response->getStatusCode() == 200){

        $digits_classifications = json_decode($digits_response->getBody())->classifications;

        $traffic_signs = array();
        foreach ($digits_classifications as $image=>$classifications){
            $classification = $classifications[0];
            $spain_id = $classification[0];
            $confidence = $classification[1];

            if(array_key_exists($spain_id, $traffic_signs)){
                $traffic_sign = $traffic_signs[$spain_id];
            }else{
                $traffic_sign = TrafficSignClass::where('spain_id', '=', $spain_id)->first();
                $traffic_signs[$spain_id] = $traffic_sign;
            }

            $classify_many_sample = new ClassifyManySample();
            $classify_many_sample->sample_image = basename($image);
            $classify_many_sample->class_id = $traffic_sign['id'];
            $classify_many_sample->confidence = $confidence;
            $classify_many_sample->classify_many_id = $classify_many_id;
            $classify_many_sample->save();

        }
        return $digits_classifications;
    }else{
        return null;
    }
}

function saveUploadedImage($file_name){
    $max_dim = 28;
    list($width, $height) = getimagesize( $_FILES['file']['tmp_name'] );
    if ( $width > $max_dim || $height > $max_dim ) {
        $target_filename = $file_name;
        $fn = $_FILES['file']['tmp_name'];
        $size = getimagesize( $fn );
        $ratio = $size[0]/$size[1]; // width/height
        if( $ratio > 1) {
            $width = $max_dim;
            $height = $max_dim/$ratio;
        } else {
            $width = $max_dim*$ratio;
            $height = $max_dim;
        }
        $src = imagecreatefromstring(file_get_contents($fn));
        $dst = imagecreatetruecolor($width, $height);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
        imagedestroy($src);
        imagepng($dst, $target_filename);
        imagedestroy($dst);
    }
}

function scandirNotImageFiles($dir){
    return array_filter(scandir($dir), function ($item) use ($dir) {
        return is_file($dir.DIRECTORY_SEPARATOR.$item)
            && !is_array(getimagesize($dir.DIRECTORY_SEPARATOR.$item));
    });
}
