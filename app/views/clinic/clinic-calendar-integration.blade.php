@include('common.header-clinic-section')
        <!--END HEADER-->

<div class="clear"></div>
<script type="text/javascript">
    $(function () {
        $("#date-holiday").datepicker({
            // dateFormat: "DD, d MM, yy"
            dateFormat: "dd-mm-yy"
        }).datepicker("setDate", "0");
//$( "#datepicker2" ).datepicker({dateFormat: 'DD, d MM, yy',});

    });
</script>
<?php //echo '<pre>'; print_r($loadarrays);echo '</pre>';  ?>

<div id="clinic-form-container">
    <!--START OF FORM NAV-->

    @include('clinic.clinic-nav-section')

    <!--END OF FORM NAV-->


    <div>
        <?php if($loadarrays['currentdoctor'] != null){ ?>
        @include('ajax.clinic.load-doctor-availability')
        <?php }else{ ?>

        <div class="form-nav-page">
            <div class="dr-option-nav">
                <ul>
                    {{--<li><a href="{{URL::to('app/clinic/clinic-doctors-view')}}">VIEW DOCTORS</a></li>--}}
                    <li><a href="{{URL::to('app/clinic/doctor-availability')}}" class="active">CREDENTIALS</a></li>
                    {{--<li><a href="{{URL::to('app/clinic/clinic-doctor')}}" >ADD DOCTOR</a></li>--}}
                </ul>
            </div> <!--END OF DR OPTION NAV-->

            <div class="clear"></div>

            <div class="search-bar">
                <div class="field-name">
                    <div class="dr-available-profile-image fl">
                        <img src="{{ URL::asset('assets/images/sample2.png') }}" width="60" height="60" alt=""/>
                    </div><!--END OF DR AVAILABL PROFILE IMAGE-->
                </div><!--END OF FIELD NAME-->

                    <div class="field-container-dr-available">
                        <div class="field-name">
                            <label class="label-dr-available">Select A Doctor</label>
                        </div><!--END OF FIELD NAME-->
                        <div class="clear"></div>
                        <div class="field-type">
                            <div class="select-box-v2">
                                <select id="load-doctors-credentials" name="procedure">
                                    <option value="">Select</option>
                                    <?php if ($loadarrays['doctors']) {
                                        foreach ($loadarrays['doctors'] as $doctor) {
                                            echo '<option value="' . $doctor->DoctorID . '">' . $doctor->DocName . '</option>';
                                        }
                                    }?>
                                </select>
                            </div>
                            <!--<input class="label-dr-available-input fl" type="text">-->
                            <label class="label-dr-available fl mar-left-2 mar-t2 font-type-oxygen l-gray S12"></label>
                            <label class="label-dr-available fl mar-t2 font-type-oxygen d-gray S2 padding-top-2 mar-left-3"></label>
                        </div><!--END OF FIELD TYPE-->
                        <br><span id="sync_msg"></span>
                    </div><!--END OF FIELD CONTAINER DR AVAILABLE-->
                    <div class="clear"></div>

                    <div class="col-sm-offset-1">
                        <div class="field-name">
                            <label class="label-dr-available">Gmail</label>
                        </div><!--END OF FIELD NAME-->
                        <div class="clear"></div>
                        <div class="field-type">
                            <input id="doctor-gmail" name="" type="text" class=""  placeholder="Gmail Address">
                        </div><!--END OF FIELD TYPE-->
                        <br><span id="alert"></span>
                    </div><!--END OF FIELD CONTAINER-->
                    <div class="clear"></div>
                <div class="field-container mar-top">
                <div class="col-lg-5">
                    <button class="btn-update font-type-Montserrat" id="btn-sync-credentials"> Send Request</div>
                    </butt0n>
                    <div class="col-lg-5">
                        <button class="btn-update font-type-Montserrat" id="btn-remove-credentials"> Remove Credentials</div>
                    </button>
                <div class="clear"></div>
                </div>
                </div><!--END OF SEARCH BAR-->

            <div class="clear"></div>


        </div><!--END OF FORM NAV PAGE--> <?php } ?>
                                 
    <div class="clear"></div>


</div><!--END OF CLINIC FORM CONTAINER-->


@include('common.footer-clinic-section')