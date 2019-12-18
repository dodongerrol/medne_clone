<?php
class MemberHelper
{
	public static function getMemberDateTerms($member_id, $term)
	{
		if($term == "current_term") {
			$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('created_at', 'desc')->first();
			if($user_plan_history) {
				$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
				$plan = DB::table('customer_plan')->where('customer_plan_id', $customer_active_plan->plan_id)->first();
				return ['start' => $plan->plan_start, 'end' => PlanHelper::endDate($plan->plan_end)];
			} else {
				return false;
			}
		} else {
			$user_plan_history = DB::table('user_plan_history')->where('user_id', $member_id)->where('type', 'started')->orderBy('user_plan_history_id', 'desc')->skip(1)->take(1)->first();
			if($user_plan_history) {
				$customer_active_plan = DB::table('customer_active_plan')->where('customer_active_plan_id', $user_plan_history->customer_active_plan_id)->first();
				$plan = DB::table('customer_plan')->where('customer_plan_id', $customer_active_plan->plan_id)->first();
				return ['start' => $plan->plan_start, 'end' => PlanHelper::endDate($plan->plan_end)];
			} else {
				return false;
			}
		}
	}
}
?>