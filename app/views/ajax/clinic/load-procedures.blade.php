<?php if(!empty($procedures)){ ?>
        <div class="procedures-chart mar-bottom">   
            <div class="dr-view-title">
               <div class="title-a">Name</div> <!--END OF TITLE-->
               <div class="title-b">Duration</div> <!--END OF TITLE-->
               <div class="title-c">Price</div> <!--END OF TITLE-->
               <div class="clear"></div> <!--END OF CLEAR-->
             </div><!--END OF DR VIEW TITLE-->
            
            <?php foreach($procedures as $procedure){ ?>
                <div class="dr-detail-inner">
                   <label class="label-dr-name"><?php echo $procedure->Name;?></label>
                    <label class="label-dr-duration "><?php echo $procedure->Duration." ".$procedure->Duration_Format;?></label>
                     <label class="label-dr-Price"><?php echo $procedure->Price;?></label>
                     <div procedure_id="<?php echo $procedure->ProcedureID; ?>" id="delete-procedures" class="icn-close"><img width="19" height="20" alt="" src="{{ URL::asset('assets/images/icn-close-dark.png') }}"></div> <!--END OF DR ICN CLOSE-->
                     <div class="clear"></div>
                </div><!--END OF DR DETAIL INNER-->
            <?php } ?>   
         <div class="clear"></div>       
        </div>     
       
 <?php  }else{ ?>
   <div class="procedures-chart new-pd3 mar-bottom">
       <h5 class="pro-banner">Please add your first procedure by filling the above fields and click Add Now.</h5>
       <div class="clear"></div> 
   </div>
 <?php  }?>