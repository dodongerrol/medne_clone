
@foreach($result as $key => $value)

<tr>
      <td hidden>{{ $value['payment']->payment_record_id }}</td>
      <td>
            @if((int)$value['payment']->status == 1)
            <div class="status paid">
                  PAID
            </div>
            @else
            <div class="status unpaid" >
                  UNPAID
            </div>
            @endif
      </td>
      @if($value['invoice_record']['start_date'] == null)
        <td></td>
      @endif
      @if($value['invoice_record']['start_date'] != null)
      <td> {{ date( 'M d, Y' , strtotime($value['invoice_record']['start_date'])) }}</td>
      @endif
      <td> {{ $value['payment']->invoice_number }} </td>
      <td> {{ $value['clinic']->Name }} </td>
      <td> ${{ $value['total']}} </td>
      <td> ${{ $value['amount_due']}} </td>
      <td>
          @if((int)$value['payment']->status == 1)
            <a class="view-statement-button" href="javascript:void(0)">View </a>
            <div class="btn-group">
              <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu">
                <li><a href="javascript:void(0)" class="view-statement-button">Export as PDF</a></li>
                <li><a class="invoice-print" href="javascript:void(0)">Print</a></li>
              </ul>
            </div>
          @else
            No Actions
          @endif
      </td>
</tr>

@endforeach