<!DOCTYPE html>

<html>
    <head>
        <title>TODO supply a title</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        {{ HTML::script('assets/js/jquery-1.11.1.js') }}
        <script>
        jQuery("document").ready(function(){
            //$('.now').change(function() {
            //    $('#target').submit();
            //});
            
            $("#img").click(function(e) {    
                $("#file").click();
            });
            
        });
        </script>
    </head>
    
    <body>
        <div>TODO write content</div>
        
        <div class="mc-dr-profile-image">
        <img id="img" width="125" height="125" src="http://localhost/medicloud_v002/public/assets/images/mc-profile-img.png">
        </div>
        <br>
        <br>
        
<!--        {{ Form:: open(array('url' => 'app/auth/test_upload','files'=> true, 'id'=>"target")) }}-->
         {{ Form:: open(array('url' => 'app/auth/test_upload','files'=> true,'id'=>"target")) }}
        <!--<input type="file" id="file" name="file" onchange="this.form.submit();">-->
<!--        <input type="image" width="125" height="125" src="http://localhost/medicloud_v002/public/assets/images/mc-profile-img.png" width="30px"/>-->
         <input type="file" id="file" name="file" onchange="this.form.submit();" style="display: none">
<!--        <input type="submit" value="Submit">-->
        {{ Form:: close() }}
        
        <br><br>
        <?php
        if($img==1){
            echo '<img width="125" height="125" src="'.$image.'">';
        }
        ?>
    </body>
</html>
