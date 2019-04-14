function doctorFormValidation(){
    var validator = jQuery("#form-doctor").validate({
        rules: {
            Name: {
                  required: true,
                  minlength: 3
              },
            Qualification: {
                  required: true,
                  minlength: 2
              },
            Specialty: {
                required: true,
                minlength: 2
            },
            Mobile: {
                required: true,
                minlength: 10,
                number:true
            },
            Email: {
                required: true,
                email:true
            }  
        },
        messages: {
          Name: "Please specify name",
          Qualification: "Please specify qualification",
          Specialty:"Please specify speciality",
          Mobile: "Please specify mobile number",
          Email:"Please specify valid email"
        }
     });
}

function doctorSignUpValidation(){
    var validator = jQuery("#form-signup").validate({
        rules: {
            Email: {
                required: true,
                email:true
            },
            Password: {
                  required: true,
                  minlength: 5
            }
        },
        messages: {
          Email: "Please specify email",
          Password: "Please specify password",
        }
     });
}

function clinicSignUpValidation(){
    var validator = jQuery("#clinic-form-signup").validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
                email:true
            },
            password: {
                  required: true,
                  minlength: 5
            }
        },
        messages: {
          name: "Please specify name",
          email: "Please specify email",
          password: "Please specify password",
        }
     });
}


function resetPasswordValidation(){
    var validator = jQuery("#form-reset").validate({
        rules: {
            OldPassword: {
                required: true
            },
            Password: {
                  required: true,
                  minlength: 5
            },
            ConPassword: {
                equalTo: "#Password"
              }
        },
        messages: {
          OldPassword: "Please specify existing password",
          Password: "Please specify password",
          ConPassword:"Please specify same password"
        }
     });
}


function BookingFormValidation(){
    var validator = jQuery("#form-booking").validate({
        rules: {
            user_name: {
                  required: true,
                  minlength: 3
              },
            user_nric: {
                  required: true,
                  minlength: 5
              },
            user_mobile: {
                required: true,
                minlength: 10,
                number:true
            },
            user_email: {
                required: true,
                email:true
            } 
        },
        messages: {
          user_name: "Please specify name",
          user_nric: "Please specify nric",
          user_mobile: "Please specify mobile number",
          user_email:"Please specify valid email"
        }
     });
}