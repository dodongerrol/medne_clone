<?php

class CorporateInvoice extends Eloquent 
{

	protected $table = 'corporate_invoice';
  protected $guarded = ['corporate_invoice_id'];

  public function createCorporateInvoice($data)
  {
  	return CorporateInvoice::create($data);
  }

  public function checkCorporateInvoiceActivePlan($id) 
  {
  	return CorporateInvoice::where('customer_active_plan_id', $id)->count();
  }

   public function getCorporateInvoiceActivePlan($id) 
  {
  	return CorporateInvoice::where('customer_active_plan_id', $id)->first();
  }

  public function updateCorporateInvoice($id, $data)
  {
    return CorporateInvoice::where('corporate_invoice_id', $id)->update($data);
  }
}
