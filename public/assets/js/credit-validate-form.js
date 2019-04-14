function creditSignUpValidation(){
    var validator = jQuery("#user-form").validate({
        rules: {
            name: {
                required: true,
            },
            email: {
                required: true,
                email:true
            },
            credit: {
                  required: true,
                  minlength: 0,
                  maxlength: 1000,
            }
        },
        messages: {
          name: "Please specify name",
          email: "Please specify email",
          company_name: "Please specify company name",
          credit: "Only $0 - $1000 the company may have a credit",
        }
     });
}