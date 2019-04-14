@include('common.header-clinic-section')
    <!--END HEADER-->
  
  <div class="clear"></div>
  
  
  
<div id="clinic-form-container">

    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

            <!--END OF FORM NAV-->




    <div class="form-nav-page padding-bottom-0">
   <div class="procedures">
   <form id="form-clinic-procedures" method="POST" action="">		
        <div class="field-container" style="background-color:#F00"> 
            <div class="field-container-xsmall new-marr1">
                   <div class="field-name">
                        <label>ADD NEW PROCEDURE</label>
                      </div><!--END OF FIELD NAME--> 
                  
                          <div class="field-type">
                             <input id="name" name="name" class="input-xsmall-b" type="text">
                          </div><!--END OF FIELD TYPE-->
                   
                   <div class="field-name">
                    <label>Name</label>
                  </div><!--END OF FIELD NAME--> 
             </div><!--END OF FIELD CONTAINER XSMALL-->
             
             <div class="field-container-xsmall-b new-wd11 mar-top">
                       <div class="field-type">
                           <!--<input id="duration" name="duration" class="input-xsmall-c" type="text">-->
                        <select id="duration" name="duration" class="new-select">
                            <option value="15"selected>15 min</option>
                            <option value="30">30 min</option>
                            <option value="45">45 min</option>
                            <option value="60" >60 min</option>
                            <option value="75" >75 min</option>
                            <option value="90" >90 min</option>
                            <option value="105" >105 min</option>
                            <option value="120" >120 min</option>
                        </select>
                        </div><!--END OF FIELD TYPE-->
                   
                       <div class="field-name">
                         <label>Duration</label>
                       </div><!--END OF FIELD NAME--> 
              </div><!--END OF FIELD CONTAINER XSMALL-->
              
               <div class="field-container-xsmall-c mar-top">
                       <div class="field-type">
                           <label class="l-blue"></label>
                           <input id="price" name="price" class="input-xsmall-c" type="text">
                        </div><!--END OF FIELD TYPE-->
                   
                       <div class="field-name">
                           <label class="new-mal3">Price</label>
                       </div><!--END OF FIELD NAME--> 
              </div><!--END OF FIELD CONTAINER XSMALL-->
              
              <div id="clinic-add-procedures" clinicid="<?php echo $clinicdetails->ClinicID;?>" class="btn-add-b font-type-Montserrat ">ADD NOW</div>     
        </div><!--END OF FIELD CONTAINER-->
        
       <div class="clear"></div>     
   </form><!--END OF PROCEDURES-FORM-->
   </div><!--END OF PROCEDURES-->
   
  <?php //echo '<pre>'; print_r($procedures); echo '</pre>'; ?>

<div class="clear"></div>

   <div class="procedures-state">
     <div class="procedures-title">
      <label>CURRENT PROCEDURES</label>
     </div><!--END OF PROCEDURES TITLE-->
   </div><!--END OF PROCEDURES STATE-->
   
    <div id="load-procedures-ajax"><!-- Load by AJAX Start -->
        
       @include('ajax.clinic.load-procedures')
    
   </div><!-- Load by AJAX End -->
   
   

   
<!--   <div class="field-container b-top padding-left padding-top padding-bottom"> 
           <div class="btn-update font-type-Montserrat">Update Changes</div>
            <div class="btn-cancel font-type-Montserrat">Cancel</div>
            <div class="clear"></div>
         </div>END OF FIELD CONTAINER-->
         
   </div><!--END OF FORM NAV PAGE-->
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->








@include('common.footer-clinic-section')