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
    $sample = Sample::find($_GET["id"]);
}


//$sample = Sample::where('class_id',0)->take(1)->get();
$subclasses = ImageSubclass::all();

$title = "Classifier";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <!--<div class="col s4">-->
            <div class="col s2 fixed">
                <!-- <div class="card">
                     <div class="card-image">
                         <img id="sample_img" src="http://placehold.it/200x200?text=Empty" class="responsive-img z-depth-1">
                     </div>
                      <div class="card-action center" style="padding-left: 0px;padding-right: 0px;">
                         <a id="previous_btn" class="waves-effect waves-orange btn-flat"><i class="material-icons left">chevron_left</i>Previous</a>
                         <a id="next_btn" class="waves-effect waves-orange btn-flat"><i class="material-icons right">chevron_right</i>Next</a>
                     </div>
                </div>-->
                <img id="sample_img" src="<?=isset($sample) ? DIR_SIGNS.'samples/'.$sample->image : 'http://placehold.it/200x200?text=Empty' ?>" class="responsive-img z-depth-1">
                <a id="classify_sample_btn" data-sample="<?=isset($sample) ? $sample->id : '-1' ?>" class="waves-effect waves-light btn-large"><i class="material-icons left">check_circle</i>Save</a>
                <a id="recommend_sample_btn" style="margin-top: 15px;" class="waves-effect waves-light btn-large orange"><i class="material-icons left">help</i>Help me</a>
            </div>
            <!--<div class="col s8" style="overflow: auto;max-height: 90vh;">-->
            <div class="col s9 offset-s3">
                <?php foreach($subclasses as $subclass){
                    $classes=ImageClass::where('subclass_id',$subclass->id)->get();?>
                    <h5 class="blue-grey-text text-lighten-1"><?=$subclass->name?></h5>
                    <div class="card">
                        <div class="card-content">
                            <?php
                            $i=0;
                            foreach ($classes as $class) {
                                $class_image_path = DIR_SIGNS.'classes/'.$class->image;
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
            url: "classifier.php",
            type: "post",
            data:{action : "unlockSampleToClassify", sample : $("#classify_sample_btn").attr("data-sample")},
            async:false
        });
    });
    <?php if(!isset($sample)){ ?> loadNextSampleToClassify(); <?php } ?>

</script>


