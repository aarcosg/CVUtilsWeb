<?php
require '../../computervision/config/common_require.php';

$subclasses = ImageSubclass::all();

if(isset($_POST["action"]) && $_POST["action"] == "submitSingleTestImage"){
    $response = processTestSingleImage();
    if(isset($response) && $response['success']){
        $sample = $response['sample'];
        $predictions = $response['predictions'];
    }
}

$title = "Classifier";
include_once("include/header.inc.php");
?>

<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s4">
                <h5 class="blue-grey-text text-lighten-1">Test a single image</h5>
                <form id="test_single_image_form" method="post" action="#" enctype="multipart/form-data">
                     <div class="row">
                         <div class="file-field input-field col s12">
                             <label for="file">Image file</label>
                             <input class="file-path" type="text"/>
                             <div class="btn orange">
                                 <input id="file" name="file" type="file" accept="image/jpeg,image/png,image/gif" />
                                 <span>Image file</span>
                             </div>
                         </div>
                     </div>
                    <div class="row">
                        <div class="center-align">OR</div>
                        <div class="input-field col s12">
                            <label for="url">Image URL</label>
                            <input name="url" id="url" type="text"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <button id="submit_test_image_btn" class="btn-large waves-effect waves-light col s12" type="submit" name="action" value="submitSingleTestImage" form="test_single_image_form">
                                Submit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php if(isset($predictions)) { ?>
            <div class="col s3 push-s1">
                <h6 class="blue-grey-text text-lighten-1">Image uploaded</h6>
                <img id="sample_img" src="<?=isset($sample) ? $sample : 'http://placehold.it/200x200?text=None' ?>" class="responsive-img z-depth-1">
            </div>
            <div class="col s3 push-s2">
                <h6 class="blue-grey-text text-lighten-1">Predictions</h6>
                    <?php if (isset($predictions) && !empty($predictions)){
                        foreach ($predictions as $prediction){
                            $traffic_sign = $prediction['traffic_sign'];
                            $confidence = $prediction['confidence']; ?>
                            <div class="row">
                                <div class="col s4">
                                    <img class="responsive-img" src="<?=DIR_SIGNS_CLASSES.$traffic_sign['image']?>" alt="<?=$traffic_sign['name']?>" title="<?=$traffic_sign['name']?>">
                                </div>
                                <div class="col s8">
                                    <h5 class="blue-grey-text text-lighten-1"><?=$confidence?>%</h5>
                                </div>
                            </div>
                    <?php }} ?>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    $(document).ready(function() {
        <?php if(isset($response) && !$response['success']){ ?>
            Materialize.toast('<?=$response["msg"]?>',5000,'toast-danger');
        <?php } ?>
    });
</script>