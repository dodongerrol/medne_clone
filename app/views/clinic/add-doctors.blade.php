@include('common.header-clinic-section')
<!--END HEADER-->

  <div class="clear"></div>
  
  
  
<div id="clinic-form-container">

    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

            <!--END OF FORM NAV-->




    <div class="form-nav-page">
   <div class="dr-option-nav">
   <ul>
   <li><a href="{{URL::to('app/clinic/clinic-doctors-view')}}">VIEW DOCTORS</a></li>
   <li><a href="{{URL::to('app/clinic/doctor-availability')}}">SET AVAILABILITY</a></li>
   <li><a href="{{URL::to('app/clinic/clinic-doctor')}}" class="active">ADD DOCTOR</a></li>
   </ul>
   </div> <!--END OF DR OPTION NAV-->
   
   <div class="clear"></div>
   <div id="header-nortification"></div>
   <div class="clear"></div>
<!--   <div class="search-bar">
   <div class="field-name">
         <label>SEARCH DOCTOR</label>
         </div>END OF FIELD NAME
         
         <div class="field-container">
  			<div class="field-name">
         		<label>Search by Name or Email</label>
        		 </div>END OF FIELD NAME 
         
         	<div class="field-type">
         		<input type="text">
         			</div>END OF FIELD TYPE
     	 </div>END OF FIELD CONTAINER
   <div class="clear"></div>    
   </div>  END OF SEARCH BAR-->
   
   
   
<div class="clear"></div>

<form id="form-clinic-add-doctors" method="POST" action="">
    
     <div class="portfolio-image-section">
       <div class="img-portflio">
         <img id="doctor-profile-img" src="{{ URL::asset('assets/images/img-portfolio-place.png') }}" width="100%" alt=""/>
       </div><!--END OF IMG PROTFOLIO-->
       
        <div class="field-name">
         <label>Upload Doctor Image</label>
         </div><!--END OF FIELD NAME-->     
     </div><!--END OF PORTFOLIO IMAGE SECTION-->
     
     
     <div class="portfolio-field-section">
     
        <div class="field-container">
          <div class="field-name">
                 <label>DOCTOR NAME</label>
                 </div><!--END OF FIELD NAME--> 
                 <div class="field-type">
                     <input type="text" id="name" name="name">
                 <input type="file" id="doctor-profile-file" name="file" style="display: none">
                 <input id="update-image" type="text" name="image" style="display: none" value="<?php //echo $clinicdetails['image'];?>">
                 </div><!--END OF FIELD TYPE-->
        </div><!--END OF FIELD CONTAINER-->
       
        <div class="field-container">
            <div class="field-name">
            <label>QUALIFICATION</label>
            </div><!--END OF FIELD NAME--> 
            <div class="field-type">
                <input id="qualification" name="qualification" type="text">
            </div><!--END OF FIELD TYPE-->
        </div><!--END OF FIELD CONTAINER-->
       
        <div class="field-container">
            <div class="field-name">
            <label>DESIGNATION</label>
            </div><!--END OF FIELD NAME--> 
            <div class="field-type">
                <input id="speciality" name="speciality" type="text">
            </div><!--END OF FIELD TYPE-->
        </div><!--END OF FIELD CONTAINER-->
           
        <div class="clear"></div>
         
        <div class="field-container">
            <div class="field-container-xsmall">
                  <div class="field-name">
                    <label>PHONE</label>
                  </div><!--END OF FIELD NAME--> 

                  <div class="field-type">
                      <input id="code" name="code" class="input-xsmall" type="text" value="+65">
                  </div><!--END OF FIELD TYPE-->

                   <div class="field-name">
                    <label>Code</label>
                  </div><!--END OF FIELD NAME--> 

             </div><!--END OF FIELD CONTAINER SMALL-->
         
         
            <div class="field-container-medium">
               <div class="field-type">
                   <input id="phone" name="phone" class="input-medium new-pd5" type="text">
                     </div><!--END OF FIELD TYPE-->

                  <div class="field-name new-mart2">
                           <label>Phone No</label>
                             </div><!--END OF FIELD NAME--> 

            </div><!--END OF FIELD CONTAINER MEDIUM-->
        </div><!--END OF FIELD CONTAINER-->
        
         <div class="clear"></div>
        
        <div class="field-container">
            <div class="field-name">
                <label>EMERGENCY CONTACT</label>
            </div><!--END OF FIELD NAME--> 
               <div class="clear"></div>
        <div class="field-container-xsmall">
           
              <div class="field-type">
                  <input id="emergency-code" name="emergency-code" class="input-xsmall" type="text" value="+65">
              </div><!--END OF FIELD TYPE-->
               
               <div class="field-name">
                <label>Code</label>
              </div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
         
         
         <div class="field-container-medium-b">
            <div class="field-type">
                <input id="emergency-phone" name="emergency-phone" class="input-medium" type="text">
       		  </div><!--END OF FIELD TYPE-->
               
               <div class="field-name new-mart2">
             		<label>Phone No</label>
			  </div><!--END OF FIELD NAME--> 
               
         </div><!--END OF FIELD CONTAINER SMALL-->
        </div><!--END OF FIELD CONTAINER-->
        
         <div class="clear"></div>
        
        
         <div class="field-container">
         
         	<div class="field-name">
         		<label>EMAIL</label>
   			</div><!--END OF FIELD NAME--> 
         	<div class="field-type">
                    <input id="email" name="email" type="text">
   			</div><!--END OF FIELD TYPE-->
                    
       	</div><!--END OF FIELD CONTAINER-->
        
        
        
        
        <div class="field-container">
            <div class="field-name">
         		<label>Procedures</label>
   			</div><!--END OF FIELD NAME-->
            <div class="clear"></div> 
            <div class="field-type">
            <div class="select-box-v2 cus-int-height">
                <select id="procedure" name="procedure" multiple="multiple">
                  <!--<option value="">Select</option>-->
                  <?php if($clinicprocedures){
                      foreach($clinicprocedures as $procedures){
                      echo '<option value="'.$procedures->ProcedureID.'">'.$procedures->Name.'</option>';
                      }  }?>
              </select>
                 <div class="clear"></div> 
            </div>
               
<!--            <div class="field-type">
                <select id="procedure" name="procedure" class="input">
                    <option value="">Select</option>
                </select>
                

            <div class="clear"></div>
            </div>END OF FIELD TYPE             -->
       	</div><!--END OF FIELD CONTAINER-->
       
        
        
       
         <div class="field-container mar-top"> 
             <div id="add-new-doctors" clinicid="<?php echo $clinicdetails->ClinicID;?>" class="btn-update new-marr1 font-type-Montserrat ">Update Changes</div>
            <div class="btn-cancel font-type-Montserrat ">Cancel</div>
         </div><!--END OF FIELD CONTAINER-->
       
      <div class="clear"></div>
      
     </div><!--END OF PORTFOLIO FIELD SECTION-->
     

   </div><!--END OF FORM NAV PAGE-->
  </form>
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->



@include('common.footer-clinic-section')
