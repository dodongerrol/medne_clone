<?php

class MYRValidationHelper
{
    public function validateAll($input): array
    {
        $generic_message = 'Please key in either Mobile No. NRIC or Password Number to proceed.';
        $messages = [
            'mobile_error'      => false,
            'mobile_message'    => '',
            'nric_error'        => false,
			'nric_message'      => '',
			'passport_error'    => false,
			'passport_message'  => ''
        ];

        $validated = $this->validated_inputs($input);

        if (count($validated) === 3) {
            $messages['error'] = true;
            $messages['mobile_error']       = true;
            $messages['mobile_message']     = $generic_message;
            $messages['nric_error']         = true;
            $messages['nric_message']       = $generic_message;
            $messages['passport_error']     = true;
            $messages['passport_message']   = $generic_message;
        }

        if (!in_array('mobile', $validated) && $this->invalidMobileNumber($input['mobile'])) {
            $messages['error'] = true;
            $messages['mobile_error'] = true;
            $messages['mobile_message'] = 'Invalid mobile format. Please enter mobile format 9-10 digit number without the prefix 0.';
        }

        if (!in_array('nric', $validated) && $this->invalidNRIC($input['nric'])) {
            $messages['error'] = true;
            $messages['nric_error'] = true;
            $messages['nric_message'] = 'Invalid NRIC format. Please enter NRIC in the format of 12 digit number only.';
        }

        // if (!in_array('passport', $validated) && $this->invalidPassport($input['passport'])) {
        //     $messages['error'] = true;
        //     $messages['passport_error'] = true;
        //     $messages['passport_message'] = 'Invalid passport format. Please enter passport in the format of a letter followed by an 8 digit number.';
        // }

        if (!in_array('mobile', $validated) && $this->checkDuplicates('PhoneNo', $input['mobile'])) {
            $messages['error'] = true;
            $messages['mobile_error'] = true;
            $messages['mobile_message'] = 'Mobile No. already taken.';
        }

        if (!in_array('nric', $validated) && $this->checkDuplicates('NRIC', $input['nric'])) {
            $messages['error'] = true;
            $messages['nric_error'] = true;
            $messages['nric_message'] = 'NRIC already taken.';
        }

        if (!in_array('passport', $validated) && $this->checkDuplicates('passport', $input['passport'])) {
            $messages['error'] = true;
            $messages['passport_error'] = true;
            $messages['passport_message'] = 'Passport already taken.';
        }

        return $messages;
    }

    public function invalidMobileNumber($mobile): bool
    {
        $mobile = preg_replace("/[^0-9]/", "", $mobile);

        if (starts_with($mobile, '0')) {
            $mobile = ltrim($mobile, '0');
        }

        $validator = Validator::make(
            ['mobile' => $mobile],
            ['mobile' => 'min:9|max:10']
        );

        return $validator->fails();
    }

    public function invalidNRIC($nric): bool
    {
        if (!is_numeric($nric)) {
            return true;
        }

        $validator = Validator::make(
            ['nric' => $nric],
            ['nric' => 'min:12|max:12']
        );

        return $validator->fails();
    }

    public function checkDuplicates($field, $value): bool
    {
        $user = DB::table('user')
            ->where($field, $value)
            ->where('Active', 1)
            ->first();

        if ($user) {
            return true;
        }

        if ($field !== 'PhoneNo') {
            $tempUser = DB::table('customer_temp_enrollment')
            ->where($field, $value)
            ->where('enrolled_status', true)
            ->first();

            if ($tempUser) {
                return true;
            }
        }

        return false;
    }

    public function invalidPassport($passport): bool
    {
        if (is_numeric($passport[0])) {
            return true;
        }

        $validator = Validator::make(
            ['passport' => $passport],
            ['passport' => 'min:9|max:9']
        );

        return $validator->fails();
    }

    /**
     * Get key with null values on validate
     */
    public function validated_inputs($input): array
    {
        $validator = Validator::make(
            [
              'mobile' => $input['mobile'],
              'nric' => $input['nric'],
              'passport' => $input['passport'],
            ],
            [
              'mobile' => 'required',
              'nric' => 'required',
              'passport' => 'required',
            ]
        );

        return array_keys($validator->messages()->toArray());
    }
}