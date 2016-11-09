<?php
require '../../computervision/config/common_require.php';

if(isset($_POST["action"]) && $_POST["action"] == "submitSingleImage"){
    $response = processSingleImage();
    if(isset($response) && $response['success']){
        $sample = $response['sample'];
        $classify_one_predictions = $response['predictions'];
    }
}else if(isset($_POST["action"]) && $_POST["action"] == "submitImageList"){
    $response = processImageList();
    if(isset($response) && $response['success'] && is_numeric($response['id']) && $response['id'] > 0){
        $classify_many_id = $response["id"];
    }
}else if(isset($_GET['classify_many']) && is_numeric($_GET['classify_many'])){
    $classify_many_id =  $_GET['classify_many'];
}

if(isset($classify_many_id) && is_numeric($classify_many_id)){
    $classify_many_predictions = ClassifyManySample::where('classify_many_id', '=', $classify_many_id)->with('_class')->get()->toArray();
    $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $items_per_page = 15;
    $current_items = array_slice($classify_many_predictions, $items_per_page * ($current_page - 1), $items_per_page);
    $paginator = new Illuminate\Pagination\LengthAwarePaginator($current_items, count($classify_many_predictions), $items_per_page, $current_page);
    $paginator->setPath('classifier.php');
    $paginator->appends(['classify_many' => $classify_many_id]);
    $presenter = new Landish\Pagination\Materialize($paginator);
}

$title = "Classifier";
include_once("include/header.inc.php");
?>

<div class="section">
    <div class="row">
        <div class="col s12">
            <ul class="tabs tabs-fixed-width">
                <li class="tab"><a class="<?=isset($classify_many_id)? '' : 'active' ?>" href="#classify_one_tab">Classify one</a></li>
                <li class="tab"><a class="<?=isset($classify_many_id)? 'active' : '' ?>" href="#classify_many_tab">Classify many</a></li>
            </ul>
        </div>
        <div id="classify_one_tab" class="col s12">
            <div class="col s5">
                <form id="test_single_image_form" method="post" action="#" enctype="multipart/form-data">
                     <div class="row">
                         <div class="file-field input-field col s8">
                             <label for="file">Image file</label>
                             <input class="file-path" type="text"/>
                             <div class="btn orange">
                                 <input id="file" name="file" type="file" accept="image/jpeg,image/png,image/gif" />
                                 <span>Image file</span>
                             </div>
                         </div>
                     </div>
                    <div class="row">
                        <div class="center-align col s8">OR</div>
                        <div class="input-field col s8">
                            <label for="url">Image URL</label>
                            <input name="url" id="url" type="text"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s6">
                            <button id="submit_test_image_btn" class="btn waves-effect waves-light" type="submit" name="action" value="submitSingleImage" form="test_single_image_form">
                                Submit
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php if(isset($classify_one_predictions) && !empty($classify_one_predictions)) { ?>
                <div class="col s6 push-s1">
                    <h5 class="blue-grey-text text-lighten-1">Image uploaded</h5>
                    <img id="sample_img" src="<?=isset($sample) ? $sample : 'http://placehold.it/200x200?text=None' ?>" class="responsive-img z-depth-1">
                </div>
                <div class="col s12">
                    <h5 class="blue-grey-text text-lighten-1">Predictions</h5>
                    <?php foreach ($classify_one_predictions as $prediction){
                            $traffic_sign = $prediction['traffic_sign'];
                            $confidence = $prediction['confidence']; ?>
                            <div class="row">
                                <div class="col s6">
                                    <img class="responsive-img" src="<?=DIR_SIGNS_CLASSES.$traffic_sign['image']?>" alt="<?=$traffic_sign['name']?>" title="<?=$traffic_sign['name']?>">
                                </div>
                                <div class="col s6">
                                    <h5 class="blue-grey-text text-lighten-1"><?=$confidence?>%</h5>
                                </div>
                            </div>
                        <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div id="classify_many_tab" class="col s12">
            <?php if(!isset($classify_many_id) && !isset($classify_many_predictions)) { ?>
            <div class="col s6">
                <form id="test_image_list_form" method="post" action="#" enctype="multipart/form-data">
                    <div class="row">
                        <div class="file-field input-field col s6">
                            <label for="file">Zip file</label>
                            <input class="file-path" type="text"/>
                            <div class="btn orange">
                                <input id="file" name="zip_file" type="file" accept="application/zip,application/x-zip,application/x-zip-compressed,application/x-compressed,multipart/x-zip,application/octet-stream" />
                                <span>Zip file</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s6">
                            <button id="submit_image_list_btn" class="btn waves-effect waves-light" type="submit" name="action" value="submitImageList" form="test_image_list_form">
                                Submit
                                <i class="material-icons right">send</i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col s12">
                        <h5 class="blue-grey-text text-lighten-1">Predictions</h5>
                        <a class="waves-effect waves-light btn deep-purple darken-4" href="<?=DIR_SIGNS_CLASSIFY_MANY.$classify_many_id.'/predictions_'.$classify_many_id.'.json'?>" download="" target="_blank">
                            <i class="material-icons right">cloud_download</i>
                            Download predictions
                        </a>
                        <a class="waves-effect waves-light btn blue" href="classifier.php">
                            <i class="material-icons right">clear_all</i>
                            Clear results
                        </a>
                        <div class="center">
                            <?=$paginator->render($presenter)?>
                        </div>
                        <table class="striped">
                            <thead>
                                <tr>
                                    <th data-field="test_image">Test image</th>
                                    <th data-field="prediction">Prediction</th>
                                    <th data-field="confidence">Confidence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paginator->items() as $prediction){?>
                                    <tr>
                                        <td>
                                            <img class="responsive-img" src="<?=DIR_SIGNS_CLASSIFY_MANY.$classify_many_id.'/'.$prediction['sample_image']?>">
                                        </td>
                                        <td>
                                            <img class="responsive-img" width="70" src="<?=DIR_SIGNS_CLASSES.$prediction['_class']['image']?>" alt="<?=$prediction['_class']['name']?>" title="<?=$prediction['_class']['name']?>">
                                        </td>
                                        <td>
                                            <h5 class="blue-grey-text text-lighten-1"><?=$prediction['confidence']?>%</h5>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="center">
                            <?=$paginator->render($presenter)?>
                        </div>
                    </div>
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