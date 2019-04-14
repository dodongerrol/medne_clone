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
   <li><a href="{{URL::to('app/clinic/clinic-doctors-view')}}" class="active">VIEW DOCTORS</a></li>
   <li><a href="{{URL::to('app/clinic/doctor-availability')}}">SET AVAILABILITY</a></li>
   <li><a href="{{URL::to('app/clinic/clinic-doctor')}}">ADD DOCTOR</a></li>
   </ul>
   </div> <!--END OF DR OPTION NAV-->
   
   <div class="clear"></div>
   
   
   
<div class="clear"></div>

 
    <div class="dr-view-title">
       <div class="title-a">Name</div> <!--END OF TITLE-->
        <div class="title-b">Phone</div> <!--END OF TITLE-->
         <div class="title-c">Procedures</div> <!--END OF TITLE-->
          <div class="clear"></div> <!--END OF CLEAR-->
    </div><!--END OF DR VIEW TITLE-->

<div id="load-doctor-view-ajax">    
    <?php if($doctors){ 
        foreach($doctors as $doctorList) {
            if(empty($doctorList['image'])){
                $docimage = URL::asset('assets/images/no-doctor.png');
            }else{
                $docimage = $doctorList['image'];
            }
?>
    <div class="dr-view-detail">
        <a href="{{URL::to('app/clinic/doctor-update-page/'.$doctorList['doctorid'])}}"><div class="dr-image"><img src="{{ $docimage }}" alt="" width="60" height="60"/></div></a> <!--END OF DR IMAGE-->
       <div class="dr-detail-inner">
       <label class="label-dr-name">{{ $doctorList['name'] }} </label>
        <label class="label-dr-no">{{ $doctorList['phone'] }} </label>
         <label class="label-dr-Procedures">{{ $doctorList['procedures'] }}</label>
         <div id="delete-doctor-view" doctorid="{{ $doctorList['doctorid'] }}" class="icn-close"><img src="{{ URL::asset('assets/images/icn-close-dark.png') }} " width="19" height="20"  alt=""/></div>
         <div class="clear"></div>
       </div><!--END OF DETAIL INNER-->
       <div class="clear"></div>
    </div><!--END OF DR VIEW DETAIL-->
     
    <div class="clear"></div>
     
    <?php } }else{
        echo '<div class="clinic-empty-state">
     <div class="info">Doctor view is not available <br> Please Click Here to add your first doctor</div>
     <a href="'.URL::to('app/clinic/clinic-doctor').'"><div class="btn-add font-type-Montserrat">Add a Doctor</div></a>
   </div>';
    } ?> 
    
</div>    




   </div><!--END OF FORM NAV PAGE-->
  
  <div class="clear"></div>
</div><!--END OF CLINIC FORM CONTAINER-->






@include('common.footer-clinic-section')