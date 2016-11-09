<?php
require '../../computervision/config/common_require.php';
define ("SAMPLES_PER_GROUP",20);


if(isset($_POST["action"]) && $_POST["action"] == "loadClassificationResults" && isset($_POST["page"]) && is_numeric($_POST["page"])){
    $samples_classification = AnnotationSample::where('class_id','>',0)->skip($_POST["page"]*SAMPLES_PER_GROUP)->take(SAMPLES_PER_GROUP)->with('_class')->get();
    echo json_encode($samples_classification);
    exit;
}

if(isset($_POST["action"]) && $_POST["action"] == "loadCroppedResults" && isset($_POST["page"]) && is_numeric($_POST["page"])){
    $samples_cropped  = AnnotationSample::whereNotNull('crop_x')->skip($_POST["page"]*SAMPLES_PER_GROUP)->take(SAMPLES_PER_GROUP)->get();
    echo json_encode($samples_cropped);
    exit;
}

$samples_classification = AnnotationSample::where('class_id','>',0)->get();
$samples_cropped = AnnotationSample::whereNotNull('crop_x')->get();


$total_groups_class = ceil($samples_classification->count()/SAMPLES_PER_GROUP);
$total_groups_crop = ceil($samples_cropped->count()/SAMPLES_PER_GROUP);


$title = "Annotation results";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s12">
                <div class="card-panel blue-grey darken-2">
                    <span class="white-text">Edit if there is any mistake.</span>
                </div>
            </div>
            <div class="col s12">
                <ul class="tabs z-depth-1">
                    <li class="tab col s3"><a class="active" href="#classification_tab">Classification</a></li>
                    <li class="tab col s3"><a href="#crop_tab">Cropped images</a></li>
                </ul>
            </div>
            <div id="classification_tab" class="col s12">
                <div class="col 3">
                    <div class="card-panel purple darken-1">
                        <span class="white-text">Total: <b><?=$samples_classification->count()?></b><i class="material-icons left">group</i></span>
                    </div>
                </div>
                <div class="col 3">
                    <div class="card-panel red darken-1">
                        <span class="white-text">Me: <b><?=$_SESSION[SESSION_KEY_CLASS_COUNTER]?></b><i class="material-icons left">person</i></span>
                    </div>
                </div>
                <div class="col s12">
                    <table class="striped" >
                        <thead>
                            <tr>
                                <th data-field="id">Id</th>
                                <th data-field="image">Image</th>
                                <th data-field="class">Class</th>
                                <th data-field="action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="classification_table_body">
                        <?php /* foreach($samples_classification as $sample){
                            ?>
                            <tr>
                                <td><?=$sample->id?></td>
                                <td><img src="<?=DIR_SIGNS.'samples/'.$sample->image?>" class="responsive-img" style="max-width: 150px;"/></td>
                                <td><img src="<?=DIR_SIGNS.'classes/'.$sample->_class->image?>"/> </td>
                                <td><a href="annotation.php?id=<?=$sample->id?>" class="waves-effect waves-light btn"><i class="material-icons left">mode_edit</i>Edit</a></td>
                            </tr>
                        <?php } */?>
                        </tbody>
                    </table>
                    <div class="progress" style="display: none">
                        <div class="indeterminate"></div>
                    </div>
                </div>
            </div>
            <div id="crop_tab" class="col s12">
                <div class="col 3">
                    <div class="card-panel purple darken-1">
                        <span class="white-text">Total: <b><?=$samples_cropped->count()?></b><i class="material-icons left">group</i></span>
                    </div>
                </div>
                <div class="col 3">
                    <div class="card-panel red darken-1">
                        <span class="white-text">Me: <b><?=$_SESSION[SESSION_KEY_CROP_COUNTER]?></b><i class="material-icons left">person</i></span>
                    </div>
                </div>
                <div class="col s12">
                    <table class="striped">
                        <thead>
                            <tr>
                                <th data-field="id">Id</th>
                                <th data-field="image">Original</th>
                                <th data-field="class">Cropped</th>
                                <th data-field="action">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cropped_table_body">
                        <?php /* foreach($samples_cropped as $sample){
                            ?>
                            <tr>
                                <td><?=$sample->id?></td>
                                <td><img src="<?=DIR_SIGNS.'samples/'.$sample->image?>" class="responsive-img" style="max-width: 150px;"/></td>
                                <td><img src="<?=DIR_SIGNS.'samples/cropped/'.$sample->image?>" class="responsive-img"/> </td>
                                <td><a href="cropper.php?id=<?=$sample->id?>" class="waves-effect waves-light btn"><i class="material-icons left">mode_edit</i>Edit</a></td>
                            </tr>
                        <?php } */ ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    var total_groups_class = <?=$total_groups_class?>;
    var total_groups_crop = <?=$total_groups_crop?>;

    $(document).ready(function(){
        $('ul.tabs').tabs();

        loadResults("loadClassificationResults","#classification_table_body",page_load_class,"classifier.php");
        loadResults("loadCroppedResults","#cropped_table_body",page_load_crop,"cropper.php");

        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() == $(document).height()){
                if(page_load_class <= total_groups_class && loading==false && $("a[href='#classification_tab']").hasClass("active")){
                    loading = true;
                    progress.show();
                    loadResults("loadClassificationResults","#classification_table_body",page_load_class,"classifier.php");
                }else if(page_load_crop <= total_groups_crop && loading==false && $("a[href='#crop_tab']").hasClass("active")){
                    loading = true;
                    progress.show();
                    loadResults("loadCroppedResults","#cropped_table_body",page_load_crop,"cropper.php");
                }
            }
        });

    });


</script>


