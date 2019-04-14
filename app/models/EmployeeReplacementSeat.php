<?php

class EmployeeReplacementSeat extends Eloquent 
{

	protected $table = 'employee_replacement_seat';
    protected $guarded = ['employee_replacement_seat_id'];

    public function createReplacementSeat($data)
    {
    	return EmployeeReplacementSeat::create($data);
    }

}
