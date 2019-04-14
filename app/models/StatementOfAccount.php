<?php


class StatementOfAccount extends Eloquent {

	protected $table = 'statement_of_account';
  protected $guarded = ['statement_of_account_id'];
	

  public function updateStatementOfAccount($data, $payment_record_id)
  {
    $check = StatementOfAccount::where('payment_record_id', '=', $payment_record_id)->count();
    if($check == 1) {
      return StatementOfAccount::update($data);
    }
  }
  
  public function getClinicStatement($id)
  {
    return StatementOfAccount::where('payment_record_id', '=', $id)->first();
  }

  public function checkGenerateStatus($id)
  {
    return StatementOfAccount::where('payment_record_id', '=', $id)->where('generate_status', '=', 1)->count();
  }

}
