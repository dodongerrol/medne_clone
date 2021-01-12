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
		$last_term = CustomerHelper::getCustomerLastTerm($user_id);

		return ['status' => true, 'current_term' => $current_term, 'last_term' => $last_term];
	}

	public function updateCompanyHrDetails (Request $request)
	{
		if(empty($request->get('customer_id')) || $request->get('customer_id') == null) {
			return array('status' => false, 'message' => 'Customer ID is required.');
		}

		if(empty($request->get('customer_business_contact_id')) || $request->get('customer_business_contact_id') == null) {
			return array('status' => false, 'message' => 'Customer Business Contact ID is required.');
		}

		if(empty($request->get('first_name')) || $request->get('first_name') == null) {
			return array('status' => false, 'message' => 'Customer Business Contact First Name is required.');
		}

		$check = DB::table('customer_buy_start')->where('customer_buy_start_id', $request->get('customer_id'))->first();
		
		if(!$check) {
			return array('status' => false, 'message' => 'Company does not exist.');
		}

		$data = array(
			'first_name'				=> $request->get('first_name'),
			'last_name'					=> $request->get('last_name'),
			'billing_email'				=> $request->get('work_email'),
			'phone'						=> !empty($request->get('phone')) ? $request->get('phone') : null,
			'updated_at'				=> date('Y-m-d H:i:s')
		);

		if(!empty($request->get('billing_name')) || $request->get('billing_name') != null) {
			$data['billing_name'] = $request->get('billing_name');
		}

		$result = DB::table('customer_billing_contact')
		->where('customer_billing_contact_id', $request->get('customer_billing_contact_id'))
		->update($data);

		if($result) {
			$admin_id = \AdminHelper::getAdminID();
			if($admin_id) {
				$admin_logs = array(
					'admin_id'  => $admin_id,
					'type'      => 'admin_updated_company_billing_contact_details',
					'data'      => \AdminHelper::serializeData($data)
				);
				\AdminHelper::createAdminLog($admin_logs);
			}
			return array('status' => true, 'message' => 'Company Billing Contact Details updated.');
		} else {
			return array('status' => false, 'message' => 'Failed to update Company Billing Contact Details.');
		}
	}

	public function resendCorporateActivationEmail ( )
    {
		$input = Input::all();

		if(empty($input['id']) || $input['id'] == null) {
			return ['status' => false, 'message' => 'id is required.'];
		}

        $message = [];
        $emailData = [];
        $id = (int)$input['id'];
        $user = DB::table('user')
        ->where('UserID', $id)
		->first();

		if(!$user) {
			return array('status' => FALSE, 'message' => 'Member does not exist.');
		}

        if(url('/') == 'https://admin.medicloud.sg') {
            $url = 'https://medicloud.sg/company-benefits-dashboard';
        } else if(url('/') == 'http://stage.medicloud.sg') {
            $url = 'http://staging.medicloud.sg/company-benefits-dashboard';
        } else {
            $url = 'http://medicloud.local/company-benefits-dashboard';
        }

        if((int)$user->member_activated == 0) {
            $emailDdata['emailSubject'] = 'WELCOME TO MEDNEFITS CARE';
            $emailDdata['emailTo']= $user->Email;
            $emailDdata['emailName'] = ucwords($user->Name);
            $emailDdata['emailPage'] = 'email-templates.newAccountLogin.member-activation-email';
            $emailDdata['url'] = $url;
            $emailDdata['code'] = $user->PhoneCode;
            $emailDdata['phone'] = $user->PhoneNo;
            $emailDdata['button'] = $url.'/company-benefits-dashboard-login';
            
            \EmailHelper::sendEmail($emailDdata);
			return array('status' => TRUE, 'message' => 'Successfully resend activation email.');         
		} else {
			return array('status' => FALSE, 'message' => 'Member already Activated');
		}
	}
}

