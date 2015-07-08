<?php
require '../../computervision/config/common_require.php';

$samples_classification = Sample::where('class_id','>',0)->get();
$samples_cropped = Sample::whereNotNull('crop_x')->get();


$title = "Overview";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <div class="col s12">
                <ul class="tabs z-depth-1">
                    <li class="tab col s3"><a class="active" href="#classification_tab">Classification</a></li>
                    <li class="tab col s3"><a href="#crop_tab">Cropped images</a></li>
                </ul>
            </div>
            <div id="classification_tab" class="col s12">
                <table class="striped" >
                    <thead>
                    <tr>
                        <th data-field="image">Image</th>
                        <th data-field="class">Class</th>
                        <th data-field="action">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($samples_classification as $sample){
                        ?>
                        <tr>
                            <td><img src="<?=DIR_SIGNS.'samples/'.$sample->image?>" class="responsive-img" style="max-width: 150px;"/></td>
                            <td><img src="<?=DIR_SIGNS.'classes/'.$sample->_class->image?>"/> </td>
                            <td><a href="classifier.php?id=<?=$sample->id?>" class="waves-effect waves-light btn"><i class="material-icons left">mode_edit</i>Edit</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div id="crop_tab" class="col s12">
                <table class="striped">
                    <thead>
                    <tr>
                        <th data-field="image">Original</th>
                        <th data-field="class">Cropped</th>
                        <th data-field="action">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($samples_cropped as $sample){
                        ?>
                        <tr>
                            <td><img src="<?=DIR_SIGNS.'samples/'.$sample->image?>"/></td>
                            <td><img src="<?=DIR_SIGNS.'samples/cropped/'.$sample->image?>"/> </td>
                            <td><a href="cropper.php?id=<?=$sample->id?>" class="waves-effect waves-light btn"><i class="material-icons left">mode_edit</i>Edit</a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">

    $(document).ready(function(){
        $('ul.tabs').tabs();
    });


</script>


