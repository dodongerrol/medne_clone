<?php


class InvoiceRecord extends Eloquent {

	protected $table = 'invoice_record';
  	protected $guarded = ['invoice_id'];
	

  public function checkInvoice($data)
  {
  	$start_date = date('Y-m-01', strtotime($data['start_date']));
    $id = $data['clinic_id'];
  	return InvoiceRecord::where('start_date', $start_date)
  						// ->where('end_date', '=', $end_date)
  						->where('clinic_id', $id)
  						->first();
  }

  public function checkStatementInvoice($start, $id)
  {
    $start_date = date('Y-m-01', strtotime('-1 month', strtotime($start)));
    return InvoiceRecord::where('start_date', $start_date)
              ->where('clinic_id', $id)
              ->first();
  }

  public function getInvoiceByDate($start, $id)
  {
    return InvoiceRecord::where('start_date', '=', $start)
              ->where('clinic_id', '=', $id)
              ->first();
  }

  public function getInvoiceClinic($id)
  {
    return InvoiceRecord::where('invoice_id', '=', $id)->first();
  }

  public function createInvoice($data)
  {
  	$create = array(
  		'start_date' 	=> date('Y-m-d', strtotime($data['start_date'])),
    	'end_date'		=> date('Y-m-d', strtotime($data['end_date'])),
    	'clinic_id'		=> $data['clinic_id']
  	);
    return InvoiceRecord::create($create);
  }

  public function insertOrUpdate($data, $invoice_id, $clinic_id)
  {
  	$invoice_record_details = new InvoiceRecordDetails();
  	foreach ($data as $key => $value) {
  		$result = $invoice_record_details->checkTransactionExistence($value->transaction_id);
  		if($result == 0) {
  			InvoiceRecordDetails::create(['transaction_id' => $value->transaction_id, 'invoice_id' => $invoice_id]);
  		}

  		// if($key + 1 == sizeof($data)) {
  		// 	return 1;
  		// }

  	}
  }
}
