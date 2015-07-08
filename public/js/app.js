const DIR_SIGNS_SAMPLES = "images/signs/samples/";

$(document).ready(function() {

    /*$("#previous_btn").click(function(){
            if(sample_index > 1){
                sample_index--;
                $("#sample_img").attr("src",DIR_SIGNS_SAMPLES + samples[sample_index].image);
                $("#classify_sample_btn").attr("data-sample",sample_index);
            }
    });*/

    /*$("#next_btn").click(function(){
        loadNextSample();
        if(sample_index < samples.length){
            sample_index++;
            $("#sample_img").attr("src",DIR_SIGNS_SAMPLES + samples[sample_index].image);
            $("#classify_sample_btn").attr("data-sample",sample_index);
        }
    });*/

    $(".signal-thumb").click(function(){
        if($(this).hasClass("signal-thumb-active")){
            $(this).removeClass("signal-thumb-active");
        }else{
            $(".signal-thumb-active").removeClass("signal-thumb-active");
            $(this).addClass("signal-thumb-active");
        }
    });

    $("#classify_sample_btn").click(function(event){
        event.preventDefault();
        var selected_class = $(".signal-thumb-active").attr("data-class");
        if(selected_class){
            var sample = $(this).attr("data-sample");
            $.ajax({
                type: "post",
                url: "classifier.php",
                data: {action : "classifySample", sample : sample, selected_class : selected_class},
                dataType: "json",
                success: function(result){
                    /*samples.splice(sample_index,1);
                    if(sample_index < samples.length){
                        sample_index++;
                        $("#sample_img").attr("src",DIR_SIGNS_SAMPLES + samples[sample_index].image);
                        $("#classify_sample_btn").attr("data-sample",sample_index);
                    }else{
                        $("#sample_img").attr("src","http://placehold.it/200x200?text=Empty");
                        $("#classify_sample_btn").attr("data-sample",0).attr("disabled",true);
                    }*/
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

    /*$("#previous_crop_sample_btn").click(function(){
         if(sample_index > 1){
             sample_index--;
             //$("#crop_img").attr("src",DIR_SIGNS_SAMPLES + samples[sample_index].image);
             $("#crop_sample_btn").attr("data-sample",sample_index);
             image.cropper("replace",DIR_SIGNS_SAMPLES + samples[sample_index-1].image);
         }
     });*/

    /*$("#next_crop_sample_btn").click(function(){
        //loadNextSampleToCrop();
         if(sample_index < samples.length){
             sample_index++;
             //$("#crop_img").attr("src",DIR_SIGNS_SAMPLES + samples[sample_index].image);
             $("#crop_sample_btn").attr("data-sample",sample_index);
             image.cropper("replace",DIR_SIGNS_SAMPLES + samples[sample_index-1].image);
         }
     });*/

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

});

function loadNextSampleToClassify(){
    $.ajax({
        type: "post",
        url: "classifier.php",
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

