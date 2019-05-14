<?php
use Illuminate\Support\Facades\Input;

class EmployeeController extends \BaseController {
	public function updateCapPerVisitEmployee( )
	{
		$input = Input::all();

		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$cap = array(
			'cap_per_visit_medical'		=> $input['cap_amount'],
			'cap_per_visit_wellness'	=> $input['cap_amount'],
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		DB::table('e_wallet')->where('UserID', $input['employee_id'])->update($cap);
		return array('status' => true, 'message' => 'Cap updated.');
	}
}
