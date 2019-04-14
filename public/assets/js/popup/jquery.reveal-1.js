/* kk
 * jQuery Reveal Plugin 1.0
 * www.ZURB.com
 * Copyright 2010, ZURB
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
*/

function loadButtonBooking(){
    jQuery('#queue-book').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal(jQuery(this).data());
                jQuery('#book-type-text').html("Queue Number");
                jQuery('.book-type-q').show();
                jQuery('.book-type-a').hide();
                jQuery('#now-booking').attr('bookoption',0);
                jQuery('#cancel-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                jQuery('#doctor-booking').attr('bookoption',0);
                
                jQuery('#queue-no').html(jQuery('#queueno').html());
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                jQuery('#now-booking').show();
                jQuery('#doctor-booking').show();
    });
    jQuery('#slot-book').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal($(this).data());
                jQuery('#book-type-text').html("Appointment Time");
                jQuery('.book-type-a').show();
                jQuery('.book-type-q').hide();
                jQuery('#now-booking').attr('bookoption',1);
                jQuery('#doctor-booking').attr('bookoption',1);
                jQuery('#cancel-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                jQuery('#now-booking').show();
                jQuery('#doctor-booking').show();
                
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                
                var slottime = jQuery(this).attr('slottime');
                jQuery('#now-booking').attr('slotdetailid',jQuery(this).attr('slotdetailid'));
                jQuery('#doctor-booking').attr('slotdetailid',jQuery(this).attr('slotdetailid'));
                jQuery('#slot-time').html(slottime.substring(0,slottime.length-2));
                jQuery('#slot-time-peak').html(slottime.substring(slottime.length-2));
    });
    jQuery('.queue-book-new').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal(jQuery(this).data());
                jQuery('#book-type-text').html("Queue Number");
                jQuery('.book-type-q').show();
                jQuery('.book-type-a').hide();
                jQuery('#now-booking').attr('bookoption',0);
                jQuery('#cancel-clinic-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                
                jQuery('#clinic-doctor-booking').attr('bookoption',0);
                jQuery('#clinic-doctor-booking').attr('doctorslotid',jQuery(this).attr('doctorslotid'));
                jQuery('#clinic-doctor-booking').attr('clinicid',jQuery(this).attr('clinicid'));
                jQuery('#clinic-doctor-booking').attr('doctorid',jQuery(this).attr('doctorid'));
                jQuery('#clinic-doctor-booking').attr('nowdate',jQuery(this).attr('book-date'));
                
                
                jQuery('#queue-no').html(jQuery(this).attr('queueno')); 
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                jQuery('#now-booking').show();
                jQuery('#doctor-booking').show();
                
                jQuery('#docname').html(jQuery(this).attr('docname'));
                jQuery('#docspeciality').html(jQuery(this).attr('docspeciality'));
                jQuery('#docimage').attr('src', jQuery(this).attr('docimage'));
                jQuery('#doctor-charge').html(jQuery(this).attr('doccharge'));       
                jQuery('#book-date').html(jQuery(this).attr('displaydate'));   
                
                
    });
    jQuery('.slot-book-new').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal($(this).data());
                jQuery('#docname').html(jQuery(this).attr('docname'));
                jQuery('#docspeciality').html(jQuery(this).attr('docspeciality'));
                jQuery('#docimage').attr('src', jQuery(this).attr('docimage'));
                jQuery('#doctor-charge').html(jQuery(this).attr('doccharge'));       
                jQuery('#book-date').html(jQuery(this).attr('displaydate'));       
                
                jQuery('#book-type-text').html("Appointment Time");
                jQuery('.book-type-a').show();
                jQuery('.book-type-q').hide();
                jQuery('#now-booking').attr('bookoption',1);
                jQuery('#clinic-doctor-booking').attr('bookoption',1);
                jQuery('#cancel-clinic-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                jQuery('#now-booking').show();
                jQuery('#clinic-doctor-booking').show();
                
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                
                var slottime = jQuery(this).attr('slottime');
                jQuery('#now-booking').attr('slotdetailid',jQuery(this).attr('slotdetailid'));
                jQuery('#slot-time').html(slottime.substring(0,slottime.length-2));
                jQuery('#slot-time-peak').html(slottime.substring(slottime.length-2));
                
                
                jQuery('#clinic-doctor-booking').attr('slotdetailid',jQuery(this).attr('slotdetailid'));
                jQuery('#clinic-doctor-booking').attr('doctorslotid',jQuery(this).attr('doctorslotid'));
                
                jQuery('#clinic-doctor-booking').attr('clinicid',jQuery(this).attr('clinicid'));
                jQuery('#clinic-doctor-booking').attr('doctorid',jQuery(this).attr('doctorid'));
                jQuery('#clinic-doctor-booking').attr('nowdate',jQuery(this).attr('slotdate'));           
    });
    
    jQuery('.slot-popup-clinic').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal($(this).data());
                jQuery('#book-type-text').html("Appointment Time");
                jQuery('.book-type-a').show();
                jQuery('.book-type-q').hide();
                jQuery('#now-booking').attr('bookoption',1);
                //jQuery('#doctor-booking').attr('bookoption',1);
                jQuery('#clinic-doctor-booking').attr('bookoption',1);
                jQuery('#cancel-clinic-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                jQuery('#now-booking').show();
                jQuery('#clinic-doctor-booking').show();
                
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                
                var slotid = jQuery(this).attr('slotid');
                var slottime = jQuery(this).attr('slottime');
                jQuery('#slot-time').html(slottime.substring(0,slottime.length-2));
                jQuery('#slot-time-peak').html(slottime.substring(slottime.length-2));
                jQuery('#now-booking').attr('slotdetailid',slotid);
                //jQuery('#doctor-booking').attr('slotdetailid',slotid);
                
                jQuery('#clinic-doctor-booking').attr('slotdetailid',jQuery(this).attr('slotid'));
                jQuery('#clinic-doctor-booking').attr('doctorslotid',jQuery(this).attr('doctorslotid'));
                
                jQuery('#clinic-doctor-booking').attr('clinicid',jQuery('.slot-book-new').attr('clinicid'));
                jQuery('#clinic-doctor-booking').attr('doctorid',jQuery(this).attr('doctorid'));
                jQuery('#clinic-doctor-booking').attr('nowdate',jQuery('.slot-book-new').attr('slotdate')); 
                jQuery('#docname').html(jQuery(this).attr('docname'));
                jQuery('#docspeciality').html(jQuery(this).attr('docspeciality'));
                jQuery('#docimage').attr('src', jQuery(this).attr('docimage'));
                jQuery('#doctor-charge').html(jQuery(this).attr('doccharge'));       
                jQuery('#book-date').html(jQuery(this).attr('displaydate'));
    });
    
    
    jQuery('.slot-popup').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal($(this).data());
                jQuery('#book-type-text').html("Appointment Time");
                jQuery('.book-type-a').show();
                jQuery('.book-type-q').hide();
                jQuery('#now-booking').attr('bookoption',1);
                jQuery('#doctor-booking').attr('bookoption',1);
                jQuery('#cancel-booking').hide();
                jQuery('#cancel-doctor-booking').hide();
                jQuery('#now-booking').show();
                jQuery('#doctor-booking').show();
                
                jQuery('#user-name').val('');
                jQuery('#user-nric').val('');
                jQuery('#user-mobile').val('');
                jQuery('#user-email').val('');
                jQuery('#user-name').removeAttr('readonly');
                jQuery('#user-nric').removeAttr('readonly');
                jQuery('#user-mobile').removeAttr('readonly');
                jQuery('#user-email').removeAttr('readonly');
                
                var slotid = jQuery(this).attr('slotid');
                var slottime = jQuery(this).attr('slottime');
                jQuery('#slot-time').html(slottime.substring(0,slottime.length-2));
                jQuery('#slot-time-peak').html(slottime.substring(slottime.length-2));
                jQuery('#now-booking').attr('slotdetailid',slotid);
                jQuery('#doctor-booking').attr('slotdetailid',slotid);
                
    });
    jQuery('.appoint-done').click(function(e){
            e.preventDefault();
		var modalLocation = jQuery(this).attr('data-reveal-id');
		jQuery('#'+modalLocation).reveal($(this).data());
                jQuery('#doctor-done').attr('appointment',jQuery(this).attr('appointid')); 
                jQuery('#diagnosis').val('');
    });
}



jQuery("document").ready(function(){
    //loadButtonBooking();
    //jQuery('#queue-slot').hide();
    
//    jQuery('#queue-book').click(function(e){
//            e.preventDefault();
//		var modalLocation = jQuery(this).attr('data-reveal-id');
//		jQuery('#'+modalLocation).reveal(jQuery(this).data());
//                jQuery('#book-type-text').html("Queue Number");
//                jQuery('.book-type-q').show();
//                jQuery('.book-type-a').hide();
//                jQuery('#now-booking').attr('bookoption',0);
//    });
//    jQuery('#slot-book').click(function(e){
//            e.preventDefault();
//		var modalLocation = jQuery(this).attr('data-reveal-id');
//		jQuery('#'+modalLocation).reveal($(this).data());
//                jQuery('#book-type-text').html("Appointment Time");
//                jQuery('.book-type-a').show();
//                jQuery('.book-type-q').hide();
//                jQuery('#now-booking').attr('bookoption',1);
//    });
    
   
    
    
    $.fn.reveal = function(options) {
        
        
        var defaults = {  
	    	animation: 'fadeAndPop', //fade, fadeAndPop, none
		    animationspeed: 300, //how fast animtions are
		    closeonbackgroundclick: true, //if you click background will modal close?
		    dismissmodalclass: 'close-reveal-modal' //the class of a button or element that will close an open modal
    	}; 
    	
        //Extend dem' options
        var options = $.extend({}, defaults, options); 
	
        return this.each(function() {
        
/*---------------------------
 Global Variables
----------------------------*/
        	var modal = $(this),
        		topMeasure  = parseInt(modal.css('top')),
				topOffset = modal.height() + topMeasure,
          		locked = false,
				modalBG = $('.reveal-modal-bg');

/*---------------------------
 Create Modal BG
----------------------------*/
			if(modalBG.length == 0) {
				modalBG = $('<div class="reveal-modal-bg" />').insertAfter(modal);
			}		    
     
/*---------------------------
 Open & Close Animations
----------------------------*/
			//Entrance Animations
			modal.bind('reveal:open', function () {
			  modalBG.unbind('click.modalEvent');
				$('.' + options.dismissmodalclass).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({'top': $(document).scrollTop()-topOffset, 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"top": $(document).scrollTop()+topMeasure + 'px',
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					}
					if(options.animation == "fade") {
						modal.css({'opacity' : 0, 'visibility' : 'visible', 'top': $(document).scrollTop()+topMeasure});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());					
					} 
					if(options.animation == "none") {
						modal.css({'visibility' : 'visible', 'top':$(document).scrollTop()+topMeasure});
						modalBG.css({"display":"block"});	
						unlockModal()				
					}
				}
				modal.unbind('reveal:open');
			}); 	

			//Closing Animation
			modal.bind('reveal:close', function () {
			  if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"top":  $(document).scrollTop()-topOffset + 'px',
							"opacity" : 0
						}, options.animationspeed/2, function() {
							modal.css({'top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
							unlockModal();
						});					
					}  	
					if(options.animation == "fade") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"opacity" : 0
						}, options.animationspeed, function() {
							modal.css({'opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
							unlockModal();
						});					
					}  	
					if(options.animation == "none") {
						modal.css({'visibility' : 'hidden', 'top' : topMeasure});
						modalBG.css({'display' : 'none'});	
					}		
				}
				modal.unbind('reveal:close');
			});     
   	
/*---------------------------
 Open and add Closing Listeners
----------------------------*/
        	//Open Modal Immediately
    	modal.trigger('reveal:open')
			
			//Close Modal Listeners
			var closeButton = $('.' + options.dismissmodalclass).bind('click.modalEvent', function () {
                          //console.log('hello');
			  modal.trigger('reveal:close')
			});
			
			if(options.closeonbackgroundclick) {
                            
				modalBG.css({"cursor":"pointer"})
				modalBG.bind('click.modalEvent', function () {
                                    //console.log('hi');
				  modal.trigger('reveal:close')
				});
			}
			$('body').keyup(function(e) {
        		if(e.which===27){ modal.trigger('reveal:close'); } // 27 is the keycode for the Escape key
			});
			
			
/*---------------------------
 Animations Locks
----------------------------*/
			function unlockModal() { 
				locked = false;
			}
			function lockModal() {
				locked = true;
			}	
			
        });//each call
    }//orbit plugin call
    
});

(function($) {

/*---------------------------
 Defaults for Reveal
----------------------------*/
	 
/*---------------------------
 Listener for data-reveal-id attributes
----------------------------*/

//	$('a[data-reveal-id]').live('click', function(e) {
//		e.preventDefault();
//		var modalLocation = $(this).attr('data-reveal-id');
//		$('#'+modalLocation).reveal($(this).data());
//	});
        
//        jQuery('#clickmenow').click(function(){
//            alert('hi');
//        });
//        $("#clickmenow1").click(function(e) {
//            alert('hi');
//        });
//        
//        $('a[data-reveal-id]').live('click', function(e) {
//		e.preventDefault();
//		var modalLocation = $(this).attr('data-reveal-id');
//		$('#'+modalLocation).reveal($(this).data());
//	});

/*---------------------------
 Extend and Execute
----------------------------*/

})(jQuery);
        
