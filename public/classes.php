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
            <div class="col s8">
                <h5 class="blue-grey-text text-lighten-1">Add new traffic sign class</h5>
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
                    <div class="row">
                        <div class="col s4">
                            <button id="submit_class_btn" class="btn-large waves-effect waves-light" type="submit" name="action" value="submitClass" form="new_class_form">
                                <i class="material-icons right">add_circle</i>
                                Submit
                            </button>
                        </div>
                    </div>
                    <!--<input type="hidden" name="event" value="submitClass">-->
                </form>
            </div>
            <div class="col s4">
                <h5 class="blue-grey-text text-lighten-1">Sources</h5>
                <div class="collection">
                    <a href="https://es.wikipedia.org/wiki/Se%C3%B1ales_de_tr%C3%A1fico_verticales_de_Espa%C3%B1a" class="collection-item" target="_blank">Wikipedia</a>
                    <a href="http://www.boe.es/diario_boe/txt.php?id=BOE-A-2003-23514" class="collection-item" target="_blank">BOE</a>
                    <a href="http://www.traficoyservicios.com/esp/senalizacion-vial-vertical/senales-de-codigo-referencias/index.htm" class="collection-item" target="_blank">Tráfico y servicios</a>
                    <a href="http://www.autoescuela.tv/senyales-14-Senales" class="collection-item" target="_blank">Auto Escuela</a>
                </div>
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