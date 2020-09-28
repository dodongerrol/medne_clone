<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Carbon\Carbon;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
    protected $guarded = ['UserID'];
	protected $hidden = array('password', 'remember_token');

        //User login by Mobile app
        public function authLogin ($email, $password){
        	  $users = DB::table('user')
                     ->select('UserID')
                     ->where(function($query) use ($email, $password){
                        $query->where('Email', $email)
                        ->where('Password', StringHelper::encode($password))
                        ->where('Active', 1)
                        ->where('UserType', 5);
                     })
                     ->orWhere(function($query) use ($email, $password){
                        $email = strtoupper($email);
                        $query->where('NRIC', $email)
                        ->where('Password', StringHelper::encode($password))
                        ->where('Active', 1)
                        ->where('UserType', 5);
                     })
                     ->orWhere(function($query) use ($email, $password){
                        $email = (int)($email);
                     	$query->where('PhoneNo', (string)$email)
                        ->where('Password', StringHelper::encode($password))
                        ->where('Active', 1)
                        ->where('UserType', 5);
                     })
                     ->first();

            if($users){
                return $users->UserID;
            }else{
                return false;
            }
        }

        public function newAuthLogin ($email, $password){
            $email = (int)($email);
            $users = DB::table('user')
                     ->select('UserID')
                    ->where('PhoneNo', (string)$email)
                    ->where('Password', StringHelper::encode($password))
                    // ->where('Active', 1)
                    ->where('UserType', 5)
                     ->first();

            if($users){
                // check employee status
                $employee_status = PlanHelper::getEmployeeStatus($users->UserID);
                $today =  PlanHelper::endDate(date('Y-m-d'));
                if($employee_status['status'] == true)  {
                    $expiry = date('Y-m-d', strtotime($employee_status['expiry_date']));
                    $expiry = PlanHelper::endDate($expiry);
                    if($today > $expiry) {
                        return false;
                    }
                }
                return $users->UserID;
            }else{
                return false;
            }
        }

        // create user base in reserver blocker
        public function createUserFromReserve($email, $phone, $code, $name){

                $config = Config::get('config.deployment');
                if($config == "Development") {
                    $this->OTPCode = '123456';
                }

                $this->Name = $name;
                $this->Password = '';
                $this->Email = $email;
                $this->PhoneNo = $phone;
                $this->PhoneCode = $code;
                $this->Lat = '';
                $this->Lng = '';
                $this->NRIC = '';
                $this->FIN = '';
                $this->Image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png';
                $this->Active = 1; //to active user
                $this->created_at = time();
                $this->updated_at = time();
                $this->UserType = 4; // for users
                $this->source_type = 1;
                if($this->save()){
                    $insertedId = $this->id;
                    $wallet = new Wallet( );
                    $data = array(
                        'UserID'        => $insertedId,
                        'balance'       => "0",
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now()
                    );
                    $wallet->createWallet($data);
                    return $insertedId;
                }else{
                    return false;
                }

        }

        public function createUserFromCorporate($data){
                $this->Name = $data['Name'];
                $this->Password = $data['Password'];
                $this->Email = $data['Email'];
                $this->PhoneNo = $data['PhoneNo'];
                $this->PhoneCode = $data['PhoneCode'];
                $this->DOB = $data['DOB'];
                $this->Lat = '';
                $this->Lng = '';
                $this->NRIC = $data['NRIC'];
                $this->passport = $data['passport'];
                $this->Zip_Code = $data['Zip_Code'];
                $this->FIN = '';
                $this->Image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png';
                $this->Active = 1; //to active user
                $this->pending = $data['pending'];
                $this->created_at = time();
                $this->updated_at = time();
                $this->UserType = 5; // for users
                $this->access_type = 0; // for users
                $this->source_type = 1;
                $this->Job_Title = $data['Job_Title'];
                $this->communication_type = !empty($data['communication_type']) ? $data['communication_type'] : "email";
                $this->account_update_status = 1;
                $this->account_update_date = date('Y-m-d H:i:s');
                $this->account_already_update = 1;
                $this->member_activated = 0;
                $this->emp_no = !empty($data['emp_no']) && $data['emp_no'] ? $data['emp_no'] : null;
                $this->bank_name = !empty($data['bank_name']) && $data['bank_name'] ? $data['bank_name'] : null;
                $this->bank_account = !empty($data['bank_account']) && $data['bank_account'] ? $data['bank_account'] : null;
                if($this->save()){
                    $insertedId = $this->id;
                    $wallet = new Wallet( );
                    $data_wallet = array(
                        'UserID'        => $insertedId,
                        'balance'       => 0,
                        'created_at'    => Carbon::now(),
                        'updated_at'    => Carbon::now()
                    );

                    if(isset($data['currency_type']) || !empty($data['currency_type'])) {
                        $data_wallet['currency_type'] = $data['currency_type'];
                    }

                    if(isset($data['cap_per_visit']) || !$data['cap_per_visit']) {
                        $data_wallet['cap_per_visit_medical'] = $data['cap_per_visit'] ? $data['cap_per_visit'] : 0;
                        $data_wallet['cap_per_visit_wellness'] = $data['cap_per_visit'] ? $data['cap_per_visit'] : 0;
                    }
                    $wallet->createWallet($data_wallet);
                    return $insertedId;
                }else{
                    return false;
                }

        }

        public function createUserFromDependent($data){

            $user_data = array(
                'Name'      => $data['Name'],
                'Password'  => '123456',
                'Email'     => $data['Email'],
                'PhoneNo'   => null,
                'PhoneCode' => $data['PhoneCode'],
                'DOB'       => $data['DOB'],
                'Lat'       => '',
                'Lng'       => '',
                'NRIC'      => $data['NRIC'],
                'FIN'       => '',
                'Image'     => 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png',
                'Active'    => 1,
                'created_at'    => time(),
                'updated_at'    => time(),
                'UserType'      => 5,
                'access_type'   => 2,
                'source_type'   => 1,
                'Job_Title'     => null,
                'account_update_status' => 1,
                'account_update_date' => date('Y-m-d H:i:s'),
                'account_already_update' => 1
            );

            $result = User::create($user_data);

            if($result) {
                return $result->id;
            } else {
                return false;
            }
                // $this->Name = $data['Name'];
                // $this->Password = '123456';
                // $this->Email = $data['Email'];
                // $this->PhoneNo = null;
                // $this->PhoneCode = $data['PhoneCode'];
                // $this->Lat = '';
                // $this->Lng = '';
                // $this->NRIC = $data['NRIC'];
                // $this->FIN = '';
                // $this->Image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png';
                // $this->Active = 1; //to active user
                // $this->created_at = time();
                // $this->updated_at = time();
                // $this->UserType = 5; // for employee users
                // $this->access_type = 2; // for dependent users
                // $this->source_type = 1;
                // $this->Job_Title = null;
                // if($this->save()){
                //     $insertedId = $this->id;
                //     return $insertedId;
                // }else{
                //     return false;
                // }

        }

        public function FindUserDetailsFromReserve($userid)
        {
            // return $userid;
            return DB::table('user')
                    ->where('UserID', '=', $userid)
                    // ->where('Active', '=', 1)
                    ->where('UserType', '=', 4)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }


        //User sign up by mobile app
        public function authSignup ($dataArray){

                $config = Config::get('config.deployment');
                if($config == "Development") {
                    $this->OTPCode = '123456';
                }

                $this->Name = $dataArray['name'];
                $this->Password = $dataArray['password'];
                $this->Email = $dataArray['email'];
                $this->PhoneNo = $dataArray['mobile'];
                //$this->Insurance_Company = $dataArray['insurance_company'];
                //$this->Insurance_Policy_No = $dataArray['insurance_policy_no'];
                $this->Lat = $dataArray['latitude'];
                $this->Lng = $dataArray['longitude'];
                $this->NRIC = $dataArray['nric'];
                $this->FIN = $dataArray['fin'];
                $this->Image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png';
                $this->Active = $dataArray['active']; //to active user
                $this->created_at = $dataArray['createdat'];
                $this->updated_at = 0;
                $this->UserType = $dataArray['usertype']; // for users
			    $this->source_type = $dataArray['source'];

                if($this->save()){
                    $insertedId = $this->id;
                    return $insertedId;
                }else{
                    return false;
                }

        }
        /*  Access      :   Public
            Function    :   Update
            Parameter   :   Array
            Author      :   Rizvi
            Return      :   Json
            Updated     :
        */

        public function updateUserProfile ($dataArray){
            $user_id = $dataArray['userid'];
            unset($dataArray['userid']);
            return DB::table('user')
                ->where('UserID', '=', $user_id)
                ->update($dataArray);

            // return $allData;
        }

        public function Forgot_Password($email){
            $users = DB::table('user')
                    ->where('Email', '=', $email)
                    // ->where('Active', '=', 1)
                    ->where('UserType', '!=', 3)
                     ->first();

            if($users){
                return $users->UserID;
            }else{
                return false;
            }
        }

        public function Forgot_Password_Mobile($email){
            $users = DB::table('user')
                    ->where(function($query) use ($email) {
                        $query->where('Email', $email)
                        ->where('Active', 1)
                        ->where('UserType', 5);
                    })
                    ->orWhere(function($query) use ($email){
                        $query->where('PhoneNo', $email)
                        ->where('Active', 1)
                        ->where('UserType', 5);
                    })
                    ->orWhere(function($query) use ($email){
                        $email = (int)$email;
                        $email = (string)$email;
                        $query->where('PhoneNo', $email)
                        ->where('Active', 1)
                        ->where('UserType', 5);
                    })
                    ->first();

            if($users){
                return $users->UserID;
            }else{
                return false;
            }
        }

        public function checkEmailMobile($email){

            $findEmail = DB::table('user')
                        ->where('Email', '=', $email)
                        ->where('UserType', '=', 5)
                        ->first();

            if($findEmail){
                return $findEmail->UserID;
            }else{
                return FALSE;
            }
        }

        public function checkEmail ($email){

            $findEmail = DB::table('user')
                        ->where('Email', '=', $email)
                        // ->where('NRIC', '=', $nric)
                        ->where('Active', '=', 1)
                        ->where('UserType', '=', 1)
                        // ->orWhere('UserType', '=', 5)
                        ->first();

            if($findEmail){
                return $findEmail->UserID;
            }else{
                return FALSE;
            }
        }

        /* use to get all users for selected user type */

        public function getAllUsers($profileType){
            $users = DB::table('user')
                    ->where('UserType', '=', $profileType)
                    ->where('Active', '=', 1)
                    ->get();

            if($users){
                return $users;
            }else{
                return FALSE;
            }
        }
        public function getIndividual($user_type, $access_type){
            $users = DB::table('user')
                    ->where('UserType', '=', $user_type)
                    ->where('access_type', '=', $access_type)
                    ->where('Active', '=', 1)
                    ->get();

            if($users){
                return $users;
            }else{
                return FALSE;
            }
        }
        public function getUsers(){
            $users = DB::table('user')
                    ->join('e_wallet', 'user.UserID', '=', 'e_wallet.UserID')
                    ->where('user.UserType', '=', 1)
                    // ->where('user.Active', '=', 1)
                    ->orderBy('user.UserID', 'desc')
                    ->paginate(10);

            if($users){
                return $users;
            }else{
                return FALSE;
            }
        }
        //get user profile
        //parameter : user id
        //Out put : array
        public function getUserProfileMobile($profileid){
            $findUser = DB::table('user')
                    ->where('UserID', '=', $profileid)
                    // ->where('Active', '=', 1)
                    ->where('UserType', '=', 5)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }
        public function getUserProfile($profileid){
            $findUser = DB::table('user')
                    ->where('UserID', '=', $profileid)
                    // ->where('Active', '=', 1)
                    // ->where('UserType', '=', 1)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }
        public function FindOTPCode($profileid,$otpcode){
            $findUser = DB::table('user')
                    ->where('UserID', '=', $profileid)
                    ->where('OTPCode', '=', $otpcode)
                    // ->where('Active', '=', 1)
                    ->first();

            return $findUser;
        }
       public function UserProfileByRef($refid){
            $findUser = DB::table('user')
                    ->where('Ref_ID', '=', $refid)
                    // ->where('Active', '=', 1)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }





       //xxxxxxxxxxxxxxxxxxxxxx  For Web xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx//

        /* Use          :   Add new users (clinic, doctor and user)
         * Parameter    :
         * Return       :
         */
        public function addNewUser($dataArray){
            $this->Name = $dataArray['name'];
            $this->UserType = $dataArray['usertype'];
            $this->Email = $dataArray['email'];
            $this->PhoneCode = $dataArray['code'];
            $this->PhoneNo = $dataArray['mobile'];
            $this->PhoneNo = $dataArray['mobile'];

            $this->Address = $dataArray['address'];
            $this->City = $dataArray['city'];
            $this->State = $dataArray['state'];
            $this->Password = $dataArray['pw'];

            //$this->Insurance_Company = $dataArray['insurance_company'];
            //$this->Insurance_Policy_No = $dataArray['insurance_policy_no'];
            //$this->Lat = $dataArray['latitude'];
            //$this->Lng = $dataArray['longitude'];
            // $this->NRIC = $dataArray['nric'];
            //$this->FIN = $dataArray['fin'];

            $this->created_at = time();
            $this->updated_at = time();
            $this->Ref_ID = $dataArray['ref_id'];
            $this->ActiveLink = $dataArray['activelink'];
            $this->Status = $dataArray['status'];
            $this->Active = 1;
            $this->source_type = $dataArray['source'];



            if($this->save()){
                $insertedId = $this->id;
                $wallet = new Wallet( );
                $data = array(
                    'UserID'        => $insertedId,
                    'balance'       => "0",
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                );
                $wallet->createWallet($data);
                return $insertedId;
            }else{
                return false;
            }
        }

        /*
         *
         *
         */
        public function getUserDetails($profileid){
            $findUser = DB::table('user')
                    ->where('UserID', '=', $profileid)
                    // ->where('Active', '=', 1)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }

        /* Use          :   Used to get details by activation link
         *
         *
         */
        public function findDoctorByActivationCode($code){
            $findUser = DB::table('user')
                    ->where('ActiveLink', '=', $code)
                    // ->where('Active', '=', 1)
                    ->where('Status', '=', 0)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }
        /* Use          :   Used to get details by reset link
         *
         *
         */
        public function findDoctorByResetCode($code){
            $findUser = DB::table('user')
                    ->where('ResetLink', '=', $code)
                    // ->where('Active', '=', 1)
                    // ->where('Recon', '=', 0)
                    ->first();

            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }

        public function updateUser ($dataArray){
            $allData = DB::table('user')
                ->where('UserID', '=', $dataArray['userid'])
                ->update($dataArray);

            return $allData;
        }

        /*
         *
         *
         */
        public function checkLogin ($email, $password){

            $users = DB::table('user')
                     //->select('UserID')
                     ->where('Email', '=', $email)
                     ->where('Password', '=', StringHelper::encode($password))
                     ->first();
            if($users){
                return $users;
            }else{
                return false;
            }
        }

        public function checkLoginFromAdmin($email, $password){

            $users = DB::table('user')
                     ->where('Email', '=', $email)
                     ->where('Password', '=', $password)
                     ->where('UserType', 3)
                     ->first();
            if($users){
                return $users;
            }else{
                return false;
            }
        }

        public function checkLoginFromAdminUserID($id){

            $users = DB::table('user')
                     ->where('UserID', $id)
                     ->first();
            if($users){
                return $users;
            }else{
                return false;
            }
        }

        /* Use      :   Used to find user details by email
         *
         */
        public function checkEmailExist($email){
            $findEmail = DB::table('user')
                     ->where('Email', '=', $email)
                     ->where('UserType', '=', 3)
                     ->first();
            if($findEmail){
                return $findEmail;
            }else{
                return FALSE;
            }
        }

        public function checkClinicEmail($email) {
            $findEmail = DB::table('user')
                        ->join('clinic', 'clinic.ClinicID', '=', 'user.Ref_ID')
                        ->where(function($query) use ($email){
                            $query->where('clinic.communication_email', '=', $email)
                            ->where('user.UserType', '=', 3);
                        })
                        ->orWhere(function($query) use ($email) {
                            $query->where('user.Email', '=', $email)
                            ->where('user.UserType', '=', 3);
                        })
                        ->first();
            if($findEmail){
                return $findEmail;
            }else{
                return FALSE;
            }
        }

        public function FindUserByNric ($nric){
            $findUser = DB::table('user')
                    ->where('NRIC', '=', $nric)
                    ->where('Active', '=', 1)
                     ->first();
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }
        public function FindRealUser ($nric,$email){
            $findUser = DB::table('user')
                    ->where('NRIC', '=', $nric)
                    ->where('Email', '=', $email)
                    ->where('Active', '=', 1)
                     ->first();
            if($findUser){
                return $findUser;
            }else{
                return FALSE;
            }
        }

    ######################################################## nhr 2016-4-26 #########3

        public function addUser($dataArray){

            $this->Name = $dataArray['name'];
            $this->UserType = $dataArray['usertype'];
            $this->Email = $dataArray['email'];

            $this->created_at = time();
            $this->updated_at = 0;
            $this->Ref_ID = $dataArray['ref_id'];
            $this->ActiveLink = $dataArray['activelink'];
            $this->Status = $dataArray['status'];
            $this->Active = 1;

            if($this->save()){
                $insertedId = $this->id;
                return $insertedId;
            }else{
                return false;
            }
        }

    public function updateAllWithWallet( )
    {
        $wallet = new Wallet( );
        $res = 1;
        $users = DB::table('user')
                    ->where('UserType', '=', 1)
                    ->where('Active', '=', 1)
                    ->get();

        if($users){
            foreach($users as $key => $value) {
                $result = $wallet->updateAllWithWallet($value->UserID);
                if($result) {
                    $res++;
                }
            }

            return array('user_count' => sizeof($users), 'result' => $res);
        }else{
            return FALSE;
        }
    }

    public function checkUserExist($email){
        $findEmail = DB::table('user')
                 ->where('Email', '=', $email)
                 ->where('UserType', '=', 1)
                 // ->orWhere('UserType', '=', 5)
                 ->count();

        if($findEmail > 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function updateUserAccountActive($id, $data)
    {
        return User::where('UserID', '=', $id)->update($data);
    }

    public function searchUser($data)
    {
        $users = DB::table('user')
                    ->join('e_wallet', 'user.UserID', '=', 'e_wallet.UserID')
                    ->where('user.UserType', '=', 1)
                    ->where('user.Active', '=', 1)
                    ->where('user.Name', 'like', '%'.$data.'%')
                    ->orWhere('user.Email', 'like', '%'.$data.'%')
                    ->orWhere('user.NRIC', 'like', '%'.$data.'%')
                    ->orderBy('user.UserID', 'desc')
                    ->paginate(10);

        if($users){
            return $users;
        }else{
            return FALSE;
        }
        // return User::where('Name', 'like', '%'.$data.'%')
        //             ->orWhere('Email', 'like', '%'.$data.'%')
        //             ->paginate(10);
    }

    public function findUserbyEmail($email)
    {
        $data = User::where('Email', '=', $email)->first();
        return $data->UserID;
    }

    public function getUserById($id)
    {
        $data = User::where('UserID', '=', $id)->first();
        return $data->UserID;
    }

    public function checkNric($nric, $email)
    {
        $check = User::where('NRIC', '=', $nric)
                    ->where('Email', '!=', $email)
                    ->where('UserType', '=', 1)
                    // ->orWhere('UserType', '=', 5)
                    ->count();
        if($check == 0) {
            $data = User::where('NRIC', '=', $nric)
                        ->where('Email', '=', $email)
                        ->where('UserType', '=', 1)
                        // ->orWhere('UserType', '=', 5)
                        ->count();
            if($data == 0) {
                return 0;
            } else {
                return 0;
            }
        } else {
            return 1;
        }
    }

    public function FindUserID($id)
    {
        $result = User::where('UserID', $id)->first();
        return $result->UserID;
    }

    public function checkIndividualUser($email)
    {
        return User::where('Email', $email)->where('UserType', 5)->where('access_type', 1)->count();
    }

    public function createIndividualUserFromPurchase($data)
    {
        $this->OTPCode = NULL;
        $this->OTPStatus = NULL;
        $this->Name = $data['first_name'].' '.$data['last_name'];
        $this->Password = $data['password'];
        $this->Email = $data['email'];
        $this->PhoneNo = $data['mobile'] ? $data['mobile'] : "0";
        $this->PhoneCode = NULL;
        $this->Lat = '';
        $this->Lng = '';
        $this->NRIC = $data['nric'];
        $this->FIN = '';
        $this->Image = 'https://res.cloudinary.com/www-medicloud-sg/image/upload/v1427972951/ls7ipl3y7mmhlukbuz6r.png';
        $this->Active = 0; //to active user
        $this->created_at = time();
        $this->updated_at = time();
        $this->UserType = 5; // for users
        $this->source_type = 3;
        $this->access_type = 1;
        if($this->save()){
            $insertedId = $this->id;
            $wallet = new Wallet( );
            $data = array(
                'UserID'        => $insertedId,
                'balance'       => "0",
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            );
            $wallet->createWallet($data);
            return $insertedId;
        }else{
            return false;
        }
    }

    public function updateIndividualUserFromPurchase($password, $id)
    {
        return User::where('UserID', $id)->update(['Password' => $password]);
    }

    public function getUserByLink($id)
    {
        return DB::table('user')
                ->join('customer_link_customer_buy', 'customer_link_customer_buy.user_id', '=', 'user.UserID')
                ->where('customer_link_customer_buy.customer_buy_start_id', $id)
                ->first();
    }
    public function getAllUsersData(){
        $users = DB::table('user')
                ->whereIn('UserType', [1,5])
                ->where('Active', '=', 1)
                ->get();

        if($users){
            return $users;
        }else{
            return FALSE;
        }
    }

    public function pin($user_id, $pin)
    {
        $returnObject = new stdClass();
        $result = User::where('UserID', $user_id)->first();
        if($result) {
            $result_pin = User::where('UserID', $user_id)->update(['user_pin' => $pin, 'pin_setup' => 1]);
            // if($result_pin) {
            $returnObject->status = TRUE;
            $returnObject->message = 'User pin updated successfully';
            // } else {
            //     $returnObject->status = FALSE;
            //     $returnObject->message = 'User pin update failed';
            // }
        } else {
            $returnObject->status = FALSE;
            $returnObject->message = 'User not found.';
        }

        return $returnObject;
    }

    public function checkUserPin($user_id, $pin)
    {
        $result = User::where('UserID', $user_id)->where('user_pin', $pin)->count();

        if($result == 0) {
            return 0;
        } else {
            return 1;
        }
    }

    function checkMemberExistence ($params) {
        $query = User::query();

        $query->select('UserID as user_id', 'Name as name', 'member_activated', DB::raw('IFNULL((case when Zip_Code <= 0 then 0 else Zip_Code end), 0)as postal_code'), 'disabled_otp', 'PhoneNo', 'Active');
       
        // Append where clause
        foreach ($params as $key => $value) {
            $query->where($value['paramKey'], $value['paramKeyValue']);    
        }
        
        $user = $query->first();
        return count((array)$user) > 0? $user: 0;
    }

    function updateMemberRecord ($userId, $data) {
        return User::where('UserID', $userId)
                    ->update($data);
    }
}
