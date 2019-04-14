<?php
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
class TopUpPassController extends BaseController {


	public function index( )
	{
		$data['title'] = 'Credit Top Up';
		return View::make('admin.credit-top-up', $data);
	}
	public function createPassword( )
	{
		$credit = new CreditPassword();

		$input = Input::all();
		$password = array(
			'password' 		=> 	$input['password'],
			'created_at'	=>	Carbon::now(),
			'updated_at'	=>  Carbon::now()
		);

		return $credit->savePassword($password);
	}

	public function checkPass( )
	{
		$credit = new CreditPassword();
		return $credit->checkPass();
	}

	public function createUser( )
	{
		$allInputs = Input::all( );
		$wallet = new Wallet( );
		$pw = StringHelper::get_random_password(8);

		if($allInputs['wallet_id'] == 0) {
			$user = new User( );
			$checkEmail = $user->checkUserExist($allInputs['email']);

			if($checkEmail == 1) {
				return 3;
			}


	        $userData['name'] = $allInputs['name'];
	        $userData['usertype'] = 1;
	        $userData['email'] = $allInputs['email'];
	        $userData['nric'] = '';
	        $userData['code'] = '';
	        $userData['mobile'] = '';

	        $userData['address'] = '';
	        $userData['city'] = '';
	        $userData['state'] = '';
	        $userData['zip'] = '';

	        $userData['ref_id'] = 0;
	        $userData['activelink'] = null;
	        $userData['status'] = 0;
	        $userData['source'] = 1;
	        $userData['pw'] = StringHelper::encode($pw);

	        $newuser = Auth_Library::AddNewUser($userData);
	        if($newuser) {

	        	// $data = array(
	        		// 'UserID'		=> $newuser,
	        		// 'balance' 		=> $allInputs['credit'],
	        		// 'created_at'	=> Carbon::now(),
	        		// 'updated_at'	=> Carbon::now()
	        	// );
	        	// $result = $wallet->createWallet($data);
	        	$wallet_id = $wallet->getWalletId($newuser);
	        	$result = $wallet->updateWalletByWalletId($wallet_id, $allInputs['credit']);
	        	if($result) {
			        $emailDdata['emailPage']= 'email-templates.welcome-user';
		            $emailDdata['emailTo']= $allInputs['email'];
		            $emailDdata['pw']= $pw;
		            $emailDdata['emailName']= $allInputs['name'];
		            $emailDdata['emailSubject'] = 'Thank you for registering with us';
		            $emailDdata['link']	='https://medicloud.sg/app/activate/user/'.$newuser;
		            EmailHelper::sendEmail($emailDdata);
		            return 1;
	        	}
	        }
		} else {
			// return $allInputs['wallet_id'];
			$result = $wallet->updateWalletByWalletId($allInputs['wallet_id'], $allInputs['credit']);
			if($result) {
				$data = array(
					'wallet_id'		=> $allInputs['wallet_id'],
					'credit'		=> $allInputs['credit'],
					'logs'			=> 'top_up',
					'created_at'	=> Carbon::now(),
	        		'updated_at'	=> Carbon::now()
				);
				$wallet_history = new WalletHistory( );
				$history_result = $wallet_history->createWalletHistory($data);
				if($history_result) {
					return 1;
				} else {
					return 0;
				}

			}
		}

	}

	public function users( )
	{
		$users = new User( );

		return $users->getUsers( );
	}

	public function updateAllWithWallet( )
	{
		$users = new User( );
		return $users->updateAllWithWallet();
	}

	public function activateUserAccount($id) {
		$hostName = $_SERVER['HTTP_HOST'];
        $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$user = new User( );
		$data['user'] = $user->getUserProfile($id);
		$data['title'] = 'User Activate Account';
		return View::make('auth.activate-account', $data);
	}

	public function updateUserAccountActive( )
	{
		$hostName = $_SERVER['HTTP_HOST'];
	    $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
	    $data['server'] = $protocol.$hostName;
	    $data['date'] = new DateTime();
		$user = new User( );
		$input = Input::all();
		$phone_code = explode(" ", $input['phone_code']);
		// return $phone_code;
		// $phone_no = "+".$phone_code[1].$input['phone_number'];
		// return $phone_no;
		// $new_phone_no = preg_replace('/\s+/', '', $phone_no);
		// return $new_phone_no;

		$findPlusSign = substr($input['phone_number'], 0, 1);
        if($findPlusSign == 0){
            $PhoneOnly = $input['phone_code'].substr($input['phone_number'], 1);
        }else{
            $PhoneOnly = $input['phone_code'].$input['phone_number'];
        }
        $new_phone_no = preg_replace('/\s+/', '', "+".$PhoneOnly);
		$data = array(
			'Name' 			=> $input['name'],
			'NRIC'			=> $input['nric'],
			'PhoneCode'		=> "+".$phone_code[1],
			'PhoneNo'		=> $new_phone_no,
			'Email'			=> $input['email'],
			'userid'		=> $input['user']
		);

		$result = $user->updateUser($data);

		if($result == 1) {
			$wallet = new Wallet( );
			return $wallet->updateWalletActive($input['user']);
		}
	}

	public function searchUser( )
	{	
		$user = new User( );
		$input = Input::all();
		return $user->searchUser($input['search']);
	}

	public function promoCodeTopUpGet( )
	{
		$hostName = $_SERVER['HTTP_HOST'];
        $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $data['server'] = $protocol.$hostName;
        $data['date'] = new DateTime();
		$user_promo_code_history = new UserPromoCodeHistory( );
		$data['title'] = 'Promo Code Top Up';
		$data['result'] = $user_promo_code_history->getPromoCodeTopUp();
		return View::make('admin.promo_code_user_list', $data);
	}
}