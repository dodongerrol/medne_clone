<?php


class PartnerDetails extends Eloquent {

	  protected $table = 'payment_partner_details';
    protected $guarded = ['payment_partner_details_id'];
	

    public function getBankDetails($id)
    {
        return PartnerDetails::where('partner_id', '=', $id)->first();
    }

    public function checkExistence($id)
    {
        return PartnerDetails::where('partner_id', '=', $id)->count();
    }

   public function createBankDetails($data)
   {
        return PartnerDetails::create($data);
   }

   public function updateBankDetails($data, $id)
   {
        return PartnerDetails::where('partner_id', '=', $id)->update($data);
   }

   public function getCompanyName($id)
   {
      $clinicData = PartnerDetails::where('partner_id', '=', $id)->first();
      $data = explode(' ', $clinicData->bank_name);
      return $data;
   }
}
