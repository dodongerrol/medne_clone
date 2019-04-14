<script type="text/javascript">
  $(function( ){
    $.toast({
      heading: 'Result:',
      text: <?php echo sizeof($myloadArray) ?> + ' Results Found.',
      showHideTransition: 'slide',
      icon: 'info',
      hideAfter : 5000,
      bgColor : '#1667AC'
    });
  });
</script>
<table class="table table-striped table-bordered table-responsive" cellspacing="0">
<tr>
  <td>UserID</td>
  <td>UserType</td>
  <td>ClinicID</td>
  <td>Name</td>
  <td>Password</td>
  <td>Email</td>
  <td>DOB</td>
  <td>Age</td>
  <td>Image</td>
  <td>Time Slot Duration</td>
  <td>NRIC</td>
  <td>FIN</td>
  <td>Phone Code</td>
  <td>Phone No.</td>
  <td>OTP Code</td>
  <td>OTP Status</td>
  <td>Bmi</td>
  <td>Weight</td>
  <td>Height</td>
  <td>Blood Type</td>
  <td>Insurance Company</td>
  <td>Insurance Policy No.</td>
  <td>Lat</td>
  <td>Lng</td>
  <td>Address</td>
  <td>Country</td>
  <td>City</td>
  <td>State</td>
  <td>Zip Code</td>
  <td>Created At</td>
  <td>Updated At</td>
  <td>Ref_ID</td>
  <td>Active Link</td>
  <td>Status</td>
  <td>Reset Link</td>
  <td>Recon</td>
  <td>Source Type</td>
</tr>
<?php foreach($myloadArray as $loadArray){

    if($loadArray->source_type == 0) {
      $source_type = '';
    } else if($loadArray->source_type == 1) {
      $source_type = 'Web';
    } else if($loadArray->source_type == 2) {
      $source_type = 'Mobile';
    } else if($loadArray->source_type == 3) {
      $source_type = 'Widget';
    }

    if($loadArray->Status==0){
        $Status = "Active";
    }elseif($loadArray->Status==1){
        $Status = "Processing";
    }elseif($loadArray->Status==2){
        $Status = "Concluded";
    }elseif($loadArray->Status==3){
        $Status = "Cancelled";
    }

?>
<tr>
    <td>{{$loadArray->UserID}}</td>
    <td>{{$loadArray->UserType}}</td>
    <td>{{$loadArray->ClinicID}}</td>
    <td>{{$loadArray->Name}}</td>
    <td>{{$loadArray->Password}}</td>
    <td>{{$loadArray->Email}}</td>
    <td>{{$loadArray->DOB}}</td>
    <td>{{$loadArray->Age}}</td>
    <td>{{$loadArray->Image}}</td>
    <td>{{$loadArray->TimeSlotDuration}}</td>
    <td>{{$loadArray->NRIC}}</td>
    <td>{{$loadArray->FIN}}</td>
    <td>{{$loadArray->PhoneCode}}</td>
    <td>{{$loadArray->PhoneNo}}</td>
    <td>{{$loadArray->OTPCode}}</td>
    <td>{{$loadArray->OTPStatus}}</td>
    <td>{{$loadArray->Bmi}}</td>
    <td>{{$loadArray->Weight}}</td>
    <td>{{$loadArray->Height}}</td>
    <td>{{$loadArray->Blood_Type}}</td>
    <td>{{$loadArray->Insurance_Company}}</td>
    <td>{{$loadArray->Insurance_Policy_No}}</td>
    <td>{{$loadArray->Lat}}</td>
    <td>{{$loadArray->Lng}}</td>
    <td>{{$loadArray->Address}}</td>
    <td>{{$loadArray->Country}}</td>
    <td>{{$loadArray->City}}</td>
    <td>{{$loadArray->State}}</td>
    <td>{{$loadArray->Zip_Code}}</td>
    <td>{{$loadArray->created_at}}</td>
    <td>{{$loadArray->updated_at}}</td>
    <td>{{$loadArray->Ref_ID}}</td>
    <td>{{$loadArray->ActiveLink}}</td>
    <td>{{$Status}}</td>
    <td>{{$loadArray->ResetLink}}</td>
    <td>{{$loadArray->Recon}}</td>
    <td>{{$source_type}}</td>
</tr>
<?php } ?>
</table>
