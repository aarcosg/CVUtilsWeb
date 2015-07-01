$(document).ready(function() {

    $("#previous_btn").click(function(){
            if(sample_index > 0){
                sample_index--;
                $("#sample_img").attr("src",samples[sample_index]);
            }
        });

        $("#next_btn").click(function(){
            if(sample_index < samples.length){
                sample_index++;
                $("#sample_img").attr("src",samples[sample_index]);
            }
        });

        $(".signal-thumb").click(function(){
            if($(this).hasClass("signal-thumb-active")){
                $(this).removeClass("signal-thumb-active");
            }else{
                $(".signal-thumb-active").removeClass("signal-thumb-active");
                $(this).addClass("signal-thumb-active");
            }
        });

        $("#save_btn").click(function(){
           Materialize.toast("Image classification saved", 4000);
        });

    });
