@include('common.header-clinic-section')
<!--END HEADER-->
  
  <div class="clear"></div>
  
  
  
<div id="clinic-form-container">

    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

            <!--END OF FORM NAV-->



    <div class="form-nav-page padding-bottom-0">
   <div class="dr-option-nav">
   <ul>
   <li><a href="{{URL::to('app/clinic/clinic-doctors-view')}}">VIEW DOCTORS</a></li>
   <li><a href="#">SET AVAILABILITY</a></li>
   <li><a href="{{URL::to('app/clinic/clinic-doctor')}}">ADD DOCTOR</a></li>
   </ul>
   </div> <!--END OF DR OPTION NAV-->
   
  
   
   
   
   
   
<div class="clear"></div>
   <div class="clinic-empty-state">
     <div class="info">Doctor view is not available <br /> Please Click Here to add your first doctor</div> <!--END OF info-->
     <a href="{{URL::to('app/clinic/clinic-doctor')}}"><div class="btn-add font-type-Montserrat">Add a Doctor</div></a>
   </div><!--END OF EMPTY STATE-->
   
   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->







@include('common.footer-clinic-section')