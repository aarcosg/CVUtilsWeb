<?php
$title = "Classes";
include_once("include/header.inc.php");
?>
<div class="section">
    <div class="container">
        <div class="row">
            <form id="new_class_form" class="col s12" action="#">
                <div class="row">
                    <div class="input-field col s4">
                        <input id="class_name" type="text" class="validate" required="true">
                        <label for="class_name">Class Name</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s4">
                        <select>
                            <option value="" disabled selected>Choose subclass</option>
                            <option value="1">Ceda el paso</option>
                            <option value="2">Dirección prohibida</option>
                            <option value="3">Indicación</option>
                            <option value="4">Obligación</option>
                            <option value="5">Peligro</option>
                            <option value="6">Prohibición</option>
                            <option value="7">Stop</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="file-field input-field col s4">
                        <input class="file-path validate" type="text"/>
                        <div class="btn orange">
                            <input type="file" accept="image/jpeg,image/png,image/gif" />
                            <span>Image file</span>
                    </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col s2">
                <button id="submit_class_btn" class="btn-large waves-effect waves-light" type="submit" name="action" form="new_class_form">
                    <i class="material-icons left">add_circle</i>
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
    });
</script>