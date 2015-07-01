<?php
require('../../computervision/vendor/autoload.php');
define ("DIR_SIGNS","images/signs");

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

$filemanager= new Filesystem(new Adapter(__DIR__."/".DIR_SIGNS));

$samples = $filemanager->listContents("/samples");
$samples_img = array();
foreach($samples as $sample){
    $samples_img[]=DIR_SIGNS.'/samples/'.$sample['basename'];
}

$classes = $filemanager->listContents("/classes");

/*$sign_classes = array();
foreach($classes as $class){
    $sign_classes[]=DIR_SIGNS.'/classes/'.$class['basename'];
}*/
$title = "Image classifier";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s2 fixed">
                <div class="card">
                    <div class="card-image">
                        <img id="sample_img" src="<?=$samples_img[0]?>" class="responsive-img">
                    </div>
                    <div class="card-action center" style="padding-left: 0px;padding-right: 0px;">
                        <a id="previous_btn" class="waves-effect waves-orange btn-flat"><i class="material-icons left">chevron_left</i>Previous</a>
                        <a id="next_btn" class="waves-effect waves-orange btn-flat"><i class="material-icons right">chevron_right</i>Next</a>
                    </div>
                </div>
                <a id="save_btn" class="waves-effect waves-light btn-large"><i class="material-icons right">check_circle</i>Save</a>
            </div>
            <div class="col s9 offset-s3">
                <div class="card">
                    <div class="card-content">
                        <?php
                        $i=0;
                        foreach($classes as $class){
                            $class_path = DIR_SIGNS.'/classes/'.$class['basename'];
                            $class_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $class['basename']);
                            if($i%6 == 0){?>
                                <div class="row">
                            <?php } ?>
                            <div class="col s2 center">
                                <div class="signal-thumb">
                                    <img src="<?=$class_path?>">
                                    <br>
                                    <span><?=$class_name?></span>
                                </div>
                            </div>
                            <?php
                            $i++;
                            if($i%6 == 0){?>
                                </div>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    var sample_index = 0;
    var samples = <?=json_encode($samples_img)?>;
</script>


