<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <script src="http://localhost/medicloud_web/public/assets/js/jquery-1.11.1.js"></script>
<?php 
//echo Request::getHost();
echo '<br>';

echo "http://".$_SERVER[HTTP_HOST];?>        
<!--<script type="text/javascript" src="http://localhost:8000/faye/client.js"></script>-->
<script type="text/javascript" src="http://<?php echo $_SERVER[HTTP_HOST];?>:8000/faye/client.js"></script>
<script>
    var client = new Faye.Client('http://localhost:8000/faye'); 

jQuery("document").ready(function(){
    
    //jQuery(".nodeNav").click(function(){
    $('.nodeNav').off('click').on('click', function(){    
        
        //console.log('hi');
        NodeClick();
    });
    
    
    
    NodeClick();
    
});

function NodeClick(){  
    client.subscribe('/messag', function(message) {
      console.log(message.text);
      alert('hi');
    });
    client.publish('/messag', {
      text: 'call me node js'
    });
    
}

</script>
<title>TODO supply a title</title>  
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div class="nodeNav" id="" >Click me</div>
    </body>
</html>
