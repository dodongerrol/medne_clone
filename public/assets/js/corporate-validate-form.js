function corporateSignUpValidation(){
    var validator = jQuery("#corporate-form-signup").validate({
        rules: {
            first_name: {
                required: true,
            },
            last_name: {
                required: true,
            },
            email: {
                required: true,
                email:true
            },
            company_name: {
                required: true,
            },
            credit: {
                  required: true,
                  minlength: 1,
                  maxlength: 1000,
            }
        },
        messages: {
          first_name: "Please specify first name",
          last_name: "Please specify first name",
          email: "Please specify email",
          company_name: "Please specify company name",
          credit: "Only $1 - $1000 the company may have a credit",
        }
     });
}