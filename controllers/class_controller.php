<?php

function processSubmitClass(){

    $success = 0;
    $msg = "Missing fields";

    if(isset($_POST["name"]) && !empty($_POST["name"])
        && isset($_POST["subclass"]) && is_numeric($_POST["subclass"]) && $_POST["subclass"] > 0
        && isset($_FILES["file"]["name"])){

        $name = $_POST["name"];
        $subclass_id = $_POST["subclass"];

        $target_dir = "images/signs/classes/";
        $target_file = $target_dir.basename($_FILES["file"]["name"]);

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
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir.$file_name)) {
                $msg = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
                $class = new ImageClass();
                $class->name = $name;
                $class->image = $file_name;
                $class->subclass_id = $subclass_id;
                $class->save();
        } else {
                $msg = "Sorry, there was an error uploading your file.";
            }
        }
    }

    return array(
        "success" => $success
        ,"msg" => $msg);
}

