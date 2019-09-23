<?php
use Illuminate\Support\Facades\Input;

class EmployeeController extends \BaseController {
	public function updateCapPerVisitEmployee( )
	{
		$input = Input::all();

		$result = StringHelper::getJwtHrSession();
		// get admin session from mednefits admin login
		$admin_id = Session::get('admin-session-id');
		$hr_id = $result->hr_dashboard_id;
		
		if(empty($input['employee_id']) || $input['employee_id'] == null) {
			return array('status' => false, 'message' => 'Employee ID is required.');
		}

		$cap = array(
			'cap_per_visit_medical'		=> $input['cap_amount'],
			'cap_per_visit_wellness'	=> $input['cap_amount'],
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		DB::table('e_wallet')->where('UserID', $input['employee_id'])->update($cap);
		$cap['employee_id'] = $input['employee_id'];
		if($admin_id) {
			$admin_logs = array(
                'admin_id'  => $admin_id,
                'admin_type' => 'mednefits',
                'type'      => 'admin_updated_cap_per_visit_cap',
                'data'      => SystemLogLibrary::serializeData($cap)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		} else {
			$admin_logs = array(
                'admin_id'  => $hr_id,
                'admin_type' => 'hr',
                'type'      => 'admin_updated_cap_per_visit_cap',
                'data'      => SystemLogLibrary::serializeData($cap)
            );
            SystemLogLibrary::createAdminLog($admin_logs);
		}

		return array('status' => true, 'message' => 'Cap updated.');
	}
}
