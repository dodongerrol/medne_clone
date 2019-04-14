function promoCodeValidation(){
    var validator = jQuery("#promo-form").validate({
        rules: {
            code: {
                required: true,
            },
            active: {
                required: true,
            },
            amount: {
                  required: true,
                  minlength: 0,
                  // maxlength: 1000,
            }
        },
        messages: {
          code: "Please specify code name",
          active: "Please switch active state",
          amount: "Please enter amount greater than 0",
        }
     });
}