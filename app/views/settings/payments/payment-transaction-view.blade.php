@foreach($results as $key => $value)
<tr style="width: 100%;display: table;table-layout: fixed;">
      <?php
        if(strrpos($value->clinic_discount, '%')) {
            $percentage = chop($value->clinic_discount, '%');
            $discount = $percentage / 100 + $value->medi_percent;
        } else {
            $discount_clinic = str_replace('$', '', $value->clinic_discount);
            $discount = round($discount_clinic / 100, 1) + $value->medi_percent;
        }
        $credit = $discount + $value->credit_cost;
        if($value->credit_cost == 0 || $value->credit_cost == null) {
              $credit_cost = 0;
        } else {
              $credit_cost = $value->credit_cost;
        }
      ?>
      <td class="text-center">{{ date('M d Y', strtotime($value->date_of_transaction)) }}</td>
      <td class="text-center">{{ ucwords($value->Name) }}</td>
      <td class="text-center">{{ isset($value->doctor_name) ? ucwords($value->doctor_name) : ''}}</td>
      <td class="text-center">{{ ucwords($value->clinic_procedure_name) }}</td>
      <td class="text-center">{{ isset($value->Created_on) ? date('M d Y', $value->Created_on) : date('M d Y', strtotime($value->date_of_transaction)) }}</td>
      <td class="text-center">{{ isset($value->BookDate) ? date('M d Y', $value->BookDate) : date('M d Y', strtotime($value->date_of_transaction)) }}</td>
      <td class="text-center">${{ $value->procedure_cost }} </td>
      <td class="text-center">${{ $value->procedure_cost - (int)$value->credit_cost }}</td>
      <td class="text-center">{{ $value->credit_cost }}</td>
      <td class="text-center">{{ $credit_cost }}</td>
</tr>
@endforeach