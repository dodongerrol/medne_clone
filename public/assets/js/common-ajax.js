window.base_url = window.location.origin + '/app/';
window.base_loading_image = '<img src="'+ window.location.origin +'/assets/images/loading.svg" alt=""/>';

jQuery("document").ready(function(){
    // var protocol = jQuery(location).attr('protocol');
    // var hostname = jQuery(location).attr('hostname');
    // var folderlocation = $(location).attr('pathname').split('/')[1];
    // window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/app/';
    // window.image_url = protocol+'//'+hostname+'/'+folderlocation+'/public/';

//    window.base_url = 'http://'+jQuery(location).attr('hostname')+'/medicloud_web/public/app/';

    //console.log(base_url);

    doctorSignUpValidation();
    clinicSignUpValidation();
    resetPasswordValidation();
    userUpdateActiveAccount();


    $(document).on('click', '#doc-mobile-codes li', function(event) {
        var id = $(this).attr('id');
        $('#phone_code').val(id);
        console.log(id);
    });

    // update user account activate;
    jQuery('#done-update-user-active-accout').click(function( event ){
        event.preventDefault();
        var btn = $('#done-update-user-active-accout').text();
        var user = $('#user').val();
        var nric = $('#nric').val();
        var name = $('#name').val();
        var phone_code = $('#phone_code').val();
        var phone_number = $('#phone_number').val();
        var email = $('#email').val();

        dataValues = '&name='+name+'&user='+user+'&nric='+nric+'&phone_number='+phone_number+'&phone_code='+phone_code+'&email='+email;
        if(jQuery("#user-update-form").valid() ==true){
            $('#done-update-user-active-accout').attr('disabled', true);
            $('#done-update-user-active-accout').text('Updating...');


            $.ajax({
                url: base_url + 'update/user/account/activate',
                type: 'POST',
                data : dataValues,

                success: function (data){
                    console.log(data);
                    if(data == 1) {
                        $('#done-update-user-active-accout').fadeOut(500);
                        $('.body').slideUp(500);
                        $('#success-update').slideDown(500);
                    }
                    jQuery.unblockUI();
                }
              });
        }
    });

    jQuery("#Doctor-Signup").click(function(){
        var userid = jQuery(this).attr('userid');
        var email = jQuery("#Email").val();
        var password = jQuery("#Password").val();
        
        dataValues = 'userid='+userid+'&email='+email+'&password='+password;
        
        if(jQuery("#form-signup").valid() ==true){
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url + "auth/signup",
                data : dataValues,
   
                success : function(data){
                    if(data == 1){
                        window.location = base_url + "doctor/dashboard";
                    }else{
                        window.location = base_url + "auth/login";
                    }
                    jQuery.unblockUI();   
                }       
            }); 
        }
    });
    
    jQuery("#auth-login").click(function(){
        var email = jQuery("#Email").val();
        var password = jQuery("#Password").val();
        
        dataValues = '&email='+email+'&password='+password;
        
        if(jQuery("#form-signup").valid() ==true){
            jQuery.blockUI({ message: '<h1> '+ base_loading_image +' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url + "auth/loginnow",
                data : dataValues,
   
                success : function(data){

                    if(data ==2){
                        //window.location = base_url+"doctor/dashboard";
                        window.location = base_url+"doctor/home";
                    }else if(data ==3){
                        //window.location = base_url+"clinic/booking";
                        //window.location = base_url+"clinic/settings-dashboard";
                        window.location = base_url+"clinic/appointment-home-view";
                    }else{
                        jQuery("#ajax-error").html('<div class="alert alert-danger" role="alert">Please check your login credential</div>');
                        //window.location = base_url+"auth/login";
                    }
                    jQuery.unblockUI();   
                }       
            }); 
        }

        return false;
    });



// nhr    create new clinic

    $('#create-clinic').click(function(event) {

        var name = $('#name').val();
        var email = $('#email').val();
        var password = $('#password').val();

        dataValues = '&email='+email+'&password='+password;

        if(jQuery("#clinic-form-signup").valid() ==true){

            jQuery.ajax({
                type: "POST",
                url : base_url+"auth/newClinic",
                data : {name:name, email:email, password:password},
   
                success : function(data){

                    if (data == 0){

                        jQuery("#div_msg").html('<div class="alert alert-danger" role="alert"><b>Sorry, This email is already taken for a clinic.</b></div>');


                    }else {

                        $('#div_msg').html('<div class="alert alert-success" role="alert"><b>Your account has been created successfully!</b></div>');


                        setTimeout(function(){

                                jQuery.ajax({
                                    type: "POST",
                                    url : base_url+"auth/loginnow",
                                    data : dataValues,
                       
                                    success : function(data){

                                        if(data ==2){

                                            window.location = base_url+"doctor/home";
                                        }else if(data ==3){

                                            window.location = base_url+"clinic/appointment-home-view";
                                        }else{

                                            jQuery("#div_msg").html('<div class="alert alert-danger" role="alert"><b>We\'re sorry, but something went wrong</b></div>');

                                        }

                                    }       
                                });

                        }, 2000);

                    }
                    
                     

                }       
            });
               
        }

        return false;
    });












    /* Use          :   Used to reset password 
     * 
     */
    jQuery("#auth-forgot").click(function(){
        var email = jQuery("#Email").val();
        dataValues = '&email='+email;
        
        if(jQuery("#form-signup").valid() ==true){
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"auth/forgot-password",
                data : dataValues,
                success : function(data){
                    if(data==1){
                        jQuery("#div_msg1").html('<div class="alert alert-success" role="alert">Check your mail, we have sent instructions to reset your password</div>');
                    }else{
                        jQuery("#div_msg1").html('<div class="alert alert-danger" role="alert">Sorry ! We could not find any information</div>');
                    }
                    //console.log(data);
//                    if(data ==2){
//                        window.location = base_url+"doctor/dashboard";
//                    }else if(data ==3){
//                        window.location = base_url+"clinic/dashboard";
//                    }else{
//                        jQuery("#ajax-error").html("<div class='error'>Please check your login credential</div>");
//                        //window.location = base_url+"auth/login";
//                    }
                    jQuery.unblockUI();   
                }       
            }); 
        }

        return false;
    });
    
    /* Use          :   Used to update new password
     * By           :   AJAX
     */
    jQuery("#auth-reset").click(function(){
        var oldpass = jQuery("#OldPassword").val();
        var newpass = jQuery("#Password").val();
        var userid = jQuery(this).attr('userid');
        
        dataValues = 'userid='+userid+'&oldpass='+oldpass+'&newpass='+newpass;
        
        if(jQuery("#form-reset").valid() ==true){
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"auth/resetpassword",
                data : dataValues,
                success : function(data){
                    var arr = data.split('~');
                    if(arr[0]==1){
                        
                        jQuery("#div_msg2").html('<div class="alert alert-success" role="alert">Your password successfully updated, please login to the app using the new password</div>');

                        if (arr[1]==3) {
                            jQuery("#div_msg2").html('<div class="alert alert-success" role="alert">Your password successfully updated</div>');
                            setTimeout(function(){ 
                                window.location = base_url+"auth/login";
                            }, 3000);
                        }


                    }else if(arr[0]==2){
                        jQuery("#div_msg2").html('<div class="alert alert-danger" role="alert">We could not recognized your old password</div>');
                    }else{
                        jQuery("#div_msg2").html('<div class="alert alert-danger" role="alert">Sorry ! Please try again</div>');
                    }              
                    jQuery.unblockUI();   
                }       
            }); 
        }

        return false;
    });
    
    
    
    
});
