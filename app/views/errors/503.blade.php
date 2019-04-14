<!DOCTYPE html>
<html>
    <head>
        <title>Ooops! MediCloud Says</title>
        <link rel="shortcut icon" href="{{ asset('assets/images/Medicloud-Favicon_16x16px.ico') }}" type="image/ico">
        {{ HTML::style('assets/css/animate.min.css') }}
        {{ HTML::style('assets/css/bootstrap/css/bootstrap.css') }}
        {{ HTML::script('assets/bower_components/jquery/dist/jquery.min.js') }}
        {{ HTML::script('assets/css/bootstrap/js/bootstrap.min.js') }}
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #176AAD;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                margin-bottom: 40px;
            }
            #logo {
                width: 10%;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <img src="{{ asset('e-template-img/mednefits logo v3 (blue-box) LARGE.png') }}" id="logo" hidden>
                <div class="title">The Page you are looking does not exist.</div>
            </div>
        </div>
    </body>

    <script type="text/javascript">
        $(document).ready(function( ){
            setTimeout(function() {
                $('.title').addClass('animated shake');
                $('.title').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function( ){
                    $('#logo').fadeIn().addClass('animated bounceInUp');
                });
            }, 1000);
        })
    </script>
</html>
