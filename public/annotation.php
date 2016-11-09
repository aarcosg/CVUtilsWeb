<?php
require '../../computervision/config/common_require.php';

if(isset($_POST["action"])){
    switch ($_POST["action"]){
        case "classifySample": echo processClassifySample(); break;
        case "loadNextSampleToClassify": echo loadNextSampleToClassify(); break;
        case "unlockSampleToClassify": unlockSampleToClassify(); break;
        case "recommendSampleClass": echo recommendSampleClass();
    }
    exit;
}

if(isset($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] > 0){
    $sample = AnnotationSample::find($_GET["id"]);
}


//$sample = AnnotationSample::where('class_id',0)->take(1)->get();
$subclasses = TrafficSignSubclass::all();

$title = "Annotate";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s2 fixed">
                <img id="sample_img" src="<?=isset($sample) ? DIR_SIGNS.'samples/'.$sample->image : 'http://placehold.it/200x200?text=Empty' ?>" class="responsive-img z-depth-1">
                <a id="classify_sample_btn" data-sample="<?=isset($sample) ? $sample->id : '-1' ?>" class="waves-effect waves-light btn-large"><i class="material-icons left">check_circle</i>Save</a>
                <a id="recommend_sample_btn" style="margin-top: 15px;" class="waves-effect waves-light btn-large orange"><i class="material-icons left">help</i>Help me</a>
                <form>
                    <div class="input-field">
                        <input id="search" type="search">
                        <label for="search"><i class="material-icons">search</i></label>
                        <i class="material-icons">close</i>
                    </div>
                </form>
            </div>
            <div class="col s9 offset-s3">
                <?php foreach($subclasses as $subclass){
                    $classes=TrafficSignClass::where('subclass_id',$subclass->id)->get();?>
                    <h5 class="blue-grey-text text-lighten-1"><?=$subclass->name?></h5>
                    <div class="card">
                        <div class="card-content">
                            <?php
                            $i=0;
                            foreach ($classes as $class) {
                                $class_image_path = DIR_SIGNS_CLASSES.$class->image;
                                if($i%6 == 0){ echo "<div class='row'>";} ?>
                                <div class="col s2 center">
                                    <div class="signal-thumb" data-class="<?=$class->id?>">
                                        <img class="signal-thumb-img" src="<?=$class_image_path?>" alt="<?=$class->name?>" title="<?=$class->name?>">
                                        <br>
                                        <span class="truncate"><?=$class->name?></span>
                                    </div>
                                </div>
                                <?php
                                $i++;
                                if($i%6 == 0){ echo "</div>"; }
                            }
                            if($i%6 > 0){ echo "</div>"; }?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    //var sample_index = 1;
    //var samples = <?php //echo json_encode($samples);?>;

    $(window).bind("beforeunload", function() {
        $.ajax({
            url: "annotation.php",
            type: "post",
            data:{action : "unlockSampleToClassify", sample : $("#classify_sample_btn").attr("data-sample")},
            async:false
        });
    });
    <?php if(!isset($sample)){ ?> loadNextSampleToClassify(); <?php } ?>

</script>


