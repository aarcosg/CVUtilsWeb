<?php
require '../../computervision/config/common_require.php';

if(isset($_POST["action"]) && $_POST["action"] == "cropSample"){
    echo processCropSample();
    exit;
}

if(isset($_POST["action"]) && $_POST["action"] == "loadNextSampleToCrop"){
    echo loadNextSampleToCrop();
    exit;
}

if(isset($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] > 0){
    $sample = Sample::find($_GET["id"]);
}


//$samples = Sample::all();
//$samples = Sample::whereNull('crop_x')->get();

$title = "Cropper";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s10">
                <div class="center">
                    <img id="crop_img" src="<?=isset($sample) ? DIR_SIGNS.'samples/'.$sample->image : 'http://placehold.it/200x200?text=Empty' ?>">
                </div>
            </div>
            <div class="col s2">
                <div class="center">
                    <!-- <a id="previous_crop_sample_btn" class="waves-effect waves-light btn-large orange"><i class="material-icons left">chevron_left</i></a>
                    <a id="next_crop_sample_btn" class="waves-effect waves-light btn-large orange"><i class="material-icons right">chevron_right</i></a>
                    <div class="clearfix" style="margin-top: 20px;"></div>-->
                    <a id="crop_sample_btn" data-sample="<?=isset($sample) ? $sample->id : '-1' ?>" class="waves-effect waves-light btn-large">Save</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12">
                <div class="card-panel blue-grey darken-2">
                    <span class="white-text">Adjust bounding box to traffic sign. The tool will add the proper margin automatically.</span>
                </div>
            </div>
            <div class="col s12">
                <div class="card blue-grey lighten-5">
                    <div class="card-content">
                        <div class="input-field col s3">
                            <input id="data_x" name="data_x" type="text" placeholder="Data x" readonly>
                            <label for="data_x">X</label>
                        </div>
                        <div class="input-field col s3">
                            <input id="data_y" name="data_y" type="text" placeholder="Data y" readonly>
                            <label for="data_y">Y</label>
                        </div>
                        <div class="input-field col s3">
                            <input id="data_width" name="data_width" type="text" placeholder="Data width" readonly>
                            <label for="data_width">Width</label>
                        </div>
                        <div class="input-field col s3">
                            <input id="data_height" name="data_height" type="text" placeholder="Data height" readonly>
                            <label for="data_height">Height</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    /*var sample_index = 1;
    var samples = <?php /*echo json_encode($samples)*/?>;*/
    initCropper();
    <?php if(!isset($sample)){ ?> loadNextSampleToCrop(); <?php } ?>


</script>


