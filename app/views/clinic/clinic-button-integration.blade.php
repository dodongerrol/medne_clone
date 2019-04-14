@include('common.header-clinic-section')
<!--END HEADER-->
  
  <div class="clear"></div>
  
  <?php //echo '<pre>'; print_r($clinicdetails); echo '</pre>';?>
  
<div id="clinic-form-container">
    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

    <!--END OF FORM NAV-->
    
   
   <div class="form-nav-page">
       
     <div class="portfolio-image-section">
       
       
       <input type="hidden" id="h-clinicID" value="{{$clinicID}}">
       
         
     </div><!--END OF PORTFOLIO IMAGE SECTION-->
     
     
     
     
     <div class="portfolio-field-section">
      
       
    
       
        
        
       
      
       
       
         
		<div class="field-container">
             <div class="field-name">
             	<label>Code Snippet</label>
             		</div><!--END OF FIELD NAME--> 
             
             <div class="field-type">
                 <textarea rows="4" cols="50" id="widget_code"> </textarea>
             		</div><!--END OF FIELD TYPE-->
     	</div><!--END OF FIELD CONTAINER-->  
         
         
         
         
       
        
    
        
        
        
       
       
       
         
       
       
       
      <div class="clear"></div>
      
      
     
     </div><!--END OF PORTFOLIO FIELD SECTION-->
     </form>
   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->




@include('common.footer-clinic-section')




