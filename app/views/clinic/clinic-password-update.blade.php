@include('common.header-clinic-section')
<!--END HEADER-->
  
  <div class="clear"></div>
  
  
  
<div id="clinic-form-container">

    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

            <!--END OF FORM NAV-->



    <div class="form-nav-page">
       <form id="form-clinic-password-update" method="POST">
    
           <div id="password-message" class="notify3">  </div>

     
     
     <div class="updatepw-field-section ">
       <div class="field-container">
         <div class="field-name">
         <label>OLD PASSWORD</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input id="oldpass" name="oldpass" type="password">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
       
       
      
       
        <div class="field-container ">
         <div class="field-name">
         <label>NEW PASSWORD</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input id="newpass" name="newpass" type="password">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
       
       
       
       
        <div class="field-container mar-bottom2">
         <div class="field-name">
         <label>CONFIRM PASSWORD</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input id="conpass" name="conpass" type="password">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
       
       
         
         <div class="clear"></div>
         
		
         
         <div class="field-container"> 
             <div id="clinic-update-password" clinicuserid="{{$clinicuserdata->UserID}}" class="btn-update font-type-Montserrat">Update Changes</div>
            <div class="btn-cancel font-type-Montserrat">Cancel</div>
         </div><!--END OF FIELD CONTAINER-->
       
      <div class="clear"></div>
      
      
     
     </div><!--END OF PORTFOLIO FIELD SECTION-->
     </form>
   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->






@include('common.footer-clinic-section')