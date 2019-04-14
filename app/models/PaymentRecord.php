<?php


class PaymentRecord extends Eloquent {

	protected $table = 'payment_record';
  protected $guarded = ['payment_record_id'];
	

  public function insertOrGet($id, $clinic_id)
  {
    $check = PaymentRecord::where('invoice_id', '=', $id)->get();
    if(sizeof($check) == 0) {
      $clinic = new Clinic();
      $check_number = PaymentRecord::where('clinic_id', '=', $clinic_id)->count();
      $getClinic = $clinic->getClinicName($clinic_id);
      // return $getClinic;
      $number = str_pad($check_number + 1, 5, "0", STR_PAD_LEFT);
      $invoice_number = 'MN'.strtoupper(substr($getClinic[0], 0, 2)).$number;
      // return $invoice_number;
      $data = array(
        'invoice_id'      => $id, 
        'amount_paid'     => null,
        'remarks'         => null,
        'payment_date'    => null,
        'clinic_id'       => $clinic_id, 
        'invoice_number'  => $invoice_number
      );
      $result = PaymentRecord::create($data);
      return $result->id;
    } else {
      $result = PaymentRecord::where('invoice_id', '=', $id)->first();
      return $result->payment_record_id;
    }
  }

  public function getPaymentRecordClinic($id)
  {
    return PaymentRecord::where('invoice_id', '=', $id)->first();
  }

  public function getPaymentRecord($id)
  {
    return PaymentRecord::where('payment_record_id', '=', $id)->first();
  }

  public function getPaymentRecordList( )
  {
    return PaymentRecord::get();
  }

  public function updatePaymentRecord($id, $data)
  {
    return PaymentRecord::where('payment_record_id', '=', $id)->update($data);
  }

  public function getPaymentRecordListClinict($id)
  {
    return PaymentRecord::where('clinic_id','=', $id)->get();
  }

  public function getPaymentRecordByInvoiceListClinic($id)
  {
    return PaymentRecord::where('invoice_id', $id)->get();
  }

  public function getPaymentRecordByDate($date)
  {
    return PaymentRecord::where('payment_date','=', $date)->where('status', '=', 1)->get();
  }
}
