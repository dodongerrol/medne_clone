<?php
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class CorporateController extends BaseController {

	function __construct()
	{
		
    }

	public function index( )
	{	
		$data['title'] = 'Corporate';
		return View::make('admin.corporate', $data);
	}

	public function createCorporate( )
	{
		$corporate_data = new Corporate();
		$input = Input::all();

		$check_email = $corporate_data->checkEmail($input['email']);
		if($check_email > 0) {
			return array(
				'status'	=> 400,
				'message'	=> 'Email Already Taken.'
			);
		}

		$check_identification = $corporate_data->checkIdentification($input['identification_number']);

		if($check_identification > 0) {
			return array(
				'status'	=> 400,
				'message'	=> 'Identifcation Number Already Taken.'
			);
		}

		$user = new User();
		// return $input;
		// return $input;
		$password = StringHelper::get_random_password(8);
		// $user_id = StringHelper::get_random_password(8).time();
		$name = $input['fname'].' '.$input['lname'];
		$user_id = $user->createUserFromCorporate($input['email'], $input['phone'], $input['code'], $name, $password, $input['credit']);

        $corporate = array(
        	'UserID'			=> $user_id,
        	'first_name'		=> $input['fname'],
        	'last_name'			=> $input['lname'],
        	'email'				=> $input['email'],
        	'credit'			=> $input['credit'],
        	'company_name'		=> $input['company_name'],
        	'identification_number'		=> $input['identification_number'],
        	'password'			=> StringHelper::encode($password),
        	'created_at'		=> Carbon::now(),
        	'updated_at'		=> Carbon::now()
        );
        // return $corporate;
        $result = $corporate_data->createCorporate($corporate);
        // return var_dump($result);
        if( $result ) {
	        $emailDdata['emailName']= 'New Medicloud Corporate Account';
	        $emailDdata['emailPage']= 'email-templates.welcome-corporate';
	        $emailDdata['emailTo']= $input['email'];
	        $emailDdata['name']= $input['fname'].' '.$input['fname'];
	        $emailDdata['credit']= $input['credit'];
	        $emailDdata['emailSubject'] = "Welcome To Mednefits";
	        $emailDdata['data'] = $result;
	        $emailDdata['pw'] = $password;
	        $emailDdata['user_id'] = $user_id;
	        return EmailHelper::sendEmail($emailDdata);
        } else {
        	return "false";
        }
	}

	public function getListCorporate( )
	{
		$corporate_data = new Corporate();
		$data['result'] = $corporate_data->getListCorporate( );
		return View::make('admin.corporate-list', $data);
	}

	public function updateCorporate( ) 
	{
		$corporate_data = new Corporate();
		$input = Input::all();
		$corporate = array(
        	'first_name'		=> $input['fname'],
        	'last_name'			=> $input['lname'],
        	'email'				=> $input['email'],
        	'credit'			=> $input['credit'],
        	'company_name'		=> $input['company_name'],
        	'updated_at'		=> Carbon::now()
        );

		$result = $corporate_data->updateCorporate($corporate, $input['id']);
		if($result) {
			$wallet = new Wallet();
			$id = $corporate_data->findUserID($input['id']);
			return $wallet->updateWallet($id->UserID, $input['credit']);
		}
	}

	public function activateAccount($id) 
	{	
		// return $id;
		$corporate_data = new Corporate();
		$result['result'] = $corporate_data->activateAccount($id);
		if($result != "false") {
			return View::make('email-templates.corporate_confirmation', $result);
		}
	}

	public function searchCoporate( )
	{
		$input = Input::all();
		$corporate_data = new Corporate();
		return $corporate_data->searchCoporate($input['search']);
	}

	public function getCorporateById($id)
	{
		$corporate_data = new Corporate();
		return json_encode($corporate_data->getCorporateById($id));
	}

	public function getDoctors( )
	{
		// $input = Input::all();
		// return $input;
		$doctors = new CalendarController();
		$getSessionData = StringHelper::getMainSession(3);
		// $getSessionData->Ref_ID
		return $doctors->getClinicDoctors($getSessionData->Ref_ID);
	}
	public function allCorporate( ) 
	{
		$corporate_data = new Corporate();
		return $corporate_data->allCorporate();
	}

	public function getDoctorProcedure($id)
	{
		$procedure = new CalendarController();
		$getSessionData = StringHelper::getMainSession(3);
		return $procedure->loadDoctorProcedures($getSessionData->Ref_ID, $id);
	}

	// corporate members
	public function addCorporateMembers( )
	{
		$input = Input::all();
	}

	public function getCompanyDateTerms( )
	{
    $input = Input::all();
    $result = StringHelper::getJwtHrSession();
    $user_id = $result->customer_buy_start_id;
    $current_term = CustomerHelper::getCustomerDateTerms($user_id, 'current_term', 'medical');
    $last_term = CustomerHelper::getCustomerDateTerms($user_id, 'last_term', 'medical');

    return ['status' => true, 'current_term' => $current_term, 'last_term' => $last_term];
	}

	public function createCompanyPasswordActivated ( )
	{
		$input = Input::all();
		$hr_data = StringHelper::getJwtHrSession();
		$hr_id = $hr_data->hr_dashboard_id;

		$hr = DB::table('customer_hr_dashboard')
		->where('customer_buy_start_id', $$hr->hr_dashboard_id)
		->first();

		if($hr->active == "1") {
			$data = array(
				'password'	=> md5($input['new_password']),
				'confirm_password' => md5($input['confirm_password']),
				'reset_link' => NULL
			);
		return array ('status' => TRUE, 'message' => 'Successfully created company password.', $hr_data);
		} else {
			return array ('status' => FALSE, 'message' => 'Member not yet activated.');
		}


	}
}
