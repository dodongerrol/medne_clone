window.base_image_url = "https://medicloud.sg/medicloud_web/public/assets/";
window.base_loading_image = '<img src="https://medicloud.sg/medicloud_web/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';

jQuery("document").ready(function () {
    jQuery('.pop1').attr('data-bpopup', '{"follow":["false,false"]}');
    /*jQuery('element_to_pop_up').bPopup({
     follow: [false, false], //x, y
     position: [150, 400] //x, y
     });
     */
    //This is for clinic details update 
    FormValidation_ClinicDetails();

    //This is for clinic add procedures
    FormValidation_ClinicProcedures();

    //Clinic password update
    FormValidation_ClinicPasswordUpdate()

    //Update clinic details 
    Clinic_Details_Update();

    //Add new procedures
    Clinic_Add_Procedures();

    //Delete Procedures
    DeleteProcedures();

    //Upload doctor profile image
    DoctorProfileImageUpload();

    //UPload Clinic profile image
    ClinicProfileImageUpload();

    //Add new Doctors
    Clinic_Add_Doctors();
    Clinic_Update_Doctors();

    //This valiation when add new doctors
    FormValidation_AddDoctors();

    //Delete a doctor from view
    DeleteDoctorsFromView();

    //Update clinic password
    ClinicPasswordUpdate();

    //Delete Clinic Opening Times
    DeleteClinicOpeningTimes();

    //Add Clinic Holidays
    AddClinicDoctorHolidays();

    //Delete Clinic Holidays
    DeleteClinicHolidays();

    //Load doctor availability
    LoadDoctorAvailability();

    //Add Clinic and Doctor Time chedule
    AddClinicDoctorTimesChedule();

    //Holiday time selections
    ClinicDoctorHolidaySelections();

    //Repeat time action for clinic and doctor 
    RepeatTimeActions();

    //Used to Opend booking form
    OpenBookingForm();
    //Apply when changing doctor procedure
    ChangeDoctorProcedure();
    //When selecting a doctor 
    ChangeDoctorSelect();
    //When selelcting a date
    MainDateSelections();
    //When creating doctor list view
    CreateDoctorListView();
    // sync oauth credentials
    syncCredentials();
    //revoke credentials;
    revokeToken();
    chekGmail();

    var protocol = jQuery(location).attr('protocol');
    var hostname = jQuery(location).attr('hostname');
    var folderlocation = $(location).attr('pathname').split('/')[1];
    window.base_url = protocol + '//' + hostname + '/' + folderlocation + '/public/app/';
    window.img_url = protocol + '//' + hostname + '/' + folderlocation + '/public/assets/images/';


    $('#widget_code').text(getWidgetUrl(base_url, img_url));
     

    jQuery("#clinic-profile-img").click(function () {
        jQuery("#clinic-profile-file").click();
    });

    jQuery("#doctor-profile-img").click(function () {
        jQuery("#doctor-profile-file").click();
    });


    //For opening times
    jQuery('#opening-times-custom').attr("checked", "checked");
    jQuery('#checkbox1').attr("checked", "checked");
    //jQuery('#custom-opening-time-enable').hide();  
    //jQuery('#custom-opening-time-disable').show();
    //jQuery("#timeOpen").attr("disabled", "disabled");
    //jQuery("#timeClose").attr("disabled", "disabled");

    jQuery("#opening-times-24").click(function () {
        jQuery("#timeOpen").attr("disabled", "disabled");
        jQuery("#timeClose").attr("disabled", "disabled");
        jQuery("#timeOpen").val('');
        jQuery("#timeClose").val('');
        jQuery(".status-opening-times").hide();
        //jQuery('#custom-opening-time-enable').hide();
        //jQuery('#custom-opening-time-disable').show();
    });
    jQuery("#opening-times-custom").click(function () {
        jQuery("#timeOpen").removeAttr("disabled");
        jQuery("#timeClose").removeAttr("disabled");
        //jQuery('#custom-opening-time-enable').show();
        //jQuery('#custom-opening-time-disable').hide();
        jQuery(".status-opening-times").show();
    });

    //for holiday times
    jQuery('#radio-fulday').attr("checked", "checked");
    jQuery("#timeStart").attr("disabled", "disabled");
    jQuery("#timeEnd").attr("disabled", "disabled");
    jQuery(".holiday-status-section").hide();


    jQuery(document).on("click", 'div[class^="week-selection"]', function (e) {
        jQuery(this).attr('class', 'week-selection-remove day-box');
        jQuery(this).attr('week', 1);
    });
    jQuery(document).on("click", 'div[class^="week-selection-remove"]', function (e) {
        jQuery(this).attr('class', 'week-selection day-box-gray');
        jQuery(this).attr('week', 0);
    });

    jQuery("#password-message").hide();


//    jQuery("#clinic-times-added").click(function(){ 
//        var doctorid = jQuery(this).attr('curdoctorid');
//        var wemon = jQuery('#weekmon').attr('week');
//        var wetus = jQuery('#weektus').attr('week');
//        var wewed = jQuery('#weekwed').attr('week');
//        var wethu = jQuery('#weekthu').attr('week');
//        var wefri = jQuery('#weekfri').attr('week');
//        var wesat = jQuery('#weeksat').attr('week');
//        var wesun = jQuery('#weeksun').attr('week');
//        var urlaccess;
//        if(doctorid){
//            urlaccess = base_url+"clinic/availability-times";
//        }else{
//            urlaccess = base_url+"clinic/opening-times";
//        }
//        var timetype = jQuery('input:radio[name=radio]:checked').val();
//        var timerepeat = jQuery('#checkbox1').is(':checked');
//        var starttime = jQuery('#timeOpen').val();
//        var endtime = jQuery('#timeClose').val();
//        
//        
//        dataValues = 'doctorid='+doctorid+'&timetype='+timetype+'&wemon='+wemon+'&wetus='+wetus+'&wewed='+wewed+'&wethu='+wethu+'&wefri='+wefri+'&wesat='+wesat+'&wesun='+wesun+'&timerepeat='+timerepeat+'&starttime='+starttime+'&endtime='+endtime;
//        
//        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
//        jQuery.ajax({
//            type: "POST",
//            url : urlaccess,
//            data : dataValues,
//            success : function(data){
//                console.log(data);
//                if(data!=0){
//                    /*jQuery("#load-opening-times-ajax").html(data);
//                    
//                    jQuery('#weekmon').attr('class','week-selection day-box-gray');
//                    jQuery('#weekmon').attr('week',0);
//                    jQuery('#weektus').attr('class','week-selection day-box-gray');
//                    jQuery('#weektus').attr('week',0);
//                    jQuery('#weekwed').attr('class','week-selection day-box-gray');
//                    jQuery('#weekwed').attr('week',0);
//                    jQuery('#weekthu').attr('class','week-selection day-box-gray');
//                    jQuery('#weekthu').attr('week',0);
//                    jQuery('#weekfri').attr('class','week-selection day-box-gray');
//                    jQuery('#weekfri').attr('week',0);
//                    jQuery('#weeksat').attr('class','week-selection day-box-gray');
//                    jQuery('#weeksat').attr('week',0);
//                    jQuery('#weeksun').attr('class','week-selection day-box-gray');
//                    jQuery('#weeksun').attr('week',0);
//                    jQuery("#timeOpen").val('');
//                    jQuery("#timeClose").val(''); */
//                }
//                
//                jQuery.unblockUI();   
//            }       
//        }); 
//    });

    jQuery("#content-5").mCustomScrollbar({
        axis: "x",
        theme: "dark-thin",
        autoExpandScrollbar: true,
        advanced: {autoExpandHorizontalScroll: true}
    });

    //jQuery('element_to_pop_up').bPopup({
    //   follow: [false, false] //x, y
    //position: [150, 400] //x, y
    //});

//End of Document Ready    
});


/*
 * 
 * @returns {undefined}
 */
function ClinicDoctorHolidaySelections() {
    //Enable and disable based on selected holiday options
    jQuery("input:radio[name='radio-holiday']").click(function () {
        var isSelected = jQuery(this).val();
        if (isSelected == 0) {
            jQuery("#timeStart").attr("disabled", "disabled");
            jQuery("#timeEnd").attr("disabled", "disabled");
            jQuery("#timeStart").val('');
            jQuery("#timeEnd").val('');
            jQuery(".holiday-status-section").hide();
            //jQuery("#timeEnd").hide(); 
        } else {
            jQuery("#timeStart").removeAttr("disabled");
            jQuery("#timeEnd").removeAttr("disabled");
            jQuery(".holiday-status-section").show();
            // jQuery("#timeEnd").show();
        }
    });
}

/* Use          :   Used to update clinic detasils by ajax
 * 
 * @returns {undefined}
 * 
 */
function Clinic_Details_Update() {
    jQuery('#clinic-details-updated').click(function () {
        var clinicid = jQuery(this).attr('clinicid');
        var name = jQuery("#name").val();
        var address = jQuery("#address").val();
        var city = jQuery("#city").val();
        var image = jQuery("#update-image").val();
        var state = jQuery("#state").val();
        var country = jQuery("#country").val();
        var postal = jQuery("#postal").val();
        var description = jQuery("#description").val();
        var code = jQuery("#code").val();
        var phone = jQuery("#phone").val();
        var email = jQuery("#email").val();
        var website = jQuery("#website").val();
        var title = jQuery("#title").val();


        dataValues = 'clinicid=' + clinicid + '&name=' + name + '&address=' + address + '&city=' + city + '&image=' + image + '&state=' + state + '&country=' + country + '&postal=' + postal + '&description=' + description + '&code=' + code + '&phone=' + phone + '&email=' + email + '&website=' + website + '&title=' + title;

        if (jQuery("#form-clinic-details-update").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/clinic-profile-update",
                data: dataValues,

                success: function (data) {
                    //console.log(data);
                    //if(data ==1){
                    //    window.location = base_url+"doctor/dashboard";
                    //}else{
                    //    window.location = base_url+"auth/login";
                    //}
                    jQuery.unblockUI();
                }
            });
        }

    });
}

/*  Use         :   Used to add new procedure to clinic 
 * 
 * @returns {undefined}
 * 
 */
function Clinic_Add_Procedures() {
    jQuery('#clinic-add-procedures').click(function () {
        var clinicid = jQuery(this).attr('clinicid');
        var name = jQuery("#name").val();
        var duration = jQuery("#duration").val();
        var price = jQuery("#price").val();

        dataValues = 'clinicid=' + clinicid + '&name=' + name + '&duration=' + duration + '&price=' + price;

        if (jQuery("#form-clinic-procedures").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/add-procedure",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        jQuery("#name").val('');
                        jQuery("#duration").val('');
                        jQuery("#price").val('');
                        jQuery("#load-procedures-ajax").html(data);
                    } else {
                        //Error here
                    }
                    jQuery.unblockUI();
                }
            });
        }

    });
}


/* Use          :    Used to delete a procedure
 * Access       :   AJAX
 * 
 */
function DeleteProcedures() {
    //jQuery('#delete-procedures').click(function() {
    //jQuery('div[id^="delete-procedures"]').on('click', function() {   
    jQuery(document).on("click", 'div[id^="delete-procedures"]', function (e) {
        var procedureid = jQuery(this).attr('procedure_id');

        dataValues = 'procedureid=' + procedureid;
        if (confirm('Are you sure you want to Delete this Procedure ?')) {
            //if(jQuery("#form-clinic-procedures").valid() ==true){
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/delete-procedure",
                data: dataValues,

                success: function (data) {
                    if (data == 0) {
                        alert("Something went wrong! Please try again");
                    }else if (data == 5) {
                        alert("Sorry! you have valid booking on this Procedure");
                    }else{
                        jQuery("#load-procedures-ajax").html(data);
                    } 
                    jQuery.unblockUI();
                }
            });
        }
    });
}

/* Use      : Used to uplaod doctor profile image
 * 
 * 
 */
function DoctorProfileImageUpload() {
    jQuery('#doctor-profile-file').change(function () {
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        var formData = new FormData();
        formData.append('file', $('#doctor-profile-file')[0].files[0]);

        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/clinic-image-upload",
            data: formData,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            success: function (data) {
                if (data != 0) {
                    jQuery("#doctor-profile-img").attr('src', data['img']);
                    jQuery("#update-image").attr('value', data['img']);
                }
                jQuery.unblockUI();
            }
        });
    });
}

/* Use      :   Used to uplaod clinic profile image
 * 
 * 
 */
function ClinicProfileImageUpload() {
    jQuery('#clinic-profile-file').change(function () {
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        var formData = new FormData();
        formData.append('file', $('#clinic-profile-file')[0].files[0]);

        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/clinic-image-upload",
            data: formData,
            processData: false,
            contentType: false,
            enctype: 'multipart/form-data',
            success: function (data) {
                if (data != 0) {
                    jQuery("#clinic-profile-img").attr('src', data['img']);
                    jQuery("#update-image").attr('value', data['img']);
                }
                jQuery.unblockUI();
            }
        });
    });
}


function Clinic_Add_Doctors() {
    jQuery('#add-new-doctors').click(function () {
        var clinicid = jQuery(this).attr('clinicid');
        var name = jQuery("#name").val();
        var qualification = jQuery("#qualification").val();
        var speciality = jQuery("#speciality").val();
        var image = jQuery("#update-image").val();
        var code = jQuery("#code").val();
        var phone = jQuery("#phone").val();
        var emergency_code = jQuery("#emergency-code").val();
        var emergency_phone = jQuery("#emergency-phone").val();
        var email = jQuery("#email").val();
        var procedure = jQuery("#procedure").val();
        //console.log(procedure);
        dataValues = 'clinicid=' + clinicid + '&name=' + name + '&qualification=' + qualification + '&speciality=' + speciality + '&image=' + image + '&code=' + code + '&phone=' + phone + '&emergency_code=' + emergency_code + '&emergency_phone=' + emergency_phone + '&procedure=' + procedure + '&email=' + email;

        if (jQuery("#form-clinic-add-doctors").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/add-doctor",
                data: dataValues,
                success: function (data) {
                    //console.log(data);
                    if (data == 2) {
                        jQuery("#header-nortification").addClass("notify5");
                        jQuery("#header-nortification").html("This email already taken");
                    }else if (data == 0) {
                        jQuery("#header-nortification").addClass("notify5");
                        jQuery("#header-nortification").html("There was a problem please try again....");
                    }else{
                        jQuery("#header-nortification").addClass("notify4");
                        jQuery("#header-nortification").html("Doctor added successfully");
                        jQuery("#name").val('');
                        jQuery("#qualification").val('');
                        jQuery("#speciality").val('');
                        jQuery("#phone").val('');
                        jQuery("#emergency-phone").val('');
                        jQuery("#email").val('');
                        jQuery("#procedure").val('');
                        jQuery("#update-image").val('');
                        jQuery("#doctor-profile-img").attr('src', 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1452582018/img-portfolio-place_i3crkv.png');
                    }

                    jQuery.unblockUI();
                }
            });
        }

    });
}
function Clinic_Update_Doctors() {
    jQuery('#update-doctors-details').click(function () {
        var doctorid = jQuery(this).attr('doctorid');
        var name = jQuery("#name").val();
        var qualification = jQuery("#qualification").val();
        var speciality = jQuery("#speciality").val();
        var image = jQuery("#update-image").val();
        var code = jQuery("#code").val();
        var phone = jQuery("#phone").val();
        var emergency_code = jQuery("#emergency-code").val();
        var emergency_phone = jQuery("#emergency-phone").val();
        var email = jQuery("#email").val();
        var procedure = jQuery("#procedure").val();
        //console.log(procedure);
        //dataValues = 'clinicid='+clinicid+'&name='+name+'&qualification='+qualification+'&speciality='+speciality+'&image='+image+'&code='+code+'&phone='+phone+'&emergency_code='+emergency_code+'&emergency_phone='+emergency_phone+'&procedure='+procedure+'&email='+email;
        dataValues = 'doctorid=' + doctorid + '&name=' + name + '&qualification=' + qualification + '&speciality=' + speciality + '&image=' + image + '&code=' + code + '&phone=' + phone + '&emergency_code=' + emergency_code + '&emergency_phone=' + emergency_phone + '&procedure=' + procedure + '&email=' + email;

        if (jQuery("#form-clinic-add-doctors").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",

                url : base_url+"clinic/update-doctor",
                data : dataValues,
                success : function(data){
                    //console.log(data);
                    if(data !=0){

                        jQuery("#header-nortification").addClass("notify4");
                        jQuery("#header-nortification").html("Doctor updated successfully");

                        //jQuery("#name").val('');
                        //jQuery("#qualification").val('');
                        //jQuery("#speciality").val('');
                        //jQuery("#phone").val('');   
                        //jQuery("#emergency-phone").val('');
                        //jQuery("#email").val('');
                        //jQuery("#procedure").val('');
                        //jQuery("#update-image").val('');
                        //jQuery("#doctor-profile-img").attr('src','https://medicloud.sg/medicloud_web/public/assets/images/img-portfolio-place.png');
                    } else {
                        jQuery("#header-nortification").addClass("notify5");
                        jQuery("#header-nortification").html("There was a problem please try again....");
                    }

                    jQuery.unblockUI();
                }
            });
        }

    });
}


/* Use          :    Used to delete a doctor
 * Access       :   AJAX
 * 
 */
function DeleteDoctorsFromView() {
    //jQuery('#delete-procedures').click(function() {
    //jQuery('div[id^="delete-procedures"]').on('click', function() {   
    jQuery(document).on("click", 'div[id^="delete-doctor-view"]', function (e) {
        var doctorid = jQuery(this).attr('doctorid');

        dataValues = 'doctorid=' + doctorid;
        if (confirm('Are you sure you want to Delete this Doctor ?')) {
            //if(jQuery("#form-clinic-procedures").valid() ==true){
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/delete-doctor",
                data: dataValues,

                success: function (data) {
                    if (data == 0) {
                        alert("Something went wrong! Please try again");
                    }else if (data == 5) {
                        alert("Sorry! this doctor has valid bookings");
                    }else{
                        jQuery("#load-doctor-view-ajax").html(data);
                    } 
                    jQuery.unblockUI();
                }
            });
        }
    });
}


/* Use          :    Used to update doctor password
 * Access       :    AJAX
 * 
 */
function ClinicPasswordUpdate() {
    jQuery('#clinic-update-password').click(function () {
        var clinicuserid = jQuery(this).attr('clinicuserid');
        var oldpass = jQuery("#oldpass").val();
        var newpass = jQuery("#newpass").val();

        dataValues = 'clinicuserid=' + clinicuserid + '&oldpass=' + oldpass + '&newpass=' + newpass;

        if (jQuery("#form-clinic-password-update").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/update-password",
                data: dataValues,
                success: function (data) {
                    console.log(data);
                    if (data != 0) {
                        jQuery("#password-message").html('Password updated');
                        jQuery("#oldpass").val('');
                        jQuery("#newpass").val('');
                        jQuery("#conpass").val('');
                        window.location = base_url + "auth/logout";
                    } else {
                        jQuery("#password-message").html('There was an error, please try again...');
                    }

                    jQuery.unblockUI();
                }
            });
        }

    });
}

function DeleteClinicOpeningTimes() {
    jQuery(document).on("click", 'div[id^="delete-opening-times"]', function (e) {
        var clinictimeid = jQuery(this).attr('clinictimeid');
        var doctorid = jQuery(this).attr('doctorid');
        var mydoctorid;
        if (doctorid) {
            mydoctorid = doctorid;
        } else {
            mydoctorid = 0;
        }
        dataValues = 'clinictimeid=' + clinictimeid + '&doctorid=' + mydoctorid;
        if (confirm('Are you sure you want to Delete this Time ?')) {
            //if(jQuery("#form-clinic-procedures").valid() ==true){
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/delete-opening-times",
                data: dataValues,

                success: function (data) {
                    if (data == 0) {
                        alert("Something went wrong! Please try again");
                    }else if (data == 5) {
                        alert("Sorry! You have valid booking on this time schedule");
                    } else {
                        jQuery("#load-opening-times-ajax").html(data);
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}


function AddClinicDoctorHolidays() {
    jQuery('#add-clinic-holidays').click(function () {
        //var clinicuserid = jQuery(this).attr('clinicuserid');
        var holidaytype = jQuery('input:radio[name=radio-holiday]:checked').val();
        var dateholiday = jQuery("#date-holiday").val();
        var timestart = jQuery("#timeStart").val();
        var timeend = jQuery("#timeEnd").val();
        var doctorid = jQuery(this).attr('doctorid');
        var mydoctorid;
        if (doctorid) {
            mydoctorid = doctorid;
        } else {
            mydoctorid = 0;
        }
        dataValues = 'doctorid=' + mydoctorid + '&holidaytype=' + holidaytype + '&dateholiday=' + dateholiday + '&timestart=' + timestart + '&timeend=' + timeend;

        //if(jQuery("#form-clinic-password-update").valid() ==true){
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/clinic-holidays",
            data: dataValues,
            success: function (data) {
                if (data != 0) {
                    if (mydoctorid != 0) {
                        jQuery("#load-doctor-holiday-ajax").html(data);
                    } else {
                        jQuery("#load-clinic-holiday-ajax").html(data);
                    }

                    jQuery("#date-holiday").val('');
                    jQuery("#timeStart").val('');
                    jQuery("#timeEnd").val('');
                } else {

                }

                jQuery.unblockUI();
            }
        });
        //}  
    });
}


function DeleteClinicHolidays() {
    jQuery(document).on("click", 'div[id^="delete-clinic-holiday"]', function (e) {
        var holidayid = jQuery(this).attr('clinicholidayid');
        var doctorin = jQuery(this).attr('doctorin');
        var partyid = jQuery(this).attr('partyid');
        dataValues = 'holidayid=' + holidayid + '&doctorin=' + doctorin + '&partyid=' + partyid;
        if (confirm('Are you sure you want to Delete this Clinic Holiday ?')) {
            //if(jQuery("#form-clinic-procedures").valid() ==true){
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/delete-clinic-holiday",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        if (doctorin == 1) {
                            jQuery("#load-doctor-holiday-ajax").html(data);
                        } else {
                            jQuery("#load-clinic-holiday-ajax").html(data);
                        }
                    } else {
                        //Error here
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}

function LoadDoctorAvailability() {
    jQuery("#load-doctors-availability").change(function () {
        //jQuery("#load-doctor-availability-ajax").html('hi');  
        var doctorid = jQuery(this).val();
        //console.log(doctorid);
        dataValues = 'doctorid=' + doctorid;
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/load-doctor-availability",
            data: dataValues,

            success: function (data) {
                if (data != 0) {
                    jQuery("#load-doctor-availability-ajax").html(data);
                    LoadDoctorAvailability();
                    AddClinicDoctorTimesChedule();
                    ClinicDoctorHolidaySelections();
                    AddClinicDoctorHolidays();
                    RepeatTimeActions();
                    //for holiday times selections
                    jQuery('#radio-fulday').attr("checked", "checked");
                    jQuery("#timeStart").attr("disabled", "disabled");
                    jQuery("#timeEnd").attr("disabled", "disabled");
                    jQuery(".holiday-status-section").hide();
                } else {
                    //Error here
                }
                jQuery.unblockUI();
            }
        });
    });
}

//function AddClinicDoctorTimes(){
//    
//}
//clinic-doctor-times-added

function AddClinicDoctorTimesChedule() {
    jQuery("#clinic-times-added").click(function () {
        var doctorid = jQuery(this).attr('curdoctorid');
        var wemon = jQuery('#weekmon').attr('week');
        var wetus = jQuery('#weektus').attr('week');
        var wewed = jQuery('#weekwed').attr('week');
        var wethu = jQuery('#weekthu').attr('week');
        var wefri = jQuery('#weekfri').attr('week');
        var wesat = jQuery('#weeksat').attr('week');
        var wesun = jQuery('#weeksun').attr('week');
        var timetype = jQuery('input:radio[name=radio]:checked').val();
        var urlaccess;
        if (doctorid) {
            urlaccess = base_url + "clinic/availability-times";
            timetype =1;
        } else {
            urlaccess = base_url + "clinic/opening-times";

        }        

        var timerepeat = jQuery('#checkbox1').is(':checked');
        var starttime = jQuery('#timeOpen').val();
        var endtime = jQuery('#timeClose').val();
        
    if((wemon==1 || wetus==1 || wewed==1 || wethu==1 || wefri==1 || wesat==1 || wesun==1) && ((timetype == 0) || (timetype == 1 && starttime !='' && endtime !=''))){

        dataValues = 'doctorid=' + doctorid + '&timetype=' + timetype + '&wemon=' + wemon + '&wetus=' + wetus + '&wewed=' + wewed + '&wethu=' + wethu + '&wefri=' + wefri + '&wesat=' + wesat + '&wesun=' + wesun + '&timerepeat=' + timerepeat + '&starttime=' + starttime + '&endtime=' + endtime;

        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: urlaccess,
            data: dataValues,
            success: function (data) {
                if(data == 2){
                    alert("Time is overlapping with existing time, Please correct it and try again");
                }else if(data == 0){
                    alert("Something went wrong, Please try again");
                }else{

                //if (data != 0) {
                    jQuery("#load-opening-times-ajax").html(data);
                    /*if(doctorid){
                     jQuery("#load-doctors-time-ajax").html(data);
                     }else{
                     jQuery("#load-opening-times-ajax").html(data);
                     }*/


                    jQuery('#weekmon').attr('class', 'week-selection day-box-gray');
                    jQuery('#weekmon').attr('week', 0);
                    jQuery('#weektus').attr('class', 'week-selection day-box-gray');
                    jQuery('#weektus').attr('week', 0);
                    jQuery('#weekwed').attr('class', 'week-selection day-box-gray');
                    jQuery('#weekwed').attr('week', 0);
                    jQuery('#weekthu').attr('class', 'week-selection day-box-gray');
                    jQuery('#weekthu').attr('week', 0);
                    jQuery('#weekfri').attr('class', 'week-selection day-box-gray');
                    jQuery('#weekfri').attr('week', 0);
                    jQuery('#weeksat').attr('class', 'week-selection day-box-gray');
                    jQuery('#weeksat').attr('week', 0);
                    jQuery('#weeksun').attr('class', 'week-selection day-box-gray');
                    jQuery('#weeksun').attr('week', 0);
                    jQuery("#timeOpen").val('');
                    jQuery("#timeClose").val('');
                    RepeatTimeActions();
                }

                jQuery.unblockUI();
            }
        });
        }else{
            alert("Please check your settings and try again")
        }
    });
}

function RepeatTimeActions() {
    jQuery("#repeat-times-action").click(function () {
        var repeatid = jQuery(this).attr('repeatid');
        var managetimeid = jQuery(this).attr('managetimeid');
        var confirmation;
        if (repeatid == 1) {
            confirmation = 'Are you sure you want to Stop auto repeat ?';
        } else {
            confirmation = 'Are you sure you want to Start auto repeat ?';
        }
        dataValues = 'repeatid=' + repeatid + '&managetimeid=' + managetimeid;

        if (confirm(confirmation)) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/repeat-action",
                data: dataValues,

                success: function (data) {
                    console.log(data);
                    if (data != 0) {
                        jQuery("#load-repeat-ajax").html(data);
                        RepeatTimeActions();

                        //jQuery("#load-doctor-availability-ajax").html(data);
                        //LoadDoctorAvailability();
                        //AddClinicDoctorTimesChedule();
                        //ClinicDoctorHolidaySelections();
                        //AddClinicDoctorHolidays();
                        //for holiday times selections
                        //jQuery('#radio-fulday').attr("checked", "checked");
                        //jQuery("#timeStart").attr("disabled", "disabled");
                        //jQuery("#timeEnd").attr("disabled", "disabled");

                    } else {
                        //Error here
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });

}


/* Use        : Use to load booking page
 * Access   : public
 */
function OpenBookingForm() {
    jQuery('div[class^="show-open-times"]').on('click', function (e) {
        var bookingid = jQuery(this).attr('bookingid');

        var starttime = jQuery(this).attr('id');
        var doctorid = jQuery(this).attr('doctorid');
        var clinictimeid = jQuery(this).attr('clinictimeid');

        var bookingdate = jQuery(this).attr('bookingdate');
        var doctortype = jQuery(this).attr('doctortype');

        var opentype = jQuery(this).attr('opentype');
        var endurl, sendvariable;
        if (opentype == 1 || opentype == 2) {
            endurl = "clinic/open-booking-update";
            sendvariable = 'bookingid=' + bookingid;
        } else {
            endurl = "clinic/open-booking-page";
            sendvariable = 'clinictimeid=' + clinictimeid + '&doctorid=' + doctorid + '&starttime=' + starttime + '&bookingdate=' + bookingdate;
        }

        dataValues = sendvariable;
        //if (confirm('Are you sure you want to Delete this Procedure ?')) {
        //if(jQuery("#form-clinic-procedures").valid() ==true){
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + endurl,
            data: dataValues,

            success: function (data) {
                //console.log(data);
                if (data != 0) {
                    jQuery("#ajax-load-bookingpopup").html(data);
                    jQuery("#new-appointment").attr('doctortype', doctortype);
                    jQuery("#new-appointment").attr('opentype', opentype);
                    if (opentype == 1) {
                        jQuery("#cancel-appointment").attr('doctortype', doctortype);
                        jQuery("#cancel-appointment").attr('opentype', opentype);
                        jQuery("#conclude-appointment").attr('doctortype', doctortype);
                        jQuery("#conclude-appointment").attr('opentype', opentype);
                    } else if (opentype == 2) {
                        jQuery('#doctors-select').attr('disabled', 'true');
                        jQuery('#doctor-procedures').attr('disabled', 'true');
                        jQuery('#remarks').attr('disabled', 'true');
                        jQuery('#booking-date').attr('disabled', 'true');
                        jQuery('#start-time').attr('disabled', 'true');
                        jQuery('#end-time').attr('disabled', 'true');
                        jQuery('#nric').attr('disabled', 'true');
                        jQuery('#name').attr('disabled', 'true');
                        jQuery('#code').attr('disabled', 'true');
                        jQuery('#phone').attr('disabled', 'true');
                        jQuery('#email').attr('disabled', 'true');
                        jQuery("#cancel-appointment").hide();
                        jQuery("#new-appointment").hide();
                        jQuery("#conclude-appointment").hide();
                    }
                    ChangeDoctorProcedure();
                    ChangeDoctorSelect();
                    NRICValidation();
                    NewAppointment();
                    DeleteAppointment();
                    ConcludeAppointment();
                    FormValidation_Booking();
                    ChangeBookingDate();
                    ChangeStartTime();
                } else {
                    //Error here
                }
                jQuery.unblockUI();
            }
        });
        //}     
    });
}

function OpenBookingForm1() {
    jQuery('div[class^="show-open-times"]').on('click', function (e) {
        var starttime = jQuery(this).attr('id');
        var doctorid = jQuery(this).attr('doctorid');
        var clinictimeid = jQuery(this).attr('clinictimeid');

        var bookingdate = jQuery(this).attr('bookingdate');
        var doctortype = jQuery(this).attr('doctortype');

        dataValues = 'clinictimeid=' + clinictimeid + '&doctorid=' + doctorid + '&starttime=' + starttime + '&bookingdate=' + bookingdate;
        //if (confirm('Are you sure you want to Delete this Procedure ?')) {
        //if(jQuery("#form-clinic-procedures").valid() ==true){
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/open-booking-page",
            data: dataValues,

            success: function (data) {
                if (data != 0) {
                    jQuery("#ajax-load-bookingpopup").html(data);
                    jQuery("#new-appointment").attr('doctortype', doctortype);
                    ChangeDoctorProcedure();
                    ChangeDoctorSelect();
                    NRICValidation();
                    NewAppointment();
                    FormValidation_Booking();
                } else {
                    //Error here
                }
                jQuery.unblockUI();
            }
        });
        //}     
    });
}
/* use      :   Used to select doctors procedures
 * 
 */
//function ChangeDoctorProcedure(){
//    jQuery('#doctor-procedures').on('change', function() {     
//        var procedureid = jQuery(this).val();
//        //var duration = jQuery(this).attr('duration');
//        var duration = jQuery('option:selected', this).attr('duration');
//        var durformat = jQuery('option:selected', this).attr('durformat');
//        if(duration){
//            jQuery('#procedure-time').html(duration+' '+durformat);
//        }
//    });
//}
function ChangeDoctorProcedure() {
    jQuery('#doctor-procedures').on('change', function () {
        var procedureid = jQuery(this).val();
        //var duration = jQuery(this).attr('duration');
        var duration = jQuery('option:selected', this).attr('duration');
        var durformat = jQuery('option:selected', this).attr('durformat');
        if (duration) {
            jQuery('#procedure-time').html(duration + ' ' + durformat);
        }
        var starttime = jQuery('option:selected', "#start-time").val();

        //jQuery('#load-startend-time-ajax').html('helllo');

        var doctorid = jQuery('#doctors-select').val();
        var bookingdate = jQuery('#booking-date').val();

        dataValues = 'procedureid=' + procedureid + '&doctorid=' + doctorid + '&bookingdate=' + bookingdate + '&duration=' + duration + '&starttime=' + starttime;
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/change-procedure",
            data: dataValues,

            success: function (data) {
                if (data != 0) {
                    jQuery('#load-startend-time-ajax').html(data);


                    //jQuery("#load-popup-booking-ajax").html(data);
                    //jQuery("#load-doctor-procedures").html(data);
                    //ChangeDoctorSelect();
                    //ChangeDoctorProcedure();
                    ChangeDoctorSelect();
                    ChangeStartTime();
                }
                jQuery.unblockUI();
            }
        });

    });
}

function ChangeBookingDate() {
    jQuery("#booking-date").on('change', function () {
        var bookingdate = jQuery(this).val();
        var procedureid = jQuery('#doctor-procedures').val();
 
        var doctorid = jQuery('#doctors-select').val();
        //var duration = jQuery('#doctor-procedures').attr('duration');
        var duration = jQuery('option:selected', '#doctor-procedures').attr('duration');
        var starttime = jQuery('option:selected', "#start-time").val();
        dataValues = 'procedureid=' + procedureid + '&doctorid=' + doctorid + '&bookingdate=' + bookingdate + '&duration=' + duration + '&starttime=' + starttime;
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            url: base_url + "clinic/change-procedure",
            data: dataValues,

            success: function (data) {
                if (data != 0) {
                    jQuery('#load-startend-time-ajax').html(data);


                    //jQuery("#load-popup-booking-ajax").html(data);
                    //jQuery("#load-doctor-procedures").html(data);
                    //ChangeDoctorSelect();
                    ChangeDoctorProcedure();
                    ChangeDoctorSelect();
                    //ChangeBookingDate();
                    ChangeStartTime();
                }
                jQuery.unblockUI();
            }
        });

    });

}
function ChangeStartTime() {
    jQuery("#start-time").on('change', function () {
        var starttime = jQuery(this).val();
        var duration = jQuery('option:selected', '#doctor-procedures').attr('duration');
        var lastvalue = jQuery('#start-time option:last-child').val();

        if (duration == undefined) {
            alert('Please select a procedure ...');
        } else {
            dataValues = 'starttime=' + starttime + '&duration=' + duration+'&lastvalue='+lastvalue;
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/change-startdate",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        jQuery('#end-time').html(data);


                        //ChangeDoctorProcedure();
                        ///ChangeDoctorSelect();
                        //ChangeBookingDate();
                    } else {
                        alert('There is a mismatch on start and end time ...');
                        jQuery('#end-time').html('');
                    }
                    jQuery.unblockUI();
                }
            });

        }
    });

}
/* Use      :   Used to select doctors procedures list 
 * 
 */
function ChangeDoctorSelect() {
    jQuery('#doctors-select').on('change', function () {
        var doctorid = jQuery(this).val();
        var bookingdate = jQuery('#booking-date').val();
        var clinicid = jQuery('option:selected', this).attr('clinicid');
        //var clinictimeid = jQuery('#new-appointment').attr('clinictimeid');
        var procedureid = jQuery('#doctor-procedures').val();
        var duration = jQuery('option:selected', '#doctor-procedures').attr('duration');
        var starttime = jQuery('option:selected', "#start-time").val();
        //console.log(clinictimeid);
        dataValues = 'clinicid=' + clinicid + '&doctorid=' + doctorid + '&bookingdate=' + bookingdate + '&procedureid=' + procedureid + '&duration=' + duration + '&starttime=' + starttime;
        jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
        jQuery.ajax({
            type: "POST",
            //url : base_url+"clinic/doctor-procedures",
            url: base_url + "clinic/load-booking-popup",
            data: dataValues,

            success: function (data) {
                if (data != 0) {
                    jQuery("#load-popup-booking-ajax").html(data);
                    //jQuery("#load-doctor-procedures").html(data);
                    ChangeDoctorSelect();
                    ChangeDoctorProcedure();
                    ChangeBookingDate();
                    //ChangeDoctorSelect();
                    ChangeStartTime();
                }
                jQuery.unblockUI();
            }
        });
    });
}

function NRICValidation() {
    jQuery('#nric').bind('input propertychange', function () {
        var nric = jQuery(this).val();
        var nricvalid = validateNRIC(nric);
        if (nricvalid == true) {
            jQuery(this).addClass("input-tick");

            dataValues = 'nric=' + nric;
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "auth/find-user",
                data: dataValues,

                success: function (data) {
                    //console.log(data);
                    if (data != 0) {
                        //jQuery("#myModal").popup( "close" );
                        //jQuery("#myModal").dialog('close');
                        //jQuery("#myModal").remove();


                        //jQuery("#myModal").trigger('click');
                        //jQuery('.close-reveal-modal').click();

                        jQuery("#name").val(data['name']);
                        jQuery("#phone").val(data['phone']);
                        jQuery("#email").val(data['email']);
                        jQuery("#code").val(data['code']);
                        //jQuery("#load-doctor-procedures").html(data);
                        //ChangeDoctorProcedure();
                        //ChangeDoctorSelect();
                    }
                    jQuery.unblockUI();
                }
            });
        } else {
            jQuery(this).removeClass("input-tick");
            jQuery("#name").val('');
            jQuery("#phone").val('');
            jQuery("#email").val('');
        }
    });
}


function NewAppointment() {
    jQuery("#new-appointment").click(function () {
        var clinictimeid = jQuery(this).attr('clinictimeid');
        var doctortype = jQuery(this).attr('doctortype');
        var opentype = jQuery(this).attr('opentype');
        var bookingid = jQuery(this).attr('bookingid');
        var doctorid = jQuery('option:selected', "#doctors-select").val();
        var procedureid = jQuery('option:selected', "#doctor-procedures").val();
        var duration = jQuery('option:selected', "#doctor-procedures").attr('duration');
        var remarks = jQuery("#remarks").val();
        var name = jQuery("#name").val();
        var email = jQuery("#email").val();
        var code = jQuery("#code").val();
        var phone = jQuery("#phone").val();
        var nric = jQuery("#nric").val();
        var bookdate = jQuery("#booking-date").val();

        var starttime = jQuery('option:selected', "#start-time").val();
        //var slotplace = jQuery('option:selected', "#start-time").attr('slot_place');
        var endtime = jQuery('option:selected', "#end-time").val();
        var endurl, localvariable;
        if (opentype == 1) {
            endurl = "clinic/update-appointment";

            localvariable = 'clinictimeid='+clinictimeid+'&doctorid='+doctorid+'&procedureid='+procedureid+'&remarks='+remarks+'&name='+name+'&email='+email+'&code='+code+'&phone='+phone+'&nric='+nric+'&bookdate='+bookdate+'&starttime='+starttime+'&endtime='+endtime+'&duration='+duration+'&bookingid='+bookingid;
        }else{
            endurl = "clinic/new-appointment";
            localvariable = 'clinictimeid='+clinictimeid+'&doctorid='+doctorid+'&procedureid='+procedureid+'&remarks='+remarks+'&name='+name+'&email='+email+'&code='+code+'&phone='+phone+'&nric='+nric+'&bookdate='+bookdate+'&starttime='+starttime+'&endtime='+endtime+'&duration='+duration;

        }

        dataValues = localvariable;

        // alerts'');
        if (jQuery("#form-clinic-booking").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            if (opentype == 1) {
                jQuery("#new-appointment").hide();
                jQuery("#cancel-appointment").hide();
                jQuery("#conclude-appointment").hide();
                jQuery("#process-loading").show();
                jQuery("#process-loading").addClass("btn-update font-type-Montserrat");
                jQuery("#process-loading").html("Please wait .....");
            } else {
                jQuery("#new-appointment").hide();
                jQuery("#process-loading").show();
                jQuery("#process-loading").addClass("btn-update font-type-Montserrat");
                jQuery("#process-loading").html("Please wait .....");
            }
            jQuery.ajax({
                type: "POST",
                //url : base_url+"clinic/new-appointment",
                url: base_url + endurl,
                data: dataValues,

                success: function (data) {
                    if (data == 0) {
                        alert('Someting went wrong, Please check ....');
                        jQuery("#new-appointment").show();
                        jQuery("#process-loading").hide();
                    } else if (data == 1) {
                        alert('Sorry! You have an open booking ....');
                        jQuery("#new-appointment").show();
                        jQuery("#process-loading").hide();
                    } else {

                        if (opentype == 1) {
                            alert('Booking Updated....');
                        } else {
                            alert('Booking confirmed....');
                        }
                        //jQuery("#myModal").css({"visibility":"hidden"});
                        if (doctortype == 1) {
                            RefreshSingleDoctorViewPage(doctorid, bookdate);
                        } else if (doctortype == 2) {
                            RefreshDoctorListViewPage(doctorid, bookdate);
                        } else if (doctortype == 3) {
                            RefreshViewAllDoctorsPage(bookdate);
                        }

                        jQuery('#popup').removeAttr("style");

                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}


function NewAppointment1() {
    jQuery("#new-appointment").click(function () {
        var clinictimeid = jQuery(this).attr('clinictimeid');
        var doctortype = jQuery(this).attr('doctortype');
        var doctorid = jQuery('option:selected', "#doctors-select").val();
        var procedureid = jQuery('option:selected', "#doctor-procedures").val();
        var duration = jQuery('option:selected', "#doctor-procedures").attr('duration');
        var remarks = jQuery("#remarks").val();
        var name = jQuery("#name").val();
        var email = jQuery("#email").val();
        var phone = jQuery("#phone").val();
        var nric = jQuery("#nric").val();
        var bookdate = jQuery("#booking-date").val();
        //var starttime = jQuery("#start-time").val();
        //var endtime = jQuery("#end-time").val();

        var starttime = jQuery('option:selected', "#start-time").val();
        //var slotplace = jQuery('option:selected', "#start-time").attr('slot_place');
        var endtime = jQuery('option:selected', "#end-time").val();


        dataValues = 'clinictimeid=' + clinictimeid + '&doctorid=' + doctorid + '&procedureid=' + procedureid + '&remarks=' + remarks + '&name=' + name + '&email=' + email + '&phone=' + phone + '&nric=' + nric + '&bookdate=' + bookdate + '&starttime=' + starttime + '&endtime=' + endtime + '&duration=' + duration;

        if (jQuery("#form-clinic-booking").valid() == true) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/new-appointment",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        alert('Booking confirmed...');
                        jQuery("#myModal").css({"visibility": "hidden"});
                        if (doctortype == 1) {
                            RefreshSingleDoctorViewPage(doctorid, bookdate);
                        } else if (doctortype == 2) {
                            RefreshDoctorListViewPage(doctorid, bookdate);
                        } else if (doctortype == 3) {
                            RefreshViewAllDoctorsPage(bookdate);
                        }

                        /*jQuery("#doctor-procedures").val("");
                         jQuery("#remarks").val("");
                         jQuery("#name").val("");
                         jQuery("#email").val("");
                         jQuery("#phone").val("");
                         //jQuery("#nric").val("");
                         jQuery("#booking-date").val("");
                         jQuery("#start-time").val("");
                         jQuery("#end-time").val("");
                         */

                        jQuery('#popup').removeAttr("style");

                    } else {
                        alert('Someting went wrong, Please check ....')
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}
/* Use      :   Used to Reload bookig slots
 * Acces    :   Public 
 */
function RefreshViewAllDoctorsPage(bookdate) {
    //var name = 0;
    dataValues = 'bookdate=' + bookdate;
    jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
    jQuery.ajax({
        type: "POST",
        url: base_url + "clinic/load-appointment-view",
        data: dataValues,

        success: function (data) {
            if (data != 0) {
                jQuery("#load-slot-section").html(data);

                OpenBookingForm();
                NewAppointment();
                jQuery('.pop1').attr('data-bpopup', '{"follow":["false,false"]}');
            }
            jQuery.unblockUI();
        }
    });
}
/* Use      :   Used to Refresh doctor list view page
 * 
 */
function RefreshDoctorListViewPage(isSelectedBox, currentdate) {
    dataValues = 'doctorid=' + isSelectedBox + '&currentdate=' + currentdate
    jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
    jQuery.ajax({
        type: "POST",
        url: base_url + "clinic/load-doctors-view",
        data: dataValues,

        success: function (data) {
            jQuery("#load-slot-section").html(data);
            jQuery("#main-calander-selection").attr('doctorids', isSelectedBox);
            //jQuery("#main-calander-selection").attr('singledoctor', 2);
            OpenBookingForm();
            NewAppointment();
            jQuery('.pop1').attr('data-bpopup', '{"follow":["false,false"]}');
            jQuery.unblockUI();
        }
    });
}
/* Use      :   Used to Refresh doctor list view page
 * 
 */
function RefreshSingleDoctorViewPage(doctorid, currentdate) {
    dataValues = 'doctorid=' + doctorid + '&currentdate=' + currentdate
    jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
    jQuery.ajax({
        type: "POST",
        url: base_url + "clinic/load-singledoctor-view",
        data: dataValues,

        success: function (data) {
            jQuery("#load-slot-section").html(data);
            //jQuery("#main-calander-selection").attr('doctorids', isSelectedBox);
            OpenBookingForm();
            NewAppointment();
            jQuery('.pop1').attr('data-bpopup', '{"follow":["false,false"]}');
            jQuery.unblockUI();
        }
    });
}


function MainDateSelections() {
    jQuery('#main-calander-selection').datepicker({
        onSelect: function () {
            var dateAsObject = jQuery(this).datepicker('getDate');
            var doctorid = jQuery(this).attr('doctorids');
            var singledoctor = jQuery(this).attr('singledoctor');

            //console.log(doctorid);
            dateAsObject = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsObject))
            if (dateAsObject != '' || dateAsObject != null) {
                jQuery("#main-calander-selection").val(dateAsObject);
                //console.log(dateAsObject);
                var currentdate = dateAsObject;
                if (doctorid) {
                    if (singledoctor == 1) {
                        RefreshSingleDoctorViewPage(doctorid, dateAsObject);
                    } else {
                        RefreshDoctorListViewPage(doctorid, dateAsObject);
                    }
                } else {
                    RefreshViewAllDoctorsPage(currentdate);
                }

            }
        }
    });
}


function CreateDoctorListView() {
    jQuery(".create-doctor-list").click(function () {
        var isSelectedBox = [];
        jQuery(".create-doctor-list").each(function () {
            if (this.checked == true) {
                isSelectedBox.push(jQuery(this).attr('id'));
            }
        });

        if (isSelectedBox.length != 0) {
            RefreshDoctorListViewPage(isSelectedBox, 0);
            /*dataValues = 'doctorid='+isSelectedBox
             jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
             jQuery.ajax({
             type: "POST",
             url : base_url+"clinic/load-doctors-view",
             data : dataValues,

             success : function(data){
             jQuery("#load-slot-section").html(data);
             jQuery("#main-calander-selection").attr('doctorids', isSelectedBox);
             jQuery.unblockUI();
             }
             });*/
        } else {
            alert("Something went wrong, Please try again .......");
        }
    });
}

function DeleteAppointment() {
    jQuery("#cancel-appointment").click(function () {
        var bookingid = jQuery(this).attr('bookingid');
        var doctortype = jQuery(this).attr('doctortype');
        var opentype = jQuery(this).attr('opentype');
        var bookdate = jQuery("#booking-date").val();
        var doctorid = jQuery('option:selected', "#doctors-select").val();

        dataValues = 'bookingid=' + bookingid + '&bookdate=' + bookdate;
        if (confirm('Are you sure you want to Delete this Appointment ?')) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/delete-appointment",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        alert('Booking Deleted...');
                        //jQuery("#myModal").css({"visibility":"hidden"});
                        if (doctortype == 1) {
                            RefreshSingleDoctorViewPage(doctorid, bookdate);
                        } else if (doctortype == 2) {
                            RefreshDoctorListViewPage(doctorid, bookdate);
                        } else if (doctortype == 3) {
                            RefreshViewAllDoctorsPage(bookdate);
                        }

                        jQuery('#popup').removeAttr("style");

                    } else {
                        alert('Someting went wrong, Please check ....')
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}
function ConcludeAppointment() {
    jQuery("#conclude-appointment").click(function () {
        var bookingid = jQuery(this).attr('bookingid');
        var doctortype = jQuery(this).attr('doctortype');
        //var opentype = jQuery(this).attr('opentype');
        var bookdate = jQuery("#booking-date").val();
        var doctorid = jQuery('option:selected', "#doctors-select").val();

        dataValues = 'bookingid=' + bookingid;
        if (confirm('Are you sure you want to Conclude this Appointment ?')) {
            jQuery.blockUI({message: '<h1> ' + base_loading_image + ' <br /> Please wait for a moment</h1>'});
            jQuery.ajax({
                type: "POST",
                url: base_url + "clinic/conclude-appointment",
                data: dataValues,

                success: function (data) {
                    if (data != 0) {
                        alert('Booking Concluded...');
                        //jQuery("#myModal").css({"visibility":"hidden"});
                        if (doctortype == 1) {
                            RefreshSingleDoctorViewPage(doctorid, bookdate);
                        } else if (doctortype == 2) {
                            RefreshDoctorListViewPage(doctorid, bookdate);
                        } else if (doctortype == 3) {
                            RefreshViewAllDoctorsPage(bookdate);
                        }

                        jQuery('#popup').removeAttr("style");

                    } else {
                        alert('Someting went wrong, Please check ....')
                    }
                    jQuery.unblockUI();
                }
            });
        }
    });
}


// nhrgoogle calender sync 2016-1-28

function syncCredentials() {

    jQuery("#btn-sync-credentials").click(function () {

        var doctorid = jQuery('option:selected', "#load-doctors-credentials").val();
        var gmail = jQuery('#doctor-gmail').val();
        var re = (/^([A-Za-z0-9_\-\.])+\@([gmail|GMAIL])+\.(com)$/);
        stra = gmail.match(re);

        if (doctorid == '') {
            jQuery('#sync_msg').html('Please select a Doctor');
            jQuery('#sync_msg').css('color', 'red');
            return false;
        }

        if
        (gmail == '') {
            //alert("Gmail must be filled out");
            jQuery('#alert').html('Gmail must be filled out');
            jQuery('#alert').css('color', 'red');
            return false;
        }

        else if (stra == null) {
            // alert("Invalid gmail address");
            jQuery('#alert').html('Please enter a valid gmail address');
            jQuery('#alert').css('color', 'red');
            return false;
        }
        else {
            jQuery('#alert').html('');
        }

        dataValues1 = 'gmail=' + gmail;
        jQuery.ajax({
                type: "POST",
                url: base_url + "gcal/checkUniqueGmail",
                data: dataValues1,

                success: function (data) {
                    // alert(data);
                    if (data==0) {
                        jQuery('#alert').html('Gmail already exist');
                        jQuery('#alert').css('color', 'red');
                        return false;
                    } else{
                        jQuery('#alert').html('');
                        var conf = confirm('Do you want to send a request?');
                        if (conf) {

                            dataValues = 'doctorid=' + doctorid + '&gmail=' + gmail;
                            jQuery("#btn-sync-credentials").text('Please wait ...');

                            jQuery.ajax({
                                type: "POST",
                                url: base_url + "gcal/sendOAuthRequest",
                                data: dataValues,

                                success: function (data) {
                                    // alert('Request Sent');
                                    jQuery("#btn-sync-credentials").text('Request sent');
                                    jQuery('#load-doctors-credentials').val('');
                                    jQuery('#doctor-gmail').val('');
                                }
                            });
                        }
                    };
                }

            });

   


    });


    jQuery("#load-doctors-credentials").change(function () {

        doctorid = $(this).val();
        dataValues = 'doctorid=' + doctorid;
        jQuery('#alert').html('');
        if (doctorid == '') {
            jQuery('#sync_msg').html('Please select a Doctor');
            jQuery('#sync_msg').css('color', 'red');
            return false;
        }
        ;

        jQuery.ajax({
            type: "POST",
            url: base_url + "gcal/loadTokendGmail",
            dataType: "json",
            data: dataValues,

            success: function (data) {
                gmail = data.gmail;
                token = data.token;
                jQuery("#btn-sync-credentials").attr('disabled', false);
                if (gmail != null && token != null) {
                    jQuery('#sync_msg').html('Sync Ok');
                    jQuery('#sync_msg').css('color', 'green');
                    jQuery("#btn-sync-credentials").attr('disabled', true);
                } else if (gmail != null && token == null) {
                    jQuery('#sync_msg').html('Sync Pending');
                    jQuery('#sync_msg').css('color', 'green');

                } else {
                    jQuery('#sync_msg').html('');

                }
                jQuery('#doctor-gmail').val(gmail);


            }

        });

    });

}
// 2016-2-8
function chekGmail(){
    $('#doctor-gmail').blur(function(event) {
        /* Act on the event */
        var gmail = jQuery('#doctor-gmail').val();

            dataValues = 'gmail=' + gmail;
        jQuery.ajax({
                type: "POST",
                url: base_url + "gcal/checkUniqueGmail",
                data: dataValues,

                success: function (data) {
                    // alert(data);
                    if (data==0) {
                        jQuery('#alert').html('Gmail already exist');
                        jQuery('#alert').css('color', 'red');
                        return false;
                    } else{
                        jQuery('#alert').html('');
                    };
                }

            });

    });
}
// nhr 2016-2-1

function revokeToken(argument) {
    jQuery("#btn-remove-credentials").click(function () {

        var conf = confirm('Do you want to revoke access?');
        if (conf) {

            var doctorid = jQuery('option:selected', "#load-doctors-credentials").val();
            dataValues = 'doctorid=' + doctorid;
            jQuery("#btn-remove-credentials").text('Please wait ...');

            jQuery.ajax({
                type: "POST",
                url: base_url + "gcal/revokeToken",
                data: dataValues,

                success: function (data) {
                    // alert('Request Sent');
                    jQuery("#btn-remove-credentials").text('Access Revoked');
                    jQuery('#load-doctors-credentials').val('');
                    jQuery('#doctor-gmail').val('');
                    jQuery('#sync_msg').html('');
                }
            });
        }

    });
}

// nhr  2016-2-29

function getWidgetUrl(base_url,img_url) {
    var clinicID = $('#h-clinicID').val();
    var url = base_url+'widget/'+clinicID;
    var btn = '<a href="'+url+'" onclick="window.open(this.href, \'newwindow\', \'menubar=1,resizable=1,width=900,height=700, left=300, right=100\'); return false;" title="Medicloud Clinic Widget"><img src="'+img_url+'medicloudbutton.png"></a>';
    return btn;
}


function UpdateChannelBooking(defaultTime,doctorid) {
    dataValues = 'defaulttime=' + defaultTime+'&doctorid='+doctorid;
    jQuery.ajax({
        type: "POST",
        url: base_url + "clinic/channel_update",
        data: dataValues,

        success: function (data) {
            if (data != 0) {
                if(doctorid){
                    jQuery('#channel-notification-doctor').html("<a href='"+base_url + "clinic/appointment-doctor-view/"+doctorid+"'><div class='notify6'>You have " +data+ " New Appointments Click here to update</div></a>");
                }else{
                    jQuery('#channel-notification').html("<a href='"+base_url + "clinic/appointment-home-view'><div class='notify6'>You have " +data+ " New Appointments Click here to update</div></a>");
                }    
            }
        }
    });
}
