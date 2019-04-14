function userUpdateActiveAccount(){
    var validator = jQuery("#user-update-form").validate({
        rules: {
            nric: {
                required: true,
            },
            name: {
                required: true,
            },
            email: {
                required: true,
                email:true
            },
            phone_code: {
                required: true,
            },
            phone_number: {
                  required: true,
            }
        },
        messages: {
          name: "Please specify first name",
          nric: "Please specify NRIC",
          email: "Please specify email",
          phone_code: "Please specify phone code",
          phone_number: "Please specify phone number",
        }
     });
}