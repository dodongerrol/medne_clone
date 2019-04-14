<?php

class Eclaim extends Eloquent 
{

	protected $table = 'e_claim';
  protected $guarded = ['e_claim_id'];

  public function createEclaim($data)
  {
  	return Eclaim::create($data);
  }

  public function updateEclaimStatus($id, $status, $reason)
  {
    if($status == 1 || $status == "1") {
      return Eclaim::where('e_claim_id', $id)->update(['status' => $status, 'approved_date' => date('Y-m-d H:i:s'), 'rejected_reason' => $reason]);
    } else {
  	 return Eclaim::where('e_claim_id', $id)->update(['status' => $status, 'rejected_reason' => $reason]); 
    }
  }
}
