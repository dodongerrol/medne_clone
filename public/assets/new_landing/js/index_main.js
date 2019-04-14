if ($(window).width() > 768) {

    var one = $('#main-navbar').offset().top;
    var two = $('#second-section').offset().top;
    var	two_sec = $('#second-two-section')[0] ? $('#second-two-section').offset().top : 0;
    var	three = $('#third-section').offset().top;
    var	fourth = $('#fourth-section').offset().top;
    var fifth = false;
    var sixth = false;

    if( $('#fifth-section:visible').length > 0 ){
    	fifth = $('#fifth-section').offset().top;
    }
    if( $('#sixth-section:visible').length > 0 ){
    	sixth = $('#sixth-section').offset().top;
    }



    var $window = $(window);

    $window.scroll(function() {
        if ( $window.scrollTop() > one-300 && $window.scrollTop() < two-300) {
            $(".side-nav li").fadeIn();
            $(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(1)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#FFF'
    		});

        }else if ( $window.scrollTop() > two-300 && $window.scrollTop() < three-300 ) {
            if( $window.scrollTop() > two-300 && $window.scrollTop() < two_sec-150 ){
              $(".side-nav li").fadeIn();
              $(".side-nav li").removeClass('active');
              $(".side-nav li:nth-child(2)").addClass('active');
              $(".side-nav .nav>li>a ").css({
                  'color': '#000'
              });
            }

            if ( $window.scrollTop() > two_sec-300 && $window.scrollTop() < three-150 ) {
                $(".side-nav li").fadeIn();
                $(".side-nav li").removeClass('active');
                $(".side-nav li:nth-child(3)").addClass('active');
                $(".side-nav .nav>li>a ").css({
                    'color': '#000'
                });
            }
        }else if ( $window.scrollTop() > three-200 && $window.scrollTop() < fourth-200 ) {
        	$(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(4)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#FFF'
    		});
        }else if ( $window.scrollTop() > fourth-300 && !fifth) {
        	$(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(5)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#000'
    		});
        }else if ( $window.scrollTop() > fourth-300 && $window.scrollTop() < fifth-300) {
        	$(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(5)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#000'
    		});
        }else if ( $window.scrollTop() > fifth-300 && $window.scrollTop() < sixth-300) {
        	$(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(6)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#FFF'
    		});
        }else if ( $window.scrollTop() > sixth-300 ) {
        	$(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(7)").addClass('active');
            $(".side-nav .nav>li>a ").css({
    		    'color': '#000'
    		});
        }
    });


    if ( $window.scrollTop() > one-300 && $window.scrollTop() < two-300) {
        $(".side-nav li").hide();
        $(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(1)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#FFF'
    	});
    }else if ( $window.scrollTop() > two-300 && $window.scrollTop() < three-300 ) {
        if( $window.scrollTop() > two-300 && $window.scrollTop() < two_sec-150 ){
          $(".side-nav li").fadeIn();
          $(".side-nav li").removeClass('active');
          $(".side-nav li:nth-child(2)").addClass('active');
          $(".side-nav .nav>li>a ").css({
              'color': '#000'
          });
        }

        if ( $window.scrollTop() > two_sec-300 && $window.scrollTop() < three-150 ) {
            $(".side-nav li").fadeIn();
            $(".side-nav li").removeClass('active');
            $(".side-nav li:nth-child(3)").addClass('active');
            $(".side-nav .nav>li>a ").css({
                'color': '#000'
            });
        }
    }else if ( $window.scrollTop() > three-200 && $window.scrollTop() < fourth-200 ) {
    	$(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(4)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#FFF'
    	});
    }else if ( $window.scrollTop() > fourth-300 && !fifth) {
    	$(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(5)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#000'
    	});
    }else if ( $window.scrollTop() > fourth-300 && $window.scrollTop() < fifth-300) {
    	$(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(5)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#000'
    	});
    }else if ( $window.scrollTop() > fifth-300 && $window.scrollTop() < sixth-300) {
    	$(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(6)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#FFF'
    	});
    }else if ( $window.scrollTop() > sixth-300 ) {
    	$(".side-nav li").removeClass('active');
        $(".side-nav li:nth-child(7)").addClass('active');
        $(".side-nav .nav>li>a ").css({
    	    'color': '#000'
    	});
    }

}else{
    $(".side-nav").hide();
}

// $(".footer-container .nav-wrapper li a").click(function(){
//     console.log( $(this).closest("li") );
//     // $(".footer-container .nav-wrapper li .dropdown").hide();
// });

var ctr = 0;

$( ".navbar-toggle" ).click(function(){
    if( ctr == 0 ){
        $( ".body-container" ).addClass('sidemenu-show');
        $( ".sidemenu" ).addClass('show-sidemenu');
        $( "body" ).addClass('body-overflow-hide');
        $( "html" ).addClass('body-overflow-hide');
        ctr = 1;
    }else{
        $( ".body-container" ).removeClass('sidemenu-show');
        $( ".sidemenu" ).removeClass('show-sidemenu');
        $( "body" ).removeClass('body-overflow-hide');
        $( "html" ).removeClass('body-overflow-hide');
        ctr = 0;
    }
    
});