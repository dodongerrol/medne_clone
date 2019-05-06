<?php
class EclaimHelper
{
	
	public static function checkPendingEclaims($user_ids, $type)
  {
    $amount = DB::table('e_claim')
              ->whereIn('user_id', $user_ids)
              ->where('status', 0)
              ->where('spending_type', $type)
              ->sum('amount');

    return $amount;
  }

  public static function getCurrencies( )
  {
    $data = array(
      array(
        'currency_name'   => "SGD - Singapore Dollar",
        'currency_exchange_rate'  => 0,
      ),
      array(
        'currency_name'   => "MYR - Malaysian Ringgit",
        'currency_exchange_rate'  => 3.00,
      )
    );

    return $data;
  }
}
?>