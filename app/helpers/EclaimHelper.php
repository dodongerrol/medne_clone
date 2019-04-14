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
}
?>