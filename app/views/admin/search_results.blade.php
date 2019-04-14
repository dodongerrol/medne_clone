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
    <td>BookingID</td>
    <td>UserName</td>
    <td>UserEmail</td>
    <td>NRIC</td>
    <td>Phone</td>
    <td>DoctorName</td>
    <td>ClinicName</td>
    <td>BookDate</td>
    <td>StartTime</td>
    <td>EndTime</td>
    <td>InitialDate</td>
    <td>Status</td>
    <td>BookType</td>
    <td>Credit Balance</td>
</tr>
<?php foreach($myloadArray as $loadArray){
    if($loadArray->event_type==1){
    $booktype = 'Google';
    }elseif($loadArray->event_type==3){
        $booktype = 'Widget';
    }elseif($loadArray->event_type==0 && $loadArray->MediaType==0){
        $booktype = 'Mobile';
    }elseif($loadArray->event_type==0 && $loadArray->MediaType==1){
        $booktype = 'Web';
    }
    if($loadArray->Status==0){
        $bookStatus = "Active";
    }elseif($loadArray->Status==1){
        $bookStatus = "Processing";
    }elseif($loadArray->Status==2){
        $bookStatus = "Concluded";
    }elseif($loadArray->Status==3){
        $bookStatus = "Cancelled";
    }
    $bookdate = date('d-m-Y',$loadArray->BookDate);
    $starttime = date('h:i A',$loadArray->StartTime);
    $endtime = date('h:i A',$loadArray->EndTime);
    $created = date('d-m-Y, h:i A', $loadArray->Created_on);
?>
<tr>
    <td>{{$loadArray->UserAppoinmentID}}</td>
    <td>{{$loadArray->UsrName}}</td>
    <td>{{$loadArray->USEmail}}</td>
    <td>{{$loadArray->USNRIC}}</td>
    <td>{{$loadArray->USPhone}}</td>
    <td>{{$loadArray->DocName}}</td>
    <td>{{$loadArray->CLName}}</td>
    <td>{{$bookdate}}</td>
    <td>{{$starttime}}</td>
    <td>{{$endtime}}</td>
    <td>{{$created}}</td>
    <td>{{$bookStatus}}</td>
    <td>{{$booktype}}</td>
    <td>{{(int)$loadArray->current_wallet_amount - (int)$loadArray->credit_cost}}</td>
</tr>
<?php } ?>
</table>
