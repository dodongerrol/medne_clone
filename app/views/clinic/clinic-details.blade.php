@include('common.header-clinic-section')
<!--END HEADER-->
  
  <div class="clear"></div>
  
  <?php //echo '<pre>'; print_r($clinicdetails); echo '</pre>';?>
  
<div id="clinic-form-container">
    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

    <!--END OF FORM NAV-->
    
   
   <div class="form-nav-page">
       <form id="form-clinic-details-update" method="POST" action="">
     <div class="portfolio-image-section">
       
       <div class="img-portflio">
           <?php if(!empty($clinicdetails['image'])){ ?>
           <img id="clinic-profile-img" src="<?php echo $clinicdetails['image'];?>" width="100%" alt=""/>
           <?php }else{ ?>
            <img id="clinic-profile-img" src="{{ URL::asset('assets/images/img-portfolio-place.png') }} " width="100%" alt=""/>
           <?php } ?>
       </div><!--END OF IMG PROTFOLIO-->
       
       
        <div class="field-name">
         <label>Upload Clinic Logo</label>
         </div><!--END OF FIELD NAME--> 
         
     </div><!--END OF PORTFOLIO IMAGE SECTION-->
     
     
     
     
     <div class="portfolio-field-section">
       <div class="field-container">
         <div class="field-name">
         <label>CLINIC NAME</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input type="text" id="name" name="name" value="<?php echo $clinicdetails['name'];?>">
             <input type="file" id="clinic-profile-file" name="file" style="display: none">
             <input id="update-image" type="text" name="image" style="display: none" value="<?php echo $clinicdetails['image'];?>">
         </div><!--END OF FIELD TYPE-->
       </div><!--END OF FIELD CONTAINER-->
       
       
      
       
        <div class="field-container">
         <div class="field-name">
         <label>ADDRESS</label>
         </div><!--END OF FIELD NAME--> 
         <div class="field-type">
             <input type="text" id="address" name="address" value="<?php echo $clinicdetails['address'];?>" >
             <div class="field-name new-mart2">
            <label class=" new-fs">Street</label>
            </div><!--END OF FIELD NAME--> 
         </div><!--END OF FIELD TYPE-->
         
       </div><!--END OF FIELD CONTAINER-->
       
        
        
       
       <div class="field-container">
        <div class="field-container-small">
           
              
              
              <div class="field-type">
                  <input class="input-small" id="city" type="text" name="city" value="<?php echo $clinicdetails['city'];?>">
               </div><!--END OF FIELD TYPE-->
               
               <div class="field-name">
                <label>City</label>
              </div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
         
         
         <div class="field-container-small">
            
              
              <div class="field-type">
                  <input class="input-small" type="text" id="state" name="state" value="<?php echo $clinicdetails['state'];?>" >
               </div><!--END OF FIELD TYPE-->
               
               
               <div class="field-name">
                    <label>State</label>
                 </div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
        </div><!--END OF FIELD CONTAINER-->
       
       
          <div class="field-container">
         	<div class="field-container-small">
           
               
              
              <div class="field-type">
                  <input class="input-small" type="text" id="country" name="country" value="<?php echo $clinicdetails['country'];?>">
              		</div><!--END OF FIELD TYPE-->
                        
                        
                        <div class="field-name">
                            <label>Country</label>
                        </div><!--END OF FIELD NAME--> 
              
              </div><!--END OF FIELD CONTAINER SMALL-->
         
         
         <div class="field-container-small">
           
              
              <div class="field-type">
                  <input class="input-small" type="text" id="postal" name="postal" value="<?php echo $clinicdetails['postal'];?>">
               		</div><!--END OF FIELD TYPE-->
                        
                        
                        <div class="field-name">
             <label>Postal code</label>
           		</div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
         </div><!--END OF FIELD CONTAINER-->
         
         
         <div class="clear"></div>
         
		<div class="field-container">
             <div class="field-name">
             	<label>DESCRIPTION</label>
             		</div><!--END OF FIELD NAME--> 
             
             <div class="field-type">
                 <textarea rows="4" cols="50" id="description"> <?php echo $clinicdetails['description'];?></textarea>
             		</div><!--END OF FIELD TYPE-->
     	</div><!--END OF FIELD CONTAINER-->  
         
         
         
         <div class="field-container">
        <div class="field-container-xsmall">
           
              <div class="field-name">
                <label>PHONE</label>
              </div><!--END OF FIELD NAME--> 
              
              <div class="field-type">
                  <input class="input-xsmall" type="text" name="code" value="<?php echo $clinicdetails['code'];?>">
               </div><!--END OF FIELD TYPE-->
               
               <div class="field-name">
                   <label class="new-fs">Code</label>
              </div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
         
         
         <div class="field-container-medium new-mart6">
            <div class="field-type">
                <input class="input-medium" type="text" id="phone" name="phone" value="<?php echo $clinicdetails['phone'];?>">
               		</div><!--END OF FIELD TYPE-->
               
               <div class="field-name">
             		<label class="new-fs">Phone No</label>
           				</div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
        </div><!--END OF FIELD CONTAINER-->
        
        
         <div class="clear"></div>
        
        
         <div class="field-container">
         
         	<div class="field-name">
         		<label>EMAIL</label>
         			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input type="text" name="email" id="email" value="<?php echo $clinicdetails['email'];?>">
         			</div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        
        
        
        
        <div class="field-container">
         
         	<div class="field-name">
         		<label>WEBSITE</label>
         			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input type="text" id="website" name="website" value="<?php echo $clinicdetails['website'];?>">
         			</div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        
        
        
        
        
        <div class="field-container">
         
         	<div class="field-name">
         		<label>PERSONALIZED HEADING</label>
         			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input type="text" id="title" name="title" value="<?php echo $clinicdetails['custom_title'];?>">
         			</div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        
        
        
       
       
       
       <div class="field-container">
             <div class="field-name">
             	<label>PERSONALIZED MESSAGE</label>
             		</div><!--END OF FIELD NAME--> 
             
             <div class="field-type">
             	<textarea rows="4" cols="50"></textarea>
             		</div><!--END OF FIELD TYPE-->
     	</div><!--END OF FIELD CONTAINER-->  
         
       
       
         <div class="field-container"> 
             <div id="clinic-details-updated" clinicid="<?php echo $clinicdetails['clinicid'];?>" class="btn-update font-type-Montserrat">Update Changes</div>
             <a href="{{URL::to('app/clinic/clinic-details')}}"><div class="btn-cancel font-type-Montserrat">Cancel</div></a>
         </div><!--END OF FIELD CONTAINER-->
       
      <div class="clear"></div>
      
      
     
     </div><!--END OF PORTFOLIO FIELD SECTION-->
     </form>
   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->




@include('common.footer-clinic-section')




