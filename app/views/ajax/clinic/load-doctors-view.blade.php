<?php if($doctors){ 
        foreach($doctors as $doctorList) {
    
?>
    <div class="dr-view-detail">
       <div class="dr-image"><img src="{{ $doctorList['image'] }}" alt="" width="60" height="60"/></div> <!--END OF DR IMAGE-->
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
     
<?php } } ?> 