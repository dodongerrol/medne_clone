function FormValidation_ClinicDetails(){
    var validator = jQuery("#form-clinic-details-update").validate({
        rules: {
            name: {
                  required: true,
                  minlength: 3
              },
            address: {
                required: true,
                minlength: 2
            },
            city: {
                required: true,
                minlength: 2
            },
            country: {
                required: true,
                minlength: 2
            },
            postal: {
                required: true,
                minlength: 2
            },
            phone: {
                required: true,
                minlength: 8,
                number:true
            },
            email: {
                required: true,
                email:true
            }  
        },
        messages: {
          name: "Please specify name",
          address: "Please specify address",
          city:"Please specify city",
          country: "Please specify country",
          postal: "Please specify postal code",
          phone:"Please specify valid phone number",
          email:"Please specify valid email"
        }
     });
}


function FormValidation_ClinicProcedures(){
    var validator = jQuery("#form-clinic-procedures").validate({
        rules: {
            name: {
                  required: true,
                  minlength: 3
              },
              duration: {
                  required: true
                  //minlength: 2,
                  //number:true
              } 
        },
        messages: {
          name: "Please specify name",
          duration: "Please specify duration"
        }
     });
}

function FormValidation_AddDoctors(){
    var validator = jQuery("#form-clinic-add-doctors").validate({
        rules: {
            name: {
                  required: true,
                  minlength: 3
              },
            qualification: {
                required: true,
                minlength: 2
            },
            speciality: {
                required: true,
                minlength: 2
            },
            phone: {
                required: true,
                minlength: 8,
                maxlength: 10,
                number:true
            },
            email: {
                required: true,
                email:true
            }, 
            procedure: {
                required: true
            }
             
        },
        messages: {
          name: "Please specify name",
          qualification: "Please specify qualification",
          speciality:"Please specify doctor speciality",
          phone:"Please specify valid phone number",
          email:"Please specify valid email",
          procedure: "Please select a procedure"
        }
     });
}

function FormValidation_ClinicPasswordUpdate(){
    var validator = jQuery("#form-clinic-password-update").validate({
        rules: {
            oldpass: {
                  required: true,
                  minlength: 3
              },
            newpass: {
                required: true,
                minlength: 3
            },
            conpass: {
                equalTo: "#newpass"
            }
             
        },
        messages: {
          oldpass: "Please specify old password",
          newpass: "Please specify new password",
          conpass:"Please specify confirm password"
        }
     });
}

function FormValidation_Booking(){
    var validator = jQuery("#form-clinic-booking").validate({
        rules: {
            doctors_select: {
                  required: true
              },
            doctor_procedure: {
                required: true
            },
            booking_date: {
                required: true,
                minlength: 10
            },
            start_time: {
                required: true
                //minlength: 5
            },
            end_time: {
                required: true
                //minlength: 5
            },
            nric: {
                required: true,
                minlength: 5
            },
            name: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email:true
            },
            phone: {
                required: true,
                minlength: 8,
                number:true
            }
             
        },
        messages: {
          doctors_select: "Please select a doctor",
          doctor_procedure: "Please select a procedure",
          booking_date:"Booking date required",
          start_time:"Start time required",
          end_time:"End time required",
          nric:"NRIC required",
          name:"Name required",
          email:"Email required",
          phone:"Phone required"
        }
     });
}