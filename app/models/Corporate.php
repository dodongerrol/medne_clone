<?php


class Corporate extends Eloquent {

    protected $table = 'corporate';
    protected $guarded = ['corporate_id'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    function allCorporate( )
    {
        return DB::table('corporate_members')
                ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
                ->where('user.Active', 1)
                ->get();
    }

    function createCorporate($data)
    {   
        // return $data['email'];
        // $result = Corporate::where('email', '=', $data['email'])->count();
        // if($result > 0) {
        //  return false;
        // } else {
            return Corporate::create($data);
        // }
    }
    function checkIdentification($identification_number)
    {
        return Corporate::where('identification_number', '=', $identification_number)->count();
    }
     function checkEmail($email)
    {
        return Corporate::where('email', '=', $email)->count();
    }
    function getListCorporate( )
    {
        // return Corporate::all();
        return DB::table('corporate')
                ->join('user', 'user.UserID', '=', 'corporate.UserID')
                ->get();
    }

    function updateCorporate($data, $id)
    {
        return Corporate::where('corporate_id', '=', $id)->update($data);
    }

    function activateAccount($id)
    {   
        $result = Corporate::where('UserID', '=', $id)->count();
        if($result > 0)
        {
            $res = Corporate::where('UserID', '=', $id)->update(['active' => 1]);
            if($res) {
                $wallet = new Wallet( );
                $wallet_result = $wallet->activateWallet($id);
                if($wallet_result == "true") {
                    return Corporate::where('UserID', '=', $id)->first();
                } else {
                    return "false";
                }
            } else {
                return "false";
            }
        }
    }

    function getCorporateEmail($id)
    {
        return Corporate::where('UserID', '=', $id)->first();
    }

    function findUserID($id)
    {
        return DB::table('corporate')->select('UserID')
                ->where('corporate_id', '=', $id)
                ->first();
    }

    function searchCoporate($search)
    {
        // return Corporate::where('identification_number', '=', $search)->get();
        // $result = DB::table('user')
        //         ->join('corporate', 'corporate.UserID', '=', 'user.UserID')
        //         ->where('user.NRIC', $search)
        //         ->orWhere('corporate.identification_number', $search)
        //         ->select('user.UserID', 'corporate.first_name', 'corporate.last_name', 'user.Email', 'user.PhoneCode', 'user.PhoneNo', 'user.NRIC', 'corporate.identification_number', 'corporate.company_name', 'corporate.corporate_id', 'corporate.credit')
        //         ->get();
        // if(sizeof($result) == 0) {
        //     return DB::table('corporate')
        //             ->join('corporate_members', 'corporate_members.corporate_id', '=', 'corporate.corporate_id' )
        //             ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
        //             ->where('user.NRIC', '=', $search)
        //             ->where('user.UserType', '=', 5)
        //             ->select('corporate_members.first_name as first_name', 'corporate_members.last_name as last_name', 'user.UserID', 'user.NRIC as identification_number', 'corporate.company_name', 'corporate.corporate_id', 'user.Email', 'corporate.credit')
        //             ->get();
        // }
        $data = DB::table('corporate_members')
                // ->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
                ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
                ->where('user.NRIC', 'LIKE', '%'.$search.'%')
                ->where('user.UserType', '=', 5)
                ->where('user.Active', '=', 1)
                ->get();

        foreach ($data as $key => $value) {
            if(strtolower($search) === strtolower($value->NRIC)) {
                return array($value);
            }
        }

        return 0;
        // return $result;
    }

    public function getCorporateById($id)
    {
        // $result = DB::table('corporate')
        //             ->join('corporate_members', 'corporate_members.corporate_id', '=', 'corporate.corporate_id' )
        //             ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
        //             ->where('user.UserID', '=', $id)
        //             ->where('user.UserType', '=', 5)
        //             ->select('corporate_members.first_name as first_name', 'corporate_members.last_name as last_name', 'user.UserID', 'user.NRIC as identification_number', 'corporate.company_name', 'corporate.corporate_id', 'user.Email', 'corporate.credit', 'user.PhoneCode', 'user.PhoneNo', 'user.NRIC')
        //             ->first();
        // if(sizeof($result) == 0) {
        //     return DB::table('corporate')
        //             ->join('user', 'user.UserID', '=', 'corporate.UserID')
        //             ->where('corporate.UserID', '=', $id)
        //             ->select('corporate.first_name', 'corporate.last_name', 'user.UserID', 'user.NRIC as identification_number', 'corporate.company_name', 'corporate.corporate_id', 'user.Email', 'corporate.credit', 'user.PhoneCode', 'user.PhoneNo', 'user.NRIC')
        //             ->first();
        // }

        // return $result;
        return DB::table('corporate_members')
                ->join('corporate', 'corporate.corporate_id', '=', 'corporate_members.corporate_id')
                ->join('user', 'user.UserID', '=', 'corporate_members.user_id')
                ->where('user.UserID', '=', $id)
                ->where('user.UserType', '=', 5)
                ->first();
    }

    public function deductCredits($user_id, $amount) 
    {
        $check = Corporate::where('UserID', '=', $user_id)->count();
        if($check > 0) {
            Corporate::where('UserID', '=', $user_id)->update(['credit' => $amount]);
        }
    }
}
