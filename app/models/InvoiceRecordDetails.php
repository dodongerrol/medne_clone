<?php


class InvoiceRecordDetails extends Eloquent {

	protected $table = 'invoice_record_details';
  	protected $guarded = ['invoice_record_id'];
	

  public function checkTransactionExistence($id)
  {
  	return InvoiceRecordDetails::where('transaction_id', '=', $id)->count();
  }

  public function getTransaction($id)
  {
  	return InvoiceRecordDetails::where('invoice_id', '=', $id)->get();
  }
}
