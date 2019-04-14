<?php
      $total_medi = 0;
      $total_clinic = 0;
?>
@foreach($result as $key => $value)
<tr style="width: 100%;display: table;table-layout: fixed;">
      <?php
            if((int)$value->credit_cost > 0) {
                  $transaction_fee = (int)$value->procedure_cost * $value->medi_percent;
                  // $transaction_fee = (int)$value->procedure_cost - $tf;
                  $clinic = (int)$value->credit_cost - $transaction_fee;
                  $total_clinic = $total_clinic + $clinic;
                  $total_revenue = (int)$value->procedure_cost - $transaction_fee;
            } else {
                  $medi = 0;
                  $clinic = 0;
                  $transaction_fee = 0;
                  $total_revenue = $value->procedure_cost;
            }

            if($value->paid_medi != 0) {
                  $status = 'Paid';
            } else {
                  $status = 'Not Paid';
            }
      ?>

      <?php
            if( $status == "Paid" ){
      ?>
            <td style="width: 75px;"> <input style="margin-right: 30px;" type="checkbox" class="checkOneBox" value="{{ $value->transaction_id }}" checked="checked"> </td>
      <?php                  
            }else{
      ?>
            <td style="width: 75px;"> <input style="margin-right: 30px;" type="checkbox" class="checkOneBox" value="{{ $value->transaction_id }}"> </td>
      <?php          
                 
            }
      ?>

      
      <td>{{ ucwords($value->clinic_name) }}</td>
      <td class="text-center">{{ date('M', strtotime($value->updated_at)) }} {{ date('d', strtotime($value->updated_at)) }} {{ date('Y', strtotime($value->updated_at)) }}</td>
      <td class="text-center">{{ ucwords($value->Name) }}</td>
      <td class="text-center">{{ ucwords($value->doctor_name) }}</td>
      <td class="text-center">{{ ucwords($value->clinic_procedure_name) }}</td>
      <td class="text-center">{{ date('M', $value->Created_on) }} {{ date('d', $value->Created_on) }} {{ date('Y', $value->Created_on) }}</td>
      <td class="text-center">{{ date('M', $value->BookDate) }} {{ date('d', $value->BookDate) }} {{ date('Y', $value->BookDate) }}</td>
      <td class="text-center">${{ $value->procedure_cost }} </td>
      <td class="text-center">${{ $value->procedure_cost - (int)$value->credit_cost }}</td>
      <td class="text-center">{{ $value->credit_cost }}</td>
      <td class="text-center">{{ $transaction_fee }}</td>
      <td class="text-center">{{ $clinic }}</td>
      <td class="text-center">{{ $total_revenue }}</td>
      <td class="text-center">{{ $status }}</td>
      <?php
            $medi = 0;
            $clinic = 0;
            $transaction_fee = 0;
            $total_revenue = 0;
      ?>
</tr>
@endforeach
@if(sizeOf($result) != 0)
      @if($filter == 0)
      <tr>
            <td style="text-align: left; width: 200px;">Total Payment to Clinic</td>
            <td style="text-align: left"><b>{{ $total_clinic }}</b></td>
      </tr>
      @endif
      @if($filter == 1)
      <tr>
            <td style="text-align: left; width: 200px;">Total Payment to Clinic</td>
            <td style="text-align: left"><b>{{ $total_clinic }}</b></td>
      </tr>
      @endif
      @if($filter == 2)
      <tr>
            <td style="text-align: left; width: 200px;">Total Payment to Medicloud</td>
            <td style="text-align: left"><b>{{ abs($total_clinic) }}</b></td>
      </tr>
      @endif
      <tr>
            <td style="text-align: left"><b>{{ sizeOf($result) }} Results</b></td>
      </tr>
@endif