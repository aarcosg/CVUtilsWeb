<?php

function processCropSample(){

    $response = ["success" => 0, "msg" => "Ops! Sample not cropped"];

    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0
        && isset($_POST["crop_data"])){

        $sample_id = $_POST["sample"];
        $data = $_POST["crop_data"];


        $sample = Sample::find($sample_id);
        if(crop($sample->image,$data)){
            $sample->crop_x = round($data["x"],2);
            $sample->crop_y = round($data["y"],2);
            $sample->crop_width = round($data["width"],2);
            $sample->crop_height = round($data["height"],2);
            $sample->lock = 0;
            if($sample->save()){
                $_SESSION[SESSION_KEY_CROP_COUNTER]++;
                $response = ["success" => 1, "msg" => "Cropped succesfully"];
            }
        }else{
            $response = ["success" => 0, "msg" => "Failed to crop the image file"];
        }

    }

    return json_encode($response);
}

function loadNextSampleToCrop(){

    $response = ["success" => 0, "id" => 0, "image" => ""];
    $sample = Sample::whereNull('crop_x')->where('lock',0)->take(1)->get();
    if($sample[0]){
        lockSampleToCrop($sample[0]);
        $response = ["success" => 1, "id" => $sample[0]->id, "image" => $sample[0]->image];
    }
    return json_encode($response);
}

function lockSampleToCrop($sample){
    if($sample->lock == 0){
        $sample->lock = 1;
        $sample->save();
    }
}

function unlockSampleToCrop(){
    if(isset($_POST["sample"]) && is_numeric($_POST["sample"]) && $_POST["sample"] > 0){
        $sample = Sample::find($_POST["sample"]);
        if($sample->lock == 1){
            $sample->lock = 0;
            $sample->save();
        }
    }
}

function crop($src, $data) {
    $success = false;
    if (!empty($src) && !empty($data)) {

        $src_filename = DIR_SIGNS."samples/".$src;
        $type = exif_imagetype($src_filename);

        switch ($type) {
            case IMAGETYPE_GIF:
                $src_img = imagecreatefromgif($src_filename);
                break;

            case IMAGETYPE_JPEG:
                $src_img = imagecreatefromjpeg($src_filename);
                break;

            case IMAGETYPE_PNG:
                $src_img = imagecreatefrompng($src_filename);
                break;
        }

        if (!$src_img) {
            echo "Failed to read the image file";
            return;
        }

        $size = getimagesize($src_filename);
        $src_w = $size[0]; // natural width
        $src_h = $size[1]; // natural height

        $src_img_w = $src_w;
        $src_img_h = $src_h;

        /*Images contain a border around the actual sign
        * of 10 percent of the sign size, at least 5 pixel
        */

        $horizontal_margin = ($data["width"]*0.1 < 5) ? 5 : $data["width"]*0.1;
        $vertical_margin = ($data["height"]*0.1 < 5) ? 5 : $data["height"]*0.1;

        $data["x"]-=$horizontal_margin;
        $data["y"]-=$vertical_margin;

        $tmp_img_w = $data["width"]+$horizontal_margin*2;
        $tmp_img_h = $data["height"]+$vertical_margin*2;
        if($tmp_img_w + $data["x"] <= $src_w){
            $data["width"] = $tmp_img_w;
        }else{
            $data["width"] += $tmp_img_w + $data["x"] - $src_w;
        }
        if($tmp_img_h + $data["y"] <= $src_h){
            $data["height"] = $tmp_img_h;
        }else{
            $data["height"] += $tmp_img_h + $data["y"] - $src_h;
        }

        $tmp_img_w = $data["width"];
        $tmp_img_h = $data["height"];
        $dst_img_w = $tmp_img_w;
        $dst_img_h = $tmp_img_h;

        $src_x = $data["x"];
        $src_y = $data["y"];

        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }

        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }

        // Scale to destination position and size
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;

        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

        /*// Add transparent background to destination image
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);*/

        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($result) {
            $crop_dir = DIR_SIGNS."samples/cropped";
            if(!file_exists($crop_dir)){
                mkdir($crop_dir);
            }
            $dst_filename = $crop_dir."/".$src;
            switch ($type) {
                case IMAGETYPE_GIF:
                    $dst_img = imagegif($dst_img,$dst_filename);
                    break;

                case IMAGETYPE_JPEG:
                    $dst_img = imagejpeg($dst_img,$dst_filename);
                    break;

                case IMAGETYPE_PNG:
                    $dst_img = imagepng($dst_img,$dst_filename);
                    break;
            }
            if ($dst_img) {
                $success = true;
                //echo "Failed to save the cropped image file";
            }
        } /*else {
            echo "Failed to crop the image file";
        }*/
    }
    return $success;
}
