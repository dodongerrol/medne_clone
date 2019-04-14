<?php

class PlanTier extends Eloquent 
{

	protected $table = 'plan_tiers';
    protected $guarded = ['plan_tier_id'];

    public function increamentMemberEnrolledHeadCount($plan_tier_id)
    {
    	return PlanTier::where('plan_tier_id', $plan_tier_id)->increment('member_enrolled_count', 1);
    }

     public function increamentDependentEnrolledHeadCount($plan_tier_id)
    {
    	return PlanTier::where('plan_tier_id', $plan_tier_id)->increment('dependent_enrolled_count', 1);
    }

     public function decrementDependentEnrolledHeadCount($plan_tier_id)
    {
        return PlanTier::where('plan_tier_id', $plan_tier_id)->decrement('dependent_enrolled_count', 1);
    }
}
