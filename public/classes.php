<?php
require '../../computervision/config/common_require.php';

$subclasses = ImageSubclass::all();

if(isset($_POST["action"]) && $_POST["action"] == "submitClass"){
    $response = processSubmitClass();
}

$title = "Classes";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <form id="new_class_form" method="post" action="#" enctype="multipart/form-data" class="col s12" >
                <div class="row">
                    <div class="input-field col s4">
                        <input id="name" name="name" type="text" class="validate" required="true">
                        <label for="name">Class Name</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s4">
                        <select id="subclass" name="subclass">
                            <?php foreach($subclasses as $subclass){ ?>
                                <option value="<?=$subclass->id?>"><?=$subclass->name?></option>
                            <?php } ?>
                        </select>
                        <label for="subclass">Subclass</label>
                    </div>
                </div>
                <div class="row">
                    <div class="file-field input-field col s4">
                        <label for="file">Image file</label>
                        <input class="file-path validate" type="text"/>
                        <div class="btn orange">
                            <input id="file" name="file" type="file" accept="image/jpeg,image/png,image/gif" required="true" />
                            <span>Image file</span>
                        </div>
                    </div>
                </div>
                <!--<input type="hidden" name="event" value="submitClass">-->
            </form>
        </div>
        <div class="row">
            <div class="col s4">
                <button id="submit_class_btn" class="btn-large waves-effect waves-light" type="submit" name="action" value="submitClass" form="new_class_form">
                    <i class="material-icons right">add_circle</i>
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
<?php include_once("include/footer.inc.php"); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('select').material_select();
        <?php if(isset($response)){ ?>
            var toastColor = '<?=($response["success"] ? "toast-success" : "toast-danger")?>';
            Materialize.toast('<?=$response["msg"]?>',5000,toastColor);
        <?php } ?>
    });
</script>