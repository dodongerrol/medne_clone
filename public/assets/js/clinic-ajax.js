/*xxxxxxxxxxx This file is used to clinic related work xxxxxxxxxx
 * Author           :   Rizvi
 * Description      :   Clinic and doctor related
 * Modified by      : 
 * Modified date    : 
 * Created on       :   01-11-2014 
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxx End xxxxxxxxxxxxxxxxxxxxxxxxxxx*/
//var client = new Faye.Client('http://localhost:8000/faye');

//To test Locally
//window.base_url = 'http://localhost/medicloud_web/public/app/'; 

//window.base_url = 'http://medicloud.sg/medicloud_web/public/app/'; 

window.base_image_url = "https://medicloud.sg/medicloud_web/public/assets/"; 
window.base_loading_image = '<img src="https://medicloud.sg/medicloud_web/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
   
   /*
    function AutoNodeLoad(){
        jQuery("#clicknode").html('<div id="ActiveNode">Click Me Nodejs</div>');
    }
   */
/*
    client.publish('/12', {
        text: 'Hello world is fine'
    });
    client.subscribe('/12', function(message) {
        //jQuery("#clicknode").append("<div id='ActiveNode'>Click Me Nodejs</div>");
        //jQuery("#clicknode").addClass( "ActiveNode" );
        console.log(message.text);
    });
*/       
    //function CalleNodePublish(){
//        client.publish('/'+11, {
//            text: 'Second click'
//        });    
    //}
    
    function GallClickBooking(){
        jQuery('div[class^="getmyslot"]').on('click', function(e) { 
        //var mainload = jQuery(this).attr('loadmain');
        
        var slotid = jQuery(this).attr('id');
        var cdate = jQuery("#"+slotid).parent().attr('cdate');
        var insertedid = jQuery(this).attr('insertedid');
        //var doctorslot = jQuery("#"+slotid).parent().attr('docslot');
        var mainload = jQuery("#"+slotid).parent().parent().attr('loadmain');
        var doctorslot = jQuery("#"+slotid).parent().parent().attr('docslot');
        //jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        //jQuery("#"+slotid).off('click');
        //jQuery("#"+slotid).attr('disabled','disabled');
        //var doubleval = 0;
        
        //jQuery( "body" ).off( "click", "#"+slotid, flash ).find( "#"+slotid );
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        //jQuery("#"+slotid).hide();
        //if(doubleval !=1){
            updateDoctorSlotDetails(doctorslot,cdate,slotid,insertedid,mainload);
        //}
        //jQuery( "body" ).on( "click", "#"+slotid, flash ).find( "#"+slotid );
        jQuery.unblockUI();
     });
    }
    
    
  /*
    function NodeJSTest(doctorid,currentdate){
        console.log(doctorid +'-' +currentdate);
        var channel = 1;
        client.subscribe('/'+channel, function(message) {
            //jQuery("#clicknode").append("<div id='ActiveNode'>Click Me Nodejs</div>");
            //jQuery("#clicknode").addClass( "ActiveNode" );
            //console.log(message.text);
            //alert(channel);
            //if(doctorid!=null && currentdate !=null){
                refreshDoctorBooking(doctorid,currentdate);
            //}
        });
        client.publish('/'+channel, {
            text: 'Booking Confirmation'
        });   
    } */
    
    
jQuery("document").ready(function(){
	// var protocol = jQuery(location).attr('protocol');
	// var hostname = jQuery(location).attr('hostname');
	// var folderlocation = $(location).attr('pathname').split('/')[1];
	// window.base_url = protocol+'//'+hostname+'/'+folderlocation+'/public/app/';
    window.base_url = window.location.origin + '/app/';
    //window.base_url = 'http://'+jQuery(location).attr('hostname')+'/medicloud_web/public/app/';
	
	//console.log(jQuery(location).attr('hostname');
	
    //NodeJSTest(0,0);
    
    /*
    jQuery("#node-test").click(function(){
        var channel = 1 + Math.floor(Math.random() * 6);
    //$('#node-test').one('click',function(){    
        //ActiveNode();
        //$(this).remove();
    });
    */
    
    GallClickBooking();
  

    jQuery("#update-doctorcharge").click(function(){
        //var doctorslotid = jQuery(this).attr('insertid');
        var mainload = jQuery(this).attr('loadmain');
        var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        saveConsultationCharge(doctorid,clinicid,mainload);
    });
    jQuery("#update-default-doctordetails").click(function(){
        if(jQuery("#form-doctor").valid() ==true){
            var mainload = jQuery(this).attr('loadmain');
            var doctorid = jQuery(this).attr('doctorid');
            var clinicid = jQuery(this).attr('clinicid');
            updateDoctorDetails(doctorid,clinicid,mainload);
        } 
    });
    //for
    jQuery("#save-doctorcharge").click(function(){
        var mainload = jQuery(this).attr('loadmain');
        var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        saveConsultationCharge(doctorid,clinicid,mainload);
    });
    
    //for date forward
    jQuery("#date-forward").click(function(){
        var mainload = jQuery(this).parent().parent().attr('loadmain');
        var currentday = jQuery(this).parent().parent().attr('currentday');       
        var doctorslotid = jQuery(this).parent().parent().attr('doctor-slotid');
        var moveme = 1;
        changeSlotForDate(mainload,doctorslotid,moveme,currentday);
    });
    jQuery("#date-backward").click(function(){
        var moveme = 0;
        var currentday = jQuery(this).parent().parent().attr('currentday'); 
        var mainload = jQuery(this).parent().parent().attr('loadmain');
        var doctorslotid = jQuery(this).parent().parent().attr('doctor-slotid');
        changeSlotForDate(mainload,doctorslotid,moveme,currentday);
    });
    jQuery("#save-consultation").click(function(){
        var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        var insertid = jQuery(this).attr('insertid');
        manageDoctorConsultation(doctorid,clinicid,insertid);
    });
    jQuery("#update-consultation").click(function(){
        var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        var insertid = jQuery(this).attr('insertid');
        manageDoctorConsultation(doctorid,clinicid,insertid);
    });
    
    
    
    jQuery('#datepick6').datepicker({   
        onSelect: function() { 
        var dateAsObject = jQuery(this).datepicker( 'getDate' ); 
        dateAsObject = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsObject))
        if(dateAsObject !='' || dateAsObject !=null){

            var moveme = 2;
            var currentday = dateAsObject;
            var mainload = jQuery('#datepick6').parent().parent().attr('loadmain');
            var doctorslotid = jQuery('#datepick6').parent().parent().attr('doctor-slotid');
            changeSlotForDate(mainload,doctorslotid,moveme,currentday);
        }
       }
    });
    
    /*
    //Manage Slot details
    //jQuery('div[id^="mona"]').on('click', function() {
    jQuery('div[class^="getmyslot"]').on('click', function() { 
        //var mainload = jQuery(this).attr('loadmain');
        
        var slotid = jQuery(this).attr('id');
        var cdate = jQuery("#"+slotid).parent().attr('cdate');
        var insertedid = jQuery(this).attr('insertedid');
        //var doctorslot = jQuery("#"+slotid).parent().attr('docslot');
        var mainload = jQuery("#"+slotid).parent().parent().attr('loadmain');
        var doctorslot = jQuery("#"+slotid).parent().parent().attr('docslot');
        //jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery("#"+slotid).off('click');
        //jQuery("#"+slotid).attr('disabled','disabled');
        updateDoctorSlotDetails(doctorslot,cdate,slotid,insertedid,mainload);
     });
    */
    
    
    
    jQuery("#select-doctor").change(function(e){
        e.preventDefault();
        //jQuery("#doctor-load-data").empty();
        //var base_url = 'http://localhost:81/medicloud_web/public/app/';
        var doctorid = jQuery( "select option:selected" ).val();
        var clid = jQuery(this).attr('clid');
        var dataString = 'doctorid='+doctorid+'&clinicid='+clid;
        //$('#doctor-load-data').html('loading...');
        //jQuery.blockUI({ message: '<h1><img src="http://preloaders.net/preloaders/287/Filling%20broken%20ring.gif" /> Just a moment...</h1>' });
         jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });

        jQuery.ajax({   
            type: "POST",
            url : base_url+"clinic/clinicdata",
            data : dataString,
            success : function(data){
                //console.log(data);
                jQuery("#doctor-load-data").html(data);
                //To save consultation and time slot
                saveConsultationCharge(doctorid,clid);
                
                //To update doctor details
                updateDoctorDetails(doctorid,clid);
                /*jQuery("#tget1").on( "click", function() {
                   alert('hi');
                });*/
                //jQuery("#dmk").html(data);
                updateDoctorSlotDetails();
                
                changeSlotForDate();
                doctorFormValidation();
                validator.resetForm();
                
                SlotQueueSelection();
                //jQuery("#form-doctor").valid()==false;
                jQuery.unblockUI();
            }
        }); 
        //},"json"); 
    });
    
    
    
    jQuery("#save-doctordetails").click(function(e){
        e.preventDefault();
         if(jQuery("#form-doctor").valid() ==true){
             
            var name = jQuery("#doc-name").val();
            var qualification = jQuery("#doc-qualification").val();
            var speciality = jQuery("#doc-speciality").val();
            var mobile = jQuery("#doc-mobile").val();
            var phone= jQuery("#doc-phone").val();
            var email = jQuery("#doc-email").val();
            var nric = null;
            var clinicid = jQuery(this).attr('clinicid');

            var dataValues = 'clinicid='+clinicid+'&name='+name+'&qualification='+qualification+'&speciality='+speciality+'&mobile='+mobile+'&phone='+phone+'&email='+email+'&nric='+nric;
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/newdoctor",
                data : dataValues,
                
                
                success : function(data){
                    if(data!=0){
                        jQuery("#save-doctorcharge").attr('doctorid', data);
                        jQuery("#ajax-error-site").html('<div class="mc-fl mc-errors error-notification-green">New doctor is added</div>');
                    }else{
                        jQuery("#ajax-error-site").html('<div class="mc-fl mc-errors error-notification-b">This Doctor is already exist</div>');
                    }
                    jQuery.unblockUI();
                
                }
               
            }); 
            
        }
    });
    
    
    

    
    
    
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
    
    //Clinic
    BookingFormValidation();
    loadNowBooking();
    deleteBooking();
    openBookingForDelete();
    QueueStopped();
    QueueStarted();
    
    //Doctor
    LoadDoctorBooking();
    DeleteDoctorBooking();
    DoctorQueueStopped();
    DoctorQueueStarted();
    DoctorDoneDiagnosis();
    DatePicker10();
    DoctorCheckout();
    
    jQuery("#change-doctor-booking").change(function(e){
        e.preventDefault();
        //var my_date_string = jQuery.datepicker.formatDate( "dd-mm-yy",  new Date() );
        //jQuery("#datepick6").val(my_date_string);
        
        var doctorid = jQuery( "select option:selected" ).val();
        var clinicid = jQuery(this).attr('clinicid');
        var currentdate = jQuery("#datepick7").val();
        
        var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"clinic/load-booking",
            data : dataValues,

            success : function(data){
                if(data !=null){
                    jQuery('#datepick7').attr('doctorid',doctorid);
                    //console.log(data);
                    jQuery("#booking-ajax").html(data);
                    loadButtonBooking();
                    BookingFormValidation();
                    loadNowBooking();
                    deleteBooking();
                    QueueStopped();
                    QueueStarted();
                    openBookingForDelete();
                }
                
                jQuery.unblockUI();       
            } 
        });       
    });
    
    
   jQuery('#datepick7').datepicker({   
        onSelect: function() { 
        var dateAsObject = jQuery(this).datepicker( 'getDate' ); 
        dateAsObject = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsObject))
        if(dateAsObject !='' || dateAsObject !=null){
            jQuery("#datepick7").val(dateAsObject);
            
            var doctorid = jQuery(this).attr('doctorid');
            var clinicid = jQuery(this).attr('clinicid');
            var currentdate = dateAsObject;

            var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid;
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"clinic/load-booking",
                data : dataValues,

                success : function(data){
                    if(data !=null){
                        jQuery('#datepick7').attr('doctorid',doctorid);
                        //console.log(data);
                        jQuery("#booking-ajax").html(data);
                        loadButtonBooking();
                        BookingFormValidation();
                        loadNowBooking();
                        deleteBooking();
                        openBookingForDelete();
                        QueueStopped();
                        QueueStarted();
                    }
                    jQuery.unblockUI();       
                } 
            });
        }
       }
    });
    
    //jQuery('#queuetag').click(function(){
    //$("input.group1").attr('disabled','disabled');
    //$("input.group1").removeAttr('disabled');
    //});
    //$( "#x" ).prop( "checked", true );
    
    
    SlotQueueSelection();
    
    
    
});//end of document ready function
   
    function SlotQueueSelection(){
        jQuery('#queuetag').click(function(){
            if (jQuery(this).is(':checked')) { 
                $("#slottag").prop('checked', false);
                $("#slot-duration").attr('disabled','disabled');
                $("#queue-no").removeAttr('disabled');
                $("#queue-time").removeAttr('disabled');
            }else{
                $("#slottag").prop('checked', true);
                $("#queue-no").attr('disabled','disabled');
                $("#queue-time").attr('disabled','disabled');
                $("#slot-duration").removeAttr('disabled');
            }
        });
        jQuery('#slottag').click(function(){
            if (jQuery(this).is(':checked')) {
                $("#queuetag").prop('checked', false);
                $("#slot-duration").removeAttr('disabled');
                $("#queue-no").attr('disabled','disabled');
                $("#queue-time").attr('disabled','disabled');
            }else{
                $("#queuetag").prop('checked', true);
                $("#queue-no").removeAttr('disabled');
                $("#queue-time").removeAttr('disabled');
                $("#slot-duration").attr('disabled','disabled');
            }
        });
    }
    
 function saveConsultationCharge(doctorid,clinicid,mainload){
    if(mainload != 1){
        jQuery('#save-doctorcharge').click(function(){
            //console.log(clid);
            var doctorslotid = jQuery(this).attr('insertid');
            var consultationcharge = jQuery('#consult-charge').val();    
            var slotduration = jQuery( "#slot-duration" ).val();
            
            var queuetag = jQuery('#queuetag').is(':checked');
            var slottag = jQuery('#slottag').is(':checked');
            var queueno = jQuery( "#queue-no" ).val();
            var queuetime = jQuery( "#queue-time" ).val();    
            var clinicsession = findClinicSession(queuetag,slottag);
            
            
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            var dataString = 'doctorslotid='+doctorslotid+'&doctorid='+doctorid+'&clinicid='+clinicid+'&consultcharge='+consultationcharge+'&slot='+slotduration+'&queueno='+queueno+'&queuetime='+queuetime+'&clinicsession='+clinicsession;
            //if(doctorslotid=="" || doctorslotid ==0){
                jQuery.ajax({
                    type: "POST",
                    url : base_url+"doctor/manageslots",
                    data : dataString,
                    success : function(data){
                        if(data !=0){
                        //console.log(data);
                        jQuery("#second-ajax-call").html(data);
                        }
                        changeSlotForDate();
                        updateDoctorSlotDetails();
                        jQuery.unblockUI();
                    }
                });  
        });
    }
    else{
        //Call ajax when default load
            var doctorslotid = jQuery("#update-doctorcharge").attr('insertid');
            var consultationcharge = jQuery('#consult-charge').val();    
            var slotduration = jQuery( "#slot-duration" ).val();
            
            var queuetag = jQuery('#queuetag').is(':checked');
            var slottag = jQuery('#slottag').is(':checked');
            var queueno = jQuery( "#queue-no" ).val();
            var queuetime = jQuery( "#queue-time" ).val();    
            var clinicsession = findClinicSession(queuetag,slottag);
            
//console.log(doctorslotid);
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            var dataString = 'doctorslotid='+doctorslotid+'&doctorid='+doctorid+'&clinicid='+clinicid+'&consultcharge='+consultationcharge+'&slot='+slotduration+'&queueno='+queueno+'&queuetime='+queuetime+'&clinicsession='+clinicsession;
            //if(doctorslotid=="" || doctorslotid ==0){
                jQuery.ajax({
                    type: "POST",
                    url : base_url+"doctor/manageslots",
                    data : dataString,
                    success : function(data){
                        //console.log(data);
                        if(data !=0){
                        jQuery("#second-ajax-call").html(data);
                        }
                        changeSlotForDate();
                        updateDoctorSlotDetails();
                        jQuery.unblockUI();
                    }
                });  
        } 
}



function updateDoctorDetails(doctorid,clinicid,mainload){
    if(mainload != 1){
        jQuery("#update-doctordetails").click(function(e){
            e.preventDefault();
        if(jQuery("#form-doctor").valid() ==true){
            var name = jQuery("#doc-name").val();
            var qualification = jQuery("#doc-qualification").val();
            var speciality = jQuery("#doc-speciality").val();
            var mobile = jQuery("#doc-mobile").val();
            var phone= jQuery("#doc-phone").val();
            var email = jQuery("#doc-email").val();
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            var dataValues = 'doctorid='+doctorid+'&clinicid='+clinicid+'&name='+name+'&qualification='+qualification+'&speciality='+speciality+'&mobile='+mobile+'&phone='+phone+'&email='+email;
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/updatedoctor",
                data : dataValues,
                success : function(data){
                    //console.log(data);
                    jQuery.unblockUI();
                }
            }); 
        }
        });
    }else{
        //jQuery("#update-doctordetails").click(function(e){
            //e.preventDefault();

            var name = jQuery("#doc-name").val();
            var qualification = jQuery("#doc-qualification").val();
            var speciality = jQuery("#doc-speciality").val();
            var mobile = jQuery("#doc-mobile").val();
            var phone= jQuery("#doc-phone").val();
            var email = jQuery("#doc-email").val();
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            var dataValues = 'doctorid='+doctorid+'&clinicid='+clinicid+'&name='+name+'&qualification='+qualification+'&speciality='+speciality+'&mobile='+mobile+'&phone='+phone+'&email='+email;
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/updatedoctor",
                data : dataValues,
                success : function(data){
                    //console.log(data);
                    jQuery.unblockUI();
                }
            }); 
        //});
    }
    
}


function updateDoctorSlotDetails(doctorslot,cdate,slotid,insertedid,mainload){
    if(mainload != 1){
        jQuery('div[class^="getmyslot"]').on('click', function() { 
            //var mainload = jQuery(this).attr('loadmain');
            //jQuery('selector').prop('ondblclick', "");
            
            var slotid = jQuery(this).attr('id');
            var cdate = jQuery("#"+slotid).parent().attr('cdate');
            var insertedid = jQuery(this).attr('insertedid');
            //var doctorslot = jQuery("#"+slotid).parent().attr('docslot');
            var doctorslot = jQuery("#"+slotid).parent().parent().attr('docslot');
            var slottime = findSlotTime(slotid);
            jQuery("#"+slotid).attr("disabled", true);
            var dataValues = 'insertedid='+insertedid+'&doctorslot='+doctorslot+'&slotid='+slotid+'&date='+cdate+'&slottime='+slottime;
            //if(doctorslot!="" && doctorslot !=null && doctorslot !=0){
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/manage-slotdetail",
                data : dataValues,
                success : function(data){ 
                        //jQuery("#"+slotid).append('<div id="'+slotid+'" class="getmyslot mc-slot1 mc-slot-color"></div>');
                        //jQuery("#"+slotid).addClass("mc-slot-color");
                    if(data['result']=="Insert"){
                        jQuery("#"+slotid).attr('insertedid', data['id']);
                        jQuery("#"+slotid).removeClass("mc-slot-color");
                    }else if(data['result']=="Update"){
                        if(data['active']==1){
                            jQuery("#"+slotid).removeClass("mc-slot-color");
                        }else{
                            jQuery("#"+slotid).addClass("mc-slot-color");
                        }
                    }else if(data['result']=="Booked"){
                        alert("This slot is allocated by someone!")
                    }  
                    jQuery.unblockUI();
                }
            }); 
            //updateDoctorSlotDetails(doctorslot,cdate,slotid,insertedid,mainload);
            
         });
    }else{ 
        var slottime = findSlotTime(slotid);
        var dataValues = 'insertedid='+insertedid+'&doctorslot='+doctorslot+'&slotid='+slotid+'&date='+cdate+'&slottime='+slottime;
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/manage-slotdetail",
            data : dataValues,
            success : function(data){
                //console.log(data);
                    //jQuery("#"+slotid).append('<div id="'+slotid+'" class="getmyslot mc-slot1 mc-slot-color"></div>');
                    //jQuery("#"+slotid).addClass("mc-slot-color");
                if(data['result']=="Insert"){
                    jQuery("#"+slotid).attr('insertedid', data['id']);
                    jQuery("#"+slotid).removeClass("mc-slot-color");
                }else if(data['result']=="Update"){
                    if(data['active']==1){
                        jQuery("#"+slotid).removeClass("mc-slot-color");
                    }else{
                        jQuery("#"+slotid).addClass("mc-slot-color");
                    }
                }else if(data['result']=="Booked"){
                    alert("This slot is allocated by someone!")
                }      
            }
        });
    }
     
}

function changeSlotForDate(mainload,doctorslotid,moveme,currentday){
    if(mainload !=1){
        jQuery("#date-forward").click(function(){
            var moveme =1;
            var currentday = jQuery(this).parent().parent().attr('currentday'); 
            var doctorslotid = jQuery(this).parent().parent().attr('doctor-slotid');
            var dataValues = 'doctorslotid='+doctorslotid+'&moveme='+moveme+'&currentday='+currentday;
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/manage-slotdate",
                data : dataValues,
                success : function(data){
                    jQuery("#second-ajax-call").html(data);
                    changeSlotForDate();
                    updateDoctorSlotDetails();
                    jQuery.unblockUI();
                }
            });    
            
        });
        jQuery("#date-backward").click(function(){
            var moveme =0;
            var currentday = jQuery(this).parent().parent().attr('currentday'); 
            var doctorslotid = jQuery(this).parent().parent().attr('doctor-slotid');
            var dataValues = 'doctorslotid='+doctorslotid+'&moveme='+moveme+'&currentday='+currentday;
            jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/manage-slotdate",
                data : dataValues,
                success : function(data){
                    jQuery("#second-ajax-call").html(data);
                    changeSlotForDate();
                    updateDoctorSlotDetails();
                    jQuery.unblockUI();
                }
            });
        });
        jQuery('#datepick6').datepicker({   
            onSelect: function() { 
            var dateAsObject = jQuery(this).datepicker( 'getDate' ); 
            dateAsObject = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsObject))
            if(dateAsObject !='' || dateAsObject !=null){
                var moveme = 2;
                var currentday = dateAsObject;             
                var doctorslotid = jQuery('#datepick6').parent().parent().attr('doctor-slotid');
                var dataValues = 'doctorslotid='+doctorslotid+'&moveme='+moveme+'&currentday='+currentday;
                jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
                jQuery.ajax({
                    type: "POST",
                    url : base_url+"doctor/manage-slotdate",
                    data : dataValues,
                    success : function(data){
                        jQuery("#second-ajax-call").html(data);
                        changeSlotForDate();
                        updateDoctorSlotDetails();
                        jQuery.unblockUI();
                    }
                });   
            }
           }
        });
    }else{
        //console.log(moveme);
        var dataValues = 'doctorslotid='+doctorslotid+'&moveme='+moveme+'&currentday='+currentday;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/manage-slotdate",
            data : dataValues,
            success : function(data){
                jQuery("#second-ajax-call").html(data);
                changeSlotForDate();
                updateDoctorSlotDetails();
                jQuery.unblockUI();
            }
        });   
    }  
}

/* Use          :   it is used to save or update consultation by Doctor
 * Parameter    :   Doctorid, clinicid and slot id if any
 * return       :   Slot details
 */
function manageDoctorConsultation(doctorid,clinicid,insertid){
    var consultationcharge = jQuery('#consult-charge').val();    
    
    var queuetag = jQuery('#queuetag').is(':checked');
    var slottag = jQuery('#slottag').is(':checked');
    var slotduration = jQuery( "#slot-duration" ).val();
    var queueno = jQuery( "#queue-no" ).val();
    var queuetime = jQuery( "#queue-time" ).val();    
    var clinicsession = findClinicSession(queuetag,slottag);
    
    var dataString = 'doctorslotid='+insertid+'&doctorid='+doctorid+'&clinicid='+clinicid+'&consultcharge='+consultationcharge+'&slot='+slotduration+'&queueno='+queueno+'&queuetime='+queuetime+'&clinicsession='+clinicsession;
    //if(doctorslotid=="" || doctorslotid ==0){
    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/manageslots",
            data : dataString,
            success : function(data){ 
                jQuery("#second-ajax-call").html(data);
                changeSlotForDate();
                updateDoctorSlotDetails();
                jQuery.unblockUI();
            }
        });
}

/* Use          :   Used to return clinic session (Queue or Slot or Both)
 * Access       :   Public
 * Parameter    :   Queue and slot
 */
function findClinicSession(queuetag,slottag){
    var clinicsession = 0;
    if(queuetag ==true && slottag == true){
        clinicsession = 3; 
    }else if(queuetag ==true && slottag == false){
        clinicsession = 1;
    }else if(queuetag ==false && slottag == true){
        clinicsession = 2;
    }
    return clinicsession;
}





function findSlotTime_OLD(slot){
    //console.log(slot.length);
    if(slot.length == 5){
        var findLetter = slot.substr(slot.length - 2);
    }else if(slot.length == 6){
        var findLetter = slot.substr(slot.length - 3);
    }
    
    var time = 0;
    //console.log(findLetter);
    if(findLetter !=""){
        if(findLetter == 'a0'){ time = '7.00AM'; }
        else if(findLetter == 'b0'){ time = '7.30AM'; }
        else if(findLetter == 'a1'){ time = '8.00AM'; }
        else if(findLetter == 'b1'){ time = '8.30AM'; }
        else if(findLetter == 'a2'){ time = '9.00AM'; }
        else if(findLetter == 'b2'){ time = '9.30AM'; }
        else if(findLetter == 'a3'){ time = '10.00AM'; }
        else if(findLetter == 'b3'){ time = '10.30AM'; }
        else if(findLetter == 'a4'){ time = '11.00AM'; }
        else if(findLetter == 'b4'){ time = '11.30AM'; }
        else if(findLetter == 'a5'){ time = '12.00PM'; }
        else if(findLetter == 'b5'){ time = '12.30PM'; }
        else if(findLetter == 'a6'){ time = '1.00PM'; }
        else if(findLetter == 'b6'){ time = '1.30PM'; }
        else if(findLetter == 'a7'){ time = '2.00PM'; }
        else if(findLetter == 'b7'){ time = '2.30PM'; }
        else if(findLetter == 'a8'){ time = '3.00PM'; }
        else if(findLetter == 'b8'){ time = '3.30PM'; }
        else if(findLetter == 'a9'){ time = '4.00PM'; }
        else if(findLetter == 'b9'){ time = '4.30PM'; }
        else if(findLetter == 'a10'){ time = '5.00PM'; }
        else if(findLetter == 'b10'){ time = '5.30PM'; }
        else if(findLetter == 'a11'){ time = '6.00PM'; }
        else if(findLetter == 'b11'){ time = '6.30PM'; }
        else if(findLetter == 'a12'){ time = '7.00PM'; }
        else if(findLetter == 'b12'){ time = '7.30PM'; }
        else if(findLetter == 'a13'){ time = '8.00PM'; }
        else if(findLetter == 'b13'){ time = '8.30PM'; }
        else if(findLetter == 'a14'){ time = '9.00PM'; }
        else if(findLetter == 'b14'){ time = '9.30PM'; }
    }
    return time;
}


/* Use          :   used to find time based on slot 
 * Access       :   Public
 * Parameter    :   Slotid
 */
function findSlotTime(slot){
    //console.log(slot.length);
    if(slot.length == 5){
        var findLetter = slot.substr(slot.length - 2);
    }else if(slot.length == 6){
        var findLetter = slot.substr(slot.length - 3);
    }
    
    var time = 0;
    //console.log(findLetter);
    if(findLetter !=""){
        if(findLetter == 'a0'){ time = '07.00AM'; }
        else if(findLetter == 'b0'){ time = '07.30AM'; }
        else if(findLetter == 'a1'){ time = '08.00AM'; }
        else if(findLetter == 'b1'){ time = '08.30AM'; }
        else if(findLetter == 'a2'){ time = '09.00AM'; }
        else if(findLetter == 'b2'){ time = '09.30AM'; }
        else if(findLetter == 'a3'){ time = '10.00AM'; }
        else if(findLetter == 'b3'){ time = '10.30AM'; }
        else if(findLetter == 'a4'){ time = '11.00AM'; }
        else if(findLetter == 'b4'){ time = '11.30AM'; }
        else if(findLetter == 'a5'){ time = '12.00PM'; }
        else if(findLetter == 'b5'){ time = '12.30PM'; }
        else if(findLetter == 'a6'){ time = '13.00PM'; }
        else if(findLetter == 'b6'){ time = '13.30PM'; }
        else if(findLetter == 'a7'){ time = '14.00PM'; }
        else if(findLetter == 'b7'){ time = '14.30PM'; }
        else if(findLetter == 'a8'){ time = '15.00PM'; }
        else if(findLetter == 'b8'){ time = '15.30PM'; }
        else if(findLetter == 'a9'){ time = '16.00PM'; }
        else if(findLetter == 'b9'){ time = '16.30PM'; }
        else if(findLetter == 'a10'){ time = '17.00PM'; }
        else if(findLetter == 'b10'){ time = '17.30PM'; }
        else if(findLetter == 'a11'){ time = '18.00PM'; }
        else if(findLetter == 'b11'){ time = '18.30PM'; }
        else if(findLetter == 'a12'){ time = '19.00PM'; }
        else if(findLetter == 'b12'){ time = '19.30PM'; }
        else if(findLetter == 'a13'){ time = '20.00PM'; }
        else if(findLetter == 'b13'){ time = '20.30PM'; }
        else if(findLetter == 'a14'){ time = '21.00PM'; }
        else if(findLetter == 'b14'){ time = '21.30PM'; }
    }
    return time;
}



function DatePicker10(){
    jQuery('#datepick10').datepicker({   
            onSelect: function() {
            var doctorid = jQuery(this).attr('doctorid');
            var clinicid = jQuery(this).attr('clinicid');    
            var dateAsObject = jQuery(this).datepicker( 'getDate' ); 
            dateAsObject = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateAsObject))
            if(dateAsObject !='' || dateAsObject !=null){
                //jQuery(this).attr('nowdate',dateAsObject);
                refreshDoctorBooking(doctorid,dateAsObject);
            }
        }
    });
}
function DoctorCheckout(){
    jQuery("#doctor-checkout").click(function(){
        console.log('hi');
    }); 
}


function QueueStopped(){
    jQuery("#queue-stopped").click(function(){
        var doctorslotid = jQuery(this).attr('doctorslotid');
        var queuetotal = jQuery(this).attr('queuetotal');
        var currenttotal = jQuery(this).attr('currenttotal');
        var currentdate = jQuery("#datepick7").val();
        var doctorid = jQuery("#datepick7").attr('doctorid');
        var clinicid = jQuery("#datepick7").attr('clinicid');
            
        //console.log(currentdate);
        var dataValues = 'doctorslotid='+doctorslotid+'&queuetotal='+queuetotal+'&currenttotal='+currenttotal+'&currentdate='+currentdate;
        if (confirm('Are you sure you want to Stop the Queue ?')) {
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/manage-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    refreshBookingPage(clinicid,doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        }); }
        
    });
}
function QueueStarted(){ 
    jQuery("#queue-started").click(function(){
        var slotmanageid = jQuery(this).attr('slotmanageid');
        //var queuetotal = jQuery(this).attr('queuetotal');
        //var currenttotal = jQuery(this).attr('currenttotal');
        var currentdate = jQuery("#datepick7").val();
        var doctorid = jQuery("#datepick7").attr('doctorid');
        var clinicid = jQuery("#datepick7").attr('clinicid');
            
        var dataValues = 'slotmanageid='+slotmanageid;
        if (confirm('Are you sure you want to Open the Queue ?')) {
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/start-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    refreshBookingPage(clinicid,doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        });   }   
    });
}

/* Use          :   Used to refresh page with existing records
 * Access       :   Public
 */
function refreshBookingPage(clinicid,doctorid,currentdate){
    var doctorid = doctorid;
    var clinicid = clinicid;
    var currentdate = currentdate;

    var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid;
    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
    jQuery.ajax({
        type: "POST",
        url : base_url+"clinic/load-booking",
        data : dataValues,

        success : function(data){
            if(data !=null){
                jQuery('#datepick7').attr('doctorid',doctorid);
                jQuery("#booking-ajax").html(data);
                loadButtonBooking();
                BookingFormValidation();
                loadNowBooking();
                openBookingForDelete();
                deleteBooking();
                QueueStopped();
                QueueStarted();
            }
            jQuery.unblockUI();       
        } 
    });
}


/* Use          :   Used to open popup by clinic and Doctor
 * Access       :   Public 
 * 
 */
    function openBookingForDelete(){
        jQuery('.appoint-delete').click(function(){
        //jQuery('div[class^="appoint-delete"]').on('click', function() {     
            var bookingid = jQuery(this).attr('id');
            var booktype = jQuery(this).attr('bktype');
            var sts = jQuery(this).attr('sts');
            if(bookingid !=''){
                var modalLocation = jQuery(this).attr('data-reveal-id');
                jQuery('#'+modalLocation).reveal(jQuery(this).data());
                if(booktype==0){
                    jQuery('#book-type-text').html("Queue Number");
                    jQuery('.book-type-q').show();
                    jQuery('.book-type-a').hide();
                }else if(booktype==1){
                    jQuery('#book-type-text').html("Appointment Time");
                    jQuery('.book-type-q').hide();
                    jQuery('.book-type-a').show();
                    var slottime = jQuery(this).attr('slottime'); 
                    jQuery('#slot-time').html(slottime.substring(0,slottime.length-2));
                    jQuery('#slot-time-peak').html(slottime.substring(slottime.length-2));
                }
            if(sts==1){ 
                jQuery('#cancel-booking').hide(); 
                jQuery('#cancel-doctor-booking').hide(); 
            }else{ 
                jQuery('#cancel-booking').show();
                jQuery('#cancel-doctor-booking').show();
            }
            jQuery('#now-booking').hide();
            jQuery('#doctor-booking').hide();

                var doctorid = jQuery("#datepick7").attr('doctorid');
                var clinicid = jQuery("#datepick7").attr('clinicid');
                var currentdate = jQuery("#datepick7").val();
                
                var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid+'&bookingid='+bookingid+'&booktype='+booktype;
                jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });    
                jQuery.ajax({
                    type: "POST",
                    url : base_url+"doctor/delete-popup",
                    data : dataValues,

                    success : function(data){
                        //console.log(data);
                        if(data != 0){
                            jQuery('#user-name').val(data['name']);
                            jQuery('#user-nric').val(data['nric']);
                            jQuery('#user-mobile').val(data['phone']);
                            jQuery('#user-email').val(data['email']);
                            jQuery('#user-name').attr('readonly','readonly');
                            jQuery('#user-nric').attr('readonly','readonly');
                            jQuery('#user-mobile').attr('readonly','readonly');
                            jQuery('#user-email').attr('readonly','readonly');
                            jQuery('#doctor-charge').html('$'+data['consultcharge']);
                            jQuery('#queue-no').html(data['bookno']);
                            jQuery('#book-date').html(data['bookdate']);
                            
                            jQuery('#cancel-booking').attr('bktype',booktype);
                            jQuery('#cancel-booking').attr('deleteid',bookingid);
                            
                            jQuery('#cancel-doctor-booking').attr('bktype',booktype);
                            jQuery('#cancel-doctor-booking').attr('deleteid',bookingid);
                            
                        }

                        jQuery.unblockUI();       
                    } 
                });
            
            }    
        });
    }
    /* Use          :   Used to cancel appointment by clinic
     * Access       :   Public
     * 
     */
    function deleteBooking(){
        jQuery('#cancel-booking').click(function(){
            //console.log('hoho');
            var bookingid = jQuery(this).attr('deleteid');
            var booktype = jQuery(this).attr('bktype');
            if(bookingid !="" && booktype !=""){
                var doctorid = jQuery("#datepick7").attr('doctorid');
                var clinicid = jQuery("#datepick7").attr('clinicid');
                var currentdate = jQuery("#datepick7").val();

                //console.log(doctorid);
                var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid+'&bookingid='+bookingid+'&booktype='+booktype;

                if (confirm('Are you sure you want to cancel this appointment ?')) {
                    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });    
                jQuery.ajax({
                    type: "POST",
                    url : base_url+"doctor/delete-appointment",
                    data : dataValues,

                    success : function(data){
                        //console.log(data);
                        if(data ==1){
                            jQuery('#datepick7').attr('doctorid',doctorid);
                            jQuery("#booking-ajax").html(data);
                            loadButtonBooking();
                            BookingFormValidation();
                            loadNowBooking();
                            refreshBookingPage(clinicid,doctorid,currentdate);
                        }
                        jQuery.unblockUI();       
                    } 
                }); }
            }
        });
    }
    

/* Use          :   Used to book Queue and Slot
 * Access       :   Public
 * 
 */
function loadNowBooking(){
    jQuery("#now-booking").click(function(){
        var bookoption = jQuery(this).attr('bookoption');
        if(bookoption == 0 || bookoption == 1){
            if(jQuery("#form-booking").valid() ==true){
                MainBooking(bookoption);
            }
        }
    });
}    
function MainBooking(booktype){
    var doctorslotid = jQuery("#now-booking").attr('doctorslotid'); 
    if(booktype == 0){  
        var queueno = jQuery('#queue-no').html();
        //var bookdate = jQuery("#now-booking").attr('book-date');
    }else if(booktype == 1){
        //var bookdate = jQuery("#now-booking").attr('slotdate');
        var slotdetailid = jQuery("#now-booking").attr('slotdetailid');
    }
    var doctorid = jQuery("#datepick7").attr('doctorid');
    var clinicid = jQuery("#datepick7").attr('clinicid');
    var currentdate = jQuery("#datepick7").val();

    var name = jQuery("#user-name").val();
    var nric = jQuery("#user-nric").val();
    var mobile = jQuery("#user-mobile").val();
    var email = jQuery("#user-email").val();

    if(booktype == 0){
    var dataValues = 'name='+name+'&nric='+nric+'&mobile='+mobile+'&email='+email+'&doctorslotid='+doctorslotid+'&bookdate='+currentdate+'&queueno='+queueno+'&booktype='+booktype;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/booking-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){     
                    alert('Booking confirmed...');
                    refreshBookingPage(clinicid,doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        });
    }else if(booktype == 1){
    var dataValues = 'name='+name+'&nric='+nric+'&mobile='+mobile+'&email='+email+'&bookdate='+currentdate+'&doctorslotid='+doctorslotid+'&slotdetailid='+slotdetailid+'&booktype='+booktype;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/booking-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    alert('Booking confirmed...');
                    refreshBookingPage(clinicid,doctorid,currentdate);
                }               
                jQuery.unblockUI();       
            } 
        });
    }
}

/* Use          :   Used to book by Doctor
 * Access       :   Public
 * 
 */
function LoadDoctorBooking(){
    jQuery("#doctor-booking").click(function(){
        var bookoption = jQuery(this).attr('bookoption');
        if(bookoption == 0 || bookoption == 1){
            if(jQuery("#form-booking").valid() ==true){
                MainDoctorBooking(bookoption);
            }
        }
    });
}

/* Use          :   Used to open booking page by Doctor
 * Access       :   Public
 * 
 */
function MainDoctorBooking(booktype){
    var doctorslotid = jQuery("#doctor-booking").attr('doctorslotid'); 
    if(booktype == 0){  
        var queueno = jQuery('#queue-no').html();
        //var bookdate = jQuery("#now-booking").attr('book-date');
    }else if(booktype == 1){
        //var bookdate = jQuery("#now-booking").attr('slotdate');
        var slotdetailid = jQuery("#doctor-booking").attr('slotdetailid');
    }
    var clinicid = jQuery("#datepick10").attr('clinicid');
    var doctorid = jQuery("#datepick10").attr('doctorid');
    var currentdate = jQuery("#datepick10").attr('nowdate');

    var name = jQuery("#user-name").val();
    var nric = jQuery("#user-nric").val();
    var mobile = jQuery("#user-mobile").val();
    var email = jQuery("#user-email").val();
    
    if(booktype == 0){
    var dataValues = 'name='+name+'&nric='+nric+'&mobile='+mobile+'&email='+email+'&doctorslotid='+doctorslotid+'&bookdate='+currentdate+'&queueno='+queueno+'&booktype='+booktype;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/booking",
            data : dataValues,

            success : function(data){
                if(data !=0){     
                    alert('Booking confirmed...');
                    //refreshBookingPage(clinicid,doctorid,currentdate);                   
                    refreshDoctorBooking(doctorid,currentdate);
                    //NodeJSTest(doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        });
    }else if(booktype == 1){
    var dataValues = 'name='+name+'&nric='+nric+'&mobile='+mobile+'&email='+email+'&bookdate='+currentdate+'&doctorslotid='+doctorslotid+'&slotdetailid='+slotdetailid+'&booktype='+booktype;
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/booking",
            data : dataValues,

            success : function(data){ 
                if(data !=0){
                    alert('Booking confirmed...');
                    refreshDoctorBooking(doctorid,currentdate);
                    
                }               
                jQuery.unblockUI();       
            } 
        });
    }
}



/* Use          :   Used to refresh the page by Doctor
 * 
 * 
 */
function refreshDoctorBooking(doctorid,currentdate){
    var doctorid = doctorid;
    //var clinicid = clinicid;
    var currentdate = currentdate; 
    var dataValues = 'currentdate='+currentdate+'&doctorid='+doctorid; 
    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
    jQuery.ajax({
        type: "POST",
        url : base_url+"doctor/ajax-booking",
        data : dataValues,

        success : function(data){
            if(data !=0){
                //jQuery("#ajax-doctor-slider").html(data);
                jQuery("#doctor-booking-ajax").html(data);
                LoadDoctorBooking();
                loadButtonBooking();
                openBookingForDelete();
                DeleteDoctorBooking();
                DoctorQueueStopped();
                DoctorQueueStarted();
                DoctorDoneDiagnosis();
                DatePicker10();
            }
            jQuery.unblockUI();       
        } 
    });
    
}

/* Use      :   Used to delete by doctor
 * Access   :   Public 
 * 
 */
function DeleteDoctorBooking(){
    jQuery('#cancel-doctor-booking').click(function(){
        //console.log('hoho');
        var bookingid = jQuery(this).attr('deleteid');
        var booktype = jQuery(this).attr('bktype');
        if(bookingid !="" && booktype !=""){
            var doctorid = jQuery("#datepick10").attr('doctorid');
            var clinicid = jQuery("#datepick10").attr('clinicid');
            var currentdate = jQuery("#datepick10").attr('nowdate');

            //console.log(doctorid);
            var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid+'&bookingid='+bookingid+'&booktype='+booktype;

            if (confirm('Are you sure you want to cancel this appointment ?')) {
                jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });    
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/delete-appointment",
                data : dataValues,

                success : function(data){
                    //console.log(data);
                    if(data ==1){
                        refreshDoctorBooking(doctorid,currentdate);
                    }
                    jQuery.unblockUI();       
                } 
            }); }
        }
    });
}

/* Use          :   Used to stop queue by Doctor
 * Access       :   Public 
 * 
 */
function DoctorQueueStopped(){
    jQuery("#doctor-queue-stopped").click(function(){
        var doctorslotid = jQuery(this).attr('doctorslotid');
        var queuetotal = jQuery(this).attr('queuetotal');
        var currenttotal = jQuery(this).attr('currenttotal');
        var currentdate = jQuery("#datepick10").attr('nowdate');
        var doctorid = jQuery("#datepick10").attr('doctorid');
        var clinicid = jQuery("#datepick10").attr('clinicid');
            
        //console.log(currentdate);
        var dataValues = 'doctorslotid='+doctorslotid+'&queuetotal='+queuetotal+'&currenttotal='+currenttotal+'&currentdate='+currentdate;
        if (confirm('Are you sure you want to Stop the Queue ?')) {
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/manage-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    refreshDoctorBooking(doctorid,currentdate);    
                }
                jQuery.unblockUI();       
            } 
        }); }
        
    });
}
function DoctorQueueStarted(){ 
    jQuery("#doctor-queue-started").click(function(){
        var slotmanageid = jQuery(this).attr('slotmanageid');
        //var queuetotal = jQuery(this).attr('queuetotal');
        //var currenttotal = jQuery(this).attr('currenttotal');
        var currentdate = jQuery("#datepick10").attr('nowdate');
        var doctorid = jQuery("#datepick10").attr('doctorid');
        var clinicid = jQuery("#datepick10").attr('clinicid');
            
        var dataValues = 'slotmanageid='+slotmanageid;
        if (confirm('Are you sure you want to Open the Queue ?')) {
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/start-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    refreshDoctorBooking(doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        });   }   
    });
}

function DoctorDoneDiagnosis(){
    jQuery('#doctor-done').click(function(){ 
        var diagnosis = jQuery("#diagnosis").val();
        var appointment = jQuery(this).attr('appointment');
        
        var currentdate = jQuery("#datepick10").attr('nowdate');
        var doctorid = jQuery("#datepick10").attr('doctorid');
        //var clinicid = jQuery("#datepick10").attr('clinicid');
        
        var dataValues = 'diagnosis='+diagnosis+'&appointment='+appointment;

        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/diagnosis",
            data : dataValues,

            success : function(data){ 
                //console.log(data);
                if(data !=0){
                    alert('Appointment Concluded...');
                    refreshDoctorBooking(doctorid,currentdate);
                }
                jQuery.unblockUI();       
            } 
        });   
        
    });
}



