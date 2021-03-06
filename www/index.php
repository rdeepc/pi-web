<!DOCTYPE html>
<html>
<head>
    <title>Robot</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1"/>
    <link type="text/css" media="screen" rel="stylesheet" href="jquery-cropbox/jquery.cropbox.css">
    <link rel="stylesheet" href="shootpi.css" type="text/css" />
    <style type="text/css">
        body {
            font-family : sans-serif;
            font-size   : 13px;
        }
        .results {
            font-family : monospace;
            font-size   : 20px;
        }
    </style>
    <script type="text/javascript" src="jquery-cropbox/jquery.min.js"></script>
    <script type="text/javascript" src="jquery-cropbox/hammer.js"></script>
    <script type="text/javascript" src="jquery-cropbox/jquery.mousewheel.js"></script>
    <script type="text/javascript" src="jquery-cropbox/jquery.cropbox.js"></script>
    <script type="text/javascript" defer>

        var image_results = {}, image_source = {};
        function do_cropper(){
            var image = $( '#pi_image' );
            var cropwidth = image.attr('cropwidth'),
                cropheight = image.attr('cropheight'),
                results = $('#results' ),
                x       = $('.cropX', results),
                y       = $('.cropY', results),
                w       = $('.cropW', results),
                h       = $('.cropH', results);

            image.cropbox( {width: cropwidth, height: cropheight, showControls: 'auto', maxZoom: 2 } )
                .on('cropbox', function( event, results, img ) {
                    image_source = img;
                    image_results = results;
                    x.text( results.cropX );
                    y.text( results.cropY );
                    w.text( results.cropW );
                    h.text( results.cropH );
                });
        }

        var tracking = false;
        function trackObject(){
            if(tracking){
                stopTrackObject();
            }

            tracking = true;
            // send coordinates to pi and start tracking
            $.post('tracking.php',{
                status: 1,
                crop: image_results,
                image: {width: image_source.width, height: image_source.height},
                minspeed: $('#minspeed').val(),
                maxspeed: $('#maxspeed').val(),
                trackingmethod: $('#trackingmethod').val(),
                objectwidth: $('#objectwidth').val()
            });
        }
        function stopTrackObject(){
            tracking = false;
            $.post('tracking.php',{status: 0});
        }


        function shootStill(btn){
            $(btn).prop('disabled',true);
            $.post('shootStill.php',function(data){
                if(data){
                    $('#cropper_holder').html('<img src="'+data+'" id="pi_image" cropwidth="200" cropheight="200">');
                    do_cropper();
                }
                $(btn).prop('disabled',false);
            });
        }


        function shootVideo(btn){
            $(btn).prop('disabled',true);
            $.post('video.php',function(data){
                $(btn).prop('disabled',false);
            });
        }
        function homeCamera(btn){
            $(btn).prop('disabled',true);
            $.post('home-camera.php',function(data){
                $(btn).prop('disabled',false);
            });
        }
        function centerCamera(btn){
            $(btn).prop('disabled',true);
            $.post('center-cam.php',{
                status: 1,
                crop: image_results,
                image: {width: image_source.width, height: image_source.height},
                minspeed: $('#minspeed').val(),
                maxspeed: $('#maxspeed').val(),
                trackingmethod: $('#trackingmethod').val(),
                objectwidth: $('#objectwidth').val()
            },function(data){
                $(btn).prop('disabled',false);
            });
        }


    </script>
</head>
<body>

<input type="button" value="Take Picture" onClick='shootStill(this);'>
<input type="button" value="Take Video" onClick='shootVideo(this);'>
<input type="button" id="track_button" value="Start Tracking" onClick='trackObject();'>
<input type="button" id="stop_track_button" value="Stop Tracking" onClick='stopTrackObject();'>
<input type="button" id="center_camera" value="Home Camera" onClick='homeCamera();'>
<input type="button" id="center_camera" value="Move Cam" onClick='centerCamera();'>
<br><br>
<div id="cropper_holder"></div>

<div id="results">
    <b>X</b>: <span class="cropX"></span>
    <b>Y</b>: <span class="cropY"></span>
    <b>W</b>: <span class="cropW"></span>
    <b>H</b>: <span class="cropH"></span>
</div>
Tracking Method: <select id="trackingmethod">
    <option value="cmt">CMT Python</option>
    <option value="cppmt-tes">CMT C++</option>
    <option value="dlibcpp">dlib C++</option>
</select> <br>
Object Width: <input type="number" value="10" id="objectwidth" style="width:50px">cm<br/>
Min Speed: <input type="number" value="11" id="minspeed" style="width:50px"><br/>
Max Speed: <input type="number" value="20" id="maxspeed" style="width:50px"><br/>
</body>
</html>