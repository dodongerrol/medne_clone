
@foreach($result as $key => $value)
<tr style="width: 100%;display: table;table-layout: fixed;">
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

      @if((int)$value->credit_cost == 0)
      <td class="text-center">0</td>
      <td class="text-center">0</td>
      @elseif((int)$value->credit_cost > 0)
      <td class="text-center">{{ round((int)$value->procedure_cost * $value->medi_percent,2) }}</td>
      <?php 
            $sub = (int)$value->procedure_cost * $value->medi_percent;
      ?>
       <td class="text-center">{{ (int)$value->procedure_cost - $sub }}</td>
      @endif
      <?php
            if($value->paid_medi == 1) {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Paid';
                  } else {
                        $status = '';
                  }
            } else {
                  if((int)$value->credit_cost > 0) {
                        $status = 'Not Paid';
                  } else {
                        $status = '';
                  }
            }
      ?>
      <td>{{ $status }}</td>
</tr>
@endforeach