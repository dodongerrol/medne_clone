//window.base_url = 'http://localhost/medicloud_web/public/app/'; 
 

//window.base_image_url = "http://medicloud.sg/medicloud_web/public/assets/"; 
//window.base_loading_image = '<img src="http://medicloud.sg/medicloud_web/public/assets/images/ajax-loader.gif" width="32" height="32"  alt=""/>';
   
   
jQuery("document").ready(function(){
    //window.base_url1 = 'http://'+jQuery(location).attr('hostname')+'/medicloud_web/public/app/';
    //console.log(base_url);
    //Load booking form
    ClinicDoctorBookingLoad();
    
    //Open clinic booking
    DeleteClinicBookingPopup();
    
    //Delete clinic booking
    DeleteClinicBooking();
    
    //Clinic dashboard pagination
    ClinicDashboardPagination();
    
    //Clinic Date Picker
    ClinicDashboardDatePicker();
    
    //Clinic Stop Queue
    ClinicQueueStopped();
    
    //Clinic Start Queue
    ClinicQueueStarted();
    
    //Dashboard Booking Datepicker
    ClinicDashboardBookingDatePicker();
    
});



/* Used in Clinic Booking
 * 
 */
function ClinicDoctorBookingLoad(){
    jQuery("#clinic-doctor-booking").click(function(){
        var bookoption = jQuery(this).attr('bookoption');
        var currentpage = jQuery(this).attr('currentpage');
        if(bookoption == 0 || bookoption == 1){
            if(jQuery("#form-booking").valid() ==true){
                MainClinicDoctorBooking(bookoption,currentpage);
                //jQuery("#ajax-clinic-doctor-slider").html("hello");
            }
        }
    });
}


/* Use          :   Used to open booking page by Doctor
 * Access       :   Public
 * 
 */
function MainClinicDoctorBooking(booktype,currentpage){
    var doctorslotid = jQuery("#clinic-doctor-booking").attr('doctorslotid'); 
    var bookdetailpage = jQuery("#clinic-doctor-booking").attr('bookdetailpage');
    
    if(booktype == 0){  
        var queueno = jQuery('#queue-no').html();
        //var bookdate = jQuery("#now-booking").attr('book-date');
    }else if(booktype == 1){
        //var bookdate = jQuery("#now-booking").attr('slotdate');
        var slotdetailid = jQuery("#clinic-doctor-booking").attr('slotdetailid');
    }
    var clinicid = jQuery("#clinic-doctor-booking").attr('clinicid');
    var doctorid = jQuery("#clinic-doctor-booking").attr('doctorid');
    var currentdate = jQuery("#clinic-doctor-booking").attr('nowdate');

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
                //console.log(data);
                if(data !=0){     
                    alert('Booking confirmed...');
                    //To access detail page validate bookdetailpage=1
                    if(bookdetailpage==1){
                        RefreshClinicBookingDetailPage(doctorslotid,currentdate);
                    }else{
                        RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
                    }
                    
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
                    if(bookdetailpage==1){
                        RefreshClinicBookingDetailPage(doctorslotid,currentdate);
                    }else{
                        RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
                    }
                    //RefreshClinicDoctorBooking(clinicid,currentdate);
                }               
                jQuery.unblockUI();       
            } 
        });
    }
}




function RefreshClinicDoctorBooking(clinicid,currentdate,pageno){
    //jQuery("#ajax-clinic-doctor-slider").html('hello');
    var clinicid = clinicid;
    //var doctorid = doctorid;
    var currentdate = currentdate; 
    var pageno = pageno;
    //var dataValues = 'currentdate='+currentdate+'&doctorid='+doctorid; 
    var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&pageno='+pageno; 
    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
    jQuery.ajax({
        type: "POST",
        url : base_url+"clinic/ajax-settings-dashboard",
        data : dataValues,

        success : function(data){
            if(data !=0){
                jQuery("#ajax-clinic-doctor-slider").html(data);
                ClinicDoctorBookingLoad();
                loadButtonBooking();
                DeleteClinicBookingPopup();
                DeleteClinicBooking();
                ClinicDashboardPagination();
                ClinicDashboardDatePicker();
                //Clinic Stop Queue
                ClinicQueueStopped();
                //Clinic Start Queue
                ClinicQueueStarted();
            }
            jQuery.unblockUI();       
        } 
    });
}




function DeleteClinicBookingPopup(){
        jQuery('.clinic-appoint-delete').click(function(){
        //jQuery('div[class^="appoint-delete"]').on('click', function() {   
        //var tg = jQuery("#datepicker_122").attr('value');
        //console.log(tg);
            var bookingid = jQuery(this).attr('id');
            var booktype = jQuery(this).attr('bktype');
            var sts = jQuery(this).attr('sts');
            var doctorslotid = jQuery(this).attr('doctorslotid');
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
                jQuery('#clinic-doctor-booking').hide(); 
                jQuery('#cancel-clinic-booking').hide(); 
            }else{ 
                jQuery('#cancel-booking').show();
                jQuery('#cancel-clinic-booking').show();
            }
            jQuery('#now-booking').hide();
            jQuery('#clinic-doctor-booking').hide();
            
            jQuery('#docname').html(jQuery(this).attr('docname'));
            jQuery('#docspeciality').html(jQuery(this).attr('docspeciality'));
            jQuery('#docimage').attr('src', jQuery(this).attr('docimage'));
            jQuery('#doctor-charge').html(jQuery(this).attr('doccharge'));       
            jQuery('#book-date').html(jQuery(this).attr('displaydate'));

                var doctorid = jQuery(this).attr('doctorid');
                var clinicid = jQuery(this).attr('clinicid');
                var currentdate = jQuery(this).attr('bookdate');
                
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
                            
                            jQuery('#cancel-clinic-booking').attr('bktype',booktype);
                            jQuery('#cancel-clinic-booking').attr('deleteid',bookingid);
                            
                            jQuery('#cancel-clinic-booking').attr('clinicid',clinicid);
                            jQuery('#cancel-clinic-booking').attr('doctorid',doctorid);
                            jQuery('#cancel-clinic-booking').attr('bookdate',currentdate);
                            jQuery('#cancel-clinic-booking').attr('doctorslotid',doctorslotid);
                        }

                        jQuery.unblockUI();       
                    } 
                });
            
            }    
        });
    }
    
    
    function DeleteClinicBooking(){
    jQuery('#cancel-clinic-booking').click(function(){
        //console.log('hoho');
        var bookdetailpage = jQuery(this).attr('bookdetailpage');
        var bookingid = jQuery(this).attr('deleteid');
        var booktype = jQuery(this).attr('bktype');
        if(bookingid !="" && booktype !=""){
            var doctorid = jQuery(this).attr('doctorid');
            var clinicid = jQuery(this).attr('clinicid');
            var currentdate = jQuery(this).attr('bookdate');
            var doctorslotid = jQuery(this).attr('doctorslotid');
            var currentpage = jQuery(this).attr('currentpage');

            var dataValues = 'clinicid='+clinicid+'&currentdate='+currentdate+'&doctorid='+doctorid+'&bookingid='+bookingid+'&booktype='+booktype;

            if (confirm('Are you sure you want to cancel this appointment ?')) {
                jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });    
            jQuery.ajax({
                type: "POST",
                url : base_url+"doctor/delete-appointment",
                data : dataValues,

                success : function(data){
                    if(data ==1){
                        if(bookdetailpage == 1){
                            RefreshClinicBookingDetailPage(doctorslotid,currentdate);
                        }else{
                            RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
                        }
                        
                    }
                    jQuery.unblockUI();       
                } 
            }); }
        }
    });
}

/* Use : It is used to pagination of doctors
 * Access : Public
 * 
 */
function ClinicDashboardPagination(){
    jQuery('.move-page').click(function(){
        var currentpage = jQuery(this).attr('id');
        var clinicid = jQuery(this).attr('clinicid');
        var currentdate = jQuery(this).attr('currentdate');
        RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
    });
}


function ChangeClinicDashboardByDate(inputdate){ 
    var currentdate = jQuery.datepicker.formatDate('dd-mm-yy', new Date(inputdate))
    RefreshClinicDoctorBooking(32,currentdate,0);
    
}
function ClinicDashboardDatePicker(){
    jQuery("#datepicker_122").datepicker({
        dateFormat: 'DD, d MM, yy',
        onSelect: function(dateText) { 
           var clinicid = jQuery(this).attr('clinicid');

           //ChangeClinicDashboardByDate(dateText);
           var currentdate = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateText));
            RefreshClinicDoctorBooking(clinicid,currentdate,0);
        }
     });
}

function ClinicQueueStopped(){
    //jQuery("#clinic-queue-stopped").click(function(){
    jQuery(".clinic-queue-stopped").click(function(){    
        var bookdetailpage = jQuery(this).attr('bookdetailpage');
        var doctorslotid = jQuery(this).attr('doctorslotid');
        var queuetotal = jQuery(this).attr('queuetotal');
        var currenttotal = jQuery(this).attr('currenttotal');
        var currentdate = jQuery(this).attr('currentdate');
        var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        var currentpage = jQuery(this).attr('currentpage');
            
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
                    if(bookdetailpage==1){
                        RefreshClinicBookingDetailPage(doctorslotid,currentdate);
                    }else{
                        RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
                    }                
                }
                jQuery.unblockUI();       
            } 
        }); }
        
    });
}

function ClinicQueueStarted(){ 
    //jQuery("#clinic-queue-started").click(function(){
    jQuery(".clinic-queue-started").click(function(){    
        var bookdetailpage = jQuery(this).attr('bookdetailpage');
        var slotmanageid = jQuery(this).attr('slotmanageid');
        var doctorslotid = jQuery(this).attr('doctorslotid');
        var currentdate = jQuery(this).attr('currentdate');
        //var doctorid = jQuery(this).attr('doctorid');
        var clinicid = jQuery(this).attr('clinicid');
        var currentpage = jQuery(this).attr('currentpage');
            
        var dataValues = 'slotmanageid='+slotmanageid;
        if (confirm('Are you sure you want to Start the Queue ?')) {
        jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
        jQuery.ajax({
            type: "POST",
            url : base_url+"doctor/start-queue",
            data : dataValues,

            success : function(data){
                if(data !=0){
                    if(bookdetailpage==1){
                        RefreshClinicBookingDetailPage(doctorslotid,currentdate);
                    }else{
                        RefreshClinicDoctorBooking(clinicid,currentdate,currentpage);
                    }
                }
                jQuery.unblockUI();       
            } 
        });   }   
    });
}


/* Use : Refresh clinic booking details page
 * 
 * 
 */
function RefreshClinicBookingDetailPage(doctorslotid,currentdate){
    //jQuery('#AJAX-Clinic-Booking-Detail-Page').html('its done');

    var doctorslotid = doctorslotid;
    var currentdate = currentdate; 
    //var pageno = pageno;

    var dataValues = 'doctorslotid='+doctorslotid+'&currentdate='+currentdate; 
    jQuery.blockUI({ message: '<h1> '+base_loading_image+' <br /> Please wait for a moment</h1>' });
    jQuery.ajax({
        type: "POST",
        url : base_url+"clinic/ajax-dashboard-booking",
        data : dataValues,

        success : function(data){
            if(data !=0){
                jQuery("#AJAX-Clinic-Booking-Detail-Page").html(data);
                
                ClinicDoctorBookingLoad();
                loadButtonBooking();
                DeleteClinicBookingPopup();
                DeleteClinicBooking();
                //ClinicDashboardPagination();
                //ClinicDashboardDatePicker();
                //Clinic Stop Queue
                ClinicQueueStopped();
                //Clinic Start Queue
                ClinicQueueStarted();
                //OpenDefualtCalender();
                ClinicDashboardBookingDatePicker();
            }
            jQuery.unblockUI();       
        } 
    });
    
}


function ClinicDashboardBookingDatePicker(){
    jQuery(".DashboardBookingDatePicker").datepicker({
        dateFormat: 'DD, d MM, yy',
        onSelect: function(dateText) { 
            var doctorslotid = jQuery(this).attr('doctorslotid');
            var currentdate = jQuery.datepicker.formatDate('dd-mm-yy', new Date(dateText));
            RefreshClinicBookingDetailPage(doctorslotid,currentdate);     
        }
     });
}

function OpenDefualtCalender() {
    //jQuery( ".DashboardBookingDatePicker" ).datepicker({dateFormat: 'DD, d MM, yy',});
    //jQuery( ".DashboardBookingDatePicker" ).datepicker({dateFormat:"yy/mm/dd"}).datepicker("setDate",new Date(2015,6,27));
}