<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';
	protected $primaryKey = 'Ref_ID';

	// Add all user details
 	public function AddUser_old()
    {
    	$this->Email       = Input::get('email');
		$this->Password    = StringHelper::encode(Input::get ('password'));
		$this->UserType    = '3';
		$this->Age         = '0';
		$this->Bmi         = '0';
		$this->Weight      = '0';
		$this->Height      = '0';
		$this->created_at  = time();
		$this->Active      = 1;

    	if($this->save()){
    		$userId = $this->UserID;
    		return $userId;
    	}else{
    		return false;
    	}
    }

    public function AddUser($data)
    {
        $this->Email       = $data['email'];
        $this->Name        = $data['name'];
        $this->Ref_ID      = $data['clinic_id'];
        $this->Password    = StringHelper::encode($data['password']);
        $this->UserType    = '3';
        $this->Age         = '0';
        $this->Bmi         = '0';
        $this->Weight      = '0';
        $this->Height      = '0';
        $this->created_at  = time();
        $this->Active      = 1;

        if($this->save()){
            $userId = $this->UserID;
            return $userId;
        }else{
            return false;
        }
    }

 	public function updateUser($userArray)
    {
		$allData = DB::table('user')
                ->where('UserID', '=', $userArray['userid'])
                ->update($userArray);

            return $allData;
    }


    public function AddUserTypeDoctor()
    {
    	$this->Email           = Input::get('email');
		$this->UserType        = '2';
		$this->Age             = '0';
		$this->Bmi             = '0';
		$this->Weight          = '0';
		$this->Height          = '0';
		$this->created_at      = time();
		$this->Status          = 0;
		$this->Active          = 1;
	 	$this->ActiveLink      = StringHelper::getEncryptValue();

    	if($this->save()){
    		$userId = $this->DoctorID;
    		return $userId;
    	}else{
    		return false;
    	}
    }

    public static function FindUserByEmail($email){
        $users = DB::table('user')
                ->where('Email', '=', $email)
                //->whereNotIn('Ref_ID', array($userid))
                ->where('Active', '=', 1)
                ->first();
        if($users){
            return $users;
        }else{
            return false;
        }
    }

    public static function FindExistingClinic($email){
        $users = DB::table('user')
                ->where('Email', '=', $email)
                ->where('UserType', '=', 3)
                ->first();
        if($users){
            return $users;
        }else{
            return false;
        }
    }

		// get user list
		public static function UserList() {
			return DB::table('user')
			->where('Active', 1)
			->get();
		}

		// get user db colums
		public static function getColumns( ) {
			return Schema::getColumnListing('user');
		}
		public static function findSignUsersByDate($start, $end, $all_status)
		{
			if($all_status == "true") {
				return DB::table('user')
				->where('Active', 1)
				->get();
			} else {
				return DB::table('user')
				->where('Active', 1)    
				->where('created_at', '>=', $start)
				->where('created_at', '<=', $end)
				->get();
			}
		}
}
