<?php

use Carbon\Carbon;

class EmailBackup extends Eloquent {

	protected $table = 'email_backup';
    protected $fillable = ['UserID', 'email', 'created_at', 'updated_at'];
    function checkEmail($id, $email)
    {
        $count_existence = EmailBackup::where('UserID', '=', $id)->count( );
        if($count_existence >= 2) {
            return 1;
        } else {
            $check_existence = EmailBackup::where('email', '=', $email)->count( );
            if($check_existence > 0) {
                return 2;
            } else {
                $data = array(
                    'UserID' => $id, 
                    'email' => $email, 
                    'created_at' => Carbon::now(), 
                    'updated_at' => Carbon::now()
                );
                $check = EmailBackup::create($data);
                if($check) {
                    return 3;
                }
            }
        }
    }
}
