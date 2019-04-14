<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Input;

class CreditPassword extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'credit_password';
    protected $fillable = ['password', 'created_at', 'updated_at'];

    function savePassword($data)
    {
        return CreditPassword::create($data);
    }

    function checkPass( )
    {
        $input = Input::all();

        $result = CreditPassword::where('password', '=', $input['password'])->count();

        if($result > 0)
        {
            return 1;
        } else {
            return 0;
        }
    }
}
