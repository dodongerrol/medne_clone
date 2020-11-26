<?php
class DependentHelper
{

	public static function createDependentPlanHistory($employee_id, $member_id)
	{
		$dependents = DB::table('employee_family_coverage_sub_accounts')
		->join('user', 'user.UserID', '=', 'employee_family_coverage_sub_accounts.user_id')
		->where('employee_family_coverage_sub_accounts.owner_id', $employee_id)
		->where('user.Active', 1)
		->where('employee_family_coverage_sub_accounts.deleted', 0)
		->orderBy('employee_family_coverage_sub_accounts.created_at', 'desc')
		->get();

		$dep = null;
		foreach ($dependents as $key => $dependent) {
			$dependent_plan_history = DB::table('dependent_plan_history')
																->where('user_id', $dependent->user_id)
																->where('type', 'started')
																->orderBy('created_at', 'desc')
																->first();
			if($dependent_plan_history) {
				$dep = $dependent_plan_history;
				break;
			}
		}
		return $dep;
	}

	public static function getEmployeeDependentID($employee_id)
	{
		$dependent_account = DB::table('employee_family_coverage_sub_accounts')
														->where('owner_id', $employee_id)
														->first();

		if($dependent_account) {
			$dependent_plan = DB::table('dependent_plan_history')
													->where('user_id', $dependent_account->user_id)->first();

			if($dependent_plan) {
				\BenefitsPlanHelper::updateDependentPlanStatusCount($dependent_plan->dependent_plan_id);
			}
		}
	}
}
?>