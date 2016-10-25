const DIR_SIGNS = "images/signs/";
const DIR_SIGNS_SAMPLES = DIR_SIGNS+"samples/";
const DIR_SIGNS_CROPPED = DIR_SIGNS+"samples/cropped/";
const DIR_SIGNS_CLASSES = DIR_SIGNS+"classes/";

$(document).ready(function() {

    $(".button-collapse").sideNav();

    $(".signal-thumb").click(function(){
        toggleSignalThumb($(this));
    });

    $("#classify_sample_btn").click(function(event){
        event.preventDefault();
        var selected_class = $(".signal-thumb-active").attr("data-class");
        if(selected_class){
            var sample = $(this).attr("data-sample");
            $.ajax({
                type: "post",
                url: "annotation.php",
                data: {action : "classifySample", sample : sample, selected_class : selected_class},
                dataType: "json",
                success: function(result){
                    if(result.success == 1){
                        loadNextSampleToClassify();
                    }
                    var toastColor = result.success == 1 ? "toast-success" : "toast-danger";
                    Materialize.toast(result.msg,5000,toastColor);
                }
            });
        }else{
            Materialize.toast("Class not selected", 4000, "toast-danger");
        }

    });

    $("#crop_sample_btn").click(function(event){
        event.preventDefault();
        var sample = $(this).attr("data-sample");
        $.ajax({
            type: "post",
            url: "cropper.php",
            data: {action : 'cropSample', crop_data : crop_data, sample : sample},
            dataType: "json",
            success: function(result){
                if(result.success == 1){
                    loadNextSampleToCrop();
                }
                var toastColor = result.success == 1 ? "toast-success" : "toast-danger";
                Materialize.toast(result.msg,5000,toastColor);
            }
        });
    });

    $("#recommend_sample_btn").on("click",function(event){
        event.preventDefault();
        var sample = $(this).attr("data-sample");
        $.ajax({
            type: "post",
            url: "annotation.php",
            data: {action : 'recommendSampleClass', sample : $("#classify_sample_btn").attr("data-sample")},
            dataType: "json",
            success: function(result){
                if(result.success == 1){
                    var thumb = $(".signal-thumb[data-class='"+result.id+"']");
                    if(!thumb.hasClass("signal-thumb-active")){
                        toggleSignalThumb(thumb);
                    }
                    $('html, body').animate({
                        scrollTop: thumb.offset().top-80
                    },2000);
                }
                var toastColor = result.success == 1 ? "toast-success" : "toast-danger";
                Materialize.toast(result.msg,5000,toastColor);
            }
        });
    });

    $('#search').keyup(function() {
        var text = $(this).val();
        if(text.length > 0){
            var regex = new RegExp(text,"i");
            $(".signal-thumb > span").each(function(){
                if($(this).text().search(regex) < 0){
                    $(this).parents(".signal-thumb").hide();
                }else{
                    $(this).parents(".signal-thumb").show();
                }
            })
        }else{
            $(".signal-thumb").show();
        }
    });

});

function loadNextSampleToClassify(){
    $.ajax({
        type: "post",
        url: "annotation.php",
        data: {action : "loadNextSampleToClassify"},
        dataType: "json",
        success: function(result){
            if(result.success == 1){
                $("#sample_img").attr("src",DIR_SIGNS_SAMPLES + result.image);
                $("#classify_sample_btn").attr("data-sample",result.id);
            }else{
                Materialize.toast("Ops! Next sample could not be loaded", 4000, "toast-danger");
            }
        }
    });
}

function loadNextSampleToCrop(){
    $.ajax({
        type: "post",
        url: "cropper.php",
        data: {action : "loadNextSampleToCrop"},
        dataType: "json",
        success: function(result){
            if(result.success == 1){
                image.cropper("replace",DIR_SIGNS_SAMPLES + result.image);
                $("#crop_sample_btn").attr("data-sample",result.id);
            }else{
                Materialize.toast("Ops! Next sample could not be loaded", 4000, "toast-danger");
            }
        }
    });
}

var crop_data;
var image;

function initCropper(){

    image = $('#crop_img'),
    dataX = $('#data_x'),
    dataY = $('#data_y'),
    dataHeight = $('#data_height'),
    dataWidth = $('#data_width'),
    options = {
        // data: {
        //   x: 420,
        //   y: 60,
        //   width: 640,
        //   height: 360
        // },
        // strict: false,
        // responsive: false,
        // checkImageOrigin: false

        // modal: false,
        // guides: false,
        // center: false,
        // highlight: false,
        // background: false,

        // autoCrop: false,
        autoCropArea: 0.8,
        // dragCrop: false,
        // movable: false,
        // rotatable: false,
        zoomable: false,
        // touchDragZoom: false,
        // mouseWheelZoom: false,
        // cropBoxMovable: false,
        // cropBoxResizable: false,
        // doubleClickToggle: false,

        // minCanvasWidth: 320,
        // minCanvasHeight: 180,
        // minCropBoxWidth: 160,
        // minCropBoxHeight: 90,
        // minContainerWidth: 320,
        // minContainerHeight: 180,

        // build: null,
        // built: null,
        // dragstart: null,
        // dragmove: null,
        // dragend: null,
        // zoomin: null,
        // zoomout: null,

        aspectRatio: NaN,
        crop: function (data) {
            dataX.val(Math.round(data.x));
            dataY.val(Math.round(data.y));
            dataHeight.val(Math.round(data.height));
            dataWidth.val(Math.round(data.width));
            crop_data = data;
        }
    };
    image.cropper(options);
}

var page_load_class = 0;
var page_load_crop = 0;
var loading  = false;
var progress = $(".progress");

function loadResults(action,table_selector,page_load,href_edit){
    $.ajax({
        type: "post",
        url: "results.php",
        data: {action : action, page : page_load},
        dataType: "json",
        success: function(result){
            $.each(result,function(index,sample){
                $(table_selector).append(
                    "<tr>"+
                    "<td>"+sample.id+"</td>"+
                    "<td><img src='"+DIR_SIGNS_SAMPLES+sample.image+"' class='responsive-img' style='max-width: 150px;'/></td>"+
                    "<td><img src='"+(action === "loadClassificationResults" ? DIR_SIGNS_CLASSES+sample._class.image : DIR_SIGNS_CROPPED+sample.image)+"' class='responsive-img' style='max-width: 150px;'/></td>"+
                    "<td><a href='"+href_edit+"?id="+sample.id+"' class='waves-effect waves-light btn'><i class='material-icons left'>mode_edit</i>Edit</a></td>"+
                    "</tr>"
                );
            });
            progress.hide();
            loading = false;
            if(action === "loadClassificationResults"){
                page_load_class++;
            }else if(action === "loadCroppedResults"){
                page_load_crop++;
            }
        }
    }).fail(function(xhr, ajaxOptions, thrownError) {
        alert(thrownError);
        progress.hide();
        loading = false;
    });
}

function toggleSignalThumb(thumb){
    if(thumb.hasClass("signal-thumb-active")){
        thumb.removeClass("signal-thumb-active");
    }else{
        $(".signal-thumb-active").removeClass("signal-thumb-active");
        thumb.addClass("signal-thumb-active");
    }
}

