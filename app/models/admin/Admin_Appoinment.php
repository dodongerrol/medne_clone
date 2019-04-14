<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Admin_Appoinment extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_appoinment';


        public function BookingById($bookingid){
            $getBooking = DB::table('user_appoinment')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
                        'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
                        'clinic.ClinicID','clinic.Name as CLName',
                        'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
                        'doctor.Name as DocName', 'transaction_history.credit_cost', 'transaction_history.current_wallet_amount')
                ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
                ->leftJoin('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                ->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
                //->where('user_appoinment.Status', '=', 0)
                //->where('user_appoinment.Active', '=', 1)
                //->where('clinic.Active', '=', 1)
                //->where('user.Active', '=', 1)
                ->get();

            return $getBooking;
        }
        public function FindCustomBooking($startdate, $enddate, $created_startbooking, $created_endbooking,  $clinic, $doctor){
                    // return $created_startbooking.$created_endbooking;
                    if($created_startbooking && $created_endbooking && $startdate && $enddate) {
                        $getBooking = DB::table('user_appoinment')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
                        'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
                        'clinic.ClinicID','clinic.Name as CLName',
                        'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
                        'doctor.Name as DocName', 'transaction_history.credit_cost', 'transaction_history.current_wallet_amount')
                ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
                ->leftJoin('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
                ->where('user_appoinment.BookDate', '>=', $startdate)
                ->where('user_appoinment.BookDate', '<=', $enddate)
                ->where('user_appoinment.Created_on', '>=', $created_startbooking)
                ->where('user_appoinment.Created_on', '<=', $created_endbooking)
                ->where('user_appoinment.event_type', '!=', 1)

                ->where(function($getBooking) use ($clinic, $doctor)
                {
                    if ($clinic) {
                        $getBooking->where('clinic.ClinicID', '=', $clinic);
                    }
                    if ($doctor) {
                        $getBooking->where('doctor.DoctorID', '=', $doctor);
                    }
                })
                //->where('clinic.ClinicID', '=', $clinic)
                //->where('doctor.DoctorID', '=', $doctor)
                ->get();

                    return $getBooking;
                    } else if($created_startbooking && $created_endbooking) {
                        if( $created_startbooking == $created_endbooking ) {
                            $getBooking = DB::table('user_appoinment')
                            ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
                                    'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
                                    'clinic.ClinicID','clinic.Name as CLName',
                                    'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
                                    'doctor.Name as DocName', 'transaction_history.credit_cost', 'transaction_history.current_wallet_amount')
                            ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
                            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                            ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                            ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
                            ->leftJoin('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                            ->where('user_appoinment.Created_on', '>=', $created_startbooking) 
                            ->where(function($getBooking) use ($clinic, $doctor)
                            {
                                if ($clinic) {
                                    $getBooking->where('clinic.ClinicID', '=', $clinic);
                                }
                                if ($doctor) {
                                    $getBooking->where('doctor.DoctorID', '=', $doctor);
                                }
                            })
                            ->get();

                        return $getBooking;
                        } else {
                            // return "ads";
                            $getBooking = DB::table('user_appoinment')
                            ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
                                    'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
                                    'clinic.ClinicID','clinic.Name as CLName',
                                    'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
                                    'doctor.Name as DocName', 'transaction_history.credit_cost', 'transaction_history.current_wallet_amount')
                            ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
                            ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                            ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                            ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
                            //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
                                            ->where('user_appoinment.Created_on', '>=', $created_startbooking)
                                            ->where('user_appoinment.Created_on', '<=', $created_endbooking)
                            ->leftJoin('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                            ->where('user_appoinment.event_type', '!=', 1)
                            ->where(function($getBooking) use ($clinic, $doctor)
                            {
                                if ($clinic) {
                                    $getBooking->where('clinic.ClinicID', '=', $clinic);
                                }
                                if ($doctor) {
                                    $getBooking->where('doctor.DoctorID', '=', $doctor);
                                }
                            })
                            //->where('clinic.ClinicID', '=', $clinic)
                            //->where('doctor.DoctorID', '=', $doctor)
                            ->get();

                        return $getBooking;
                        }
                    } else {
                        $getBooking = DB::table('user_appoinment')
                ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
                        'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
                        'clinic.ClinicID','clinic.Name as CLName',
                        'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
                        'doctor.Name as DocName', 'transaction_history.credit_cost', 'transaction_history.current_wallet_amount')
                ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
                ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
                ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
                ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
                ->leftJoin('transaction_history', 'transaction_history.AppointmenID', '=', 'user_appoinment.UserAppoinmentID')
                //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
                ->where('user_appoinment.BookDate', '>=', $startdate)
                ->where('user_appoinment.BookDate', '<=', $enddate)
                ->where('user_appoinment.event_type', '!=', 1)

                ->where(function($getBooking) use ($clinic, $doctor)
                {
                    if ($clinic) {
                        $getBooking->where('clinic.ClinicID', '=', $clinic);
                    }
                    if ($doctor) {
                        $getBooking->where('doctor.DoctorID', '=', $doctor);
                    }
                })
                //->where('clinic.ClinicID', '=', $clinic)
                //->where('doctor.DoctorID', '=', $doctor)
                ->get();

            return $getBooking;
                    }

        }
  //       public function FindCustomBooking($startdate, $enddate, $created_startbooking, $created_endbooking,  $clinic, $doctor){
		// 			if($created_startbooking && $created_endbooking & $startdate & $enddate) {
		// 				$getBooking = DB::table('user_appoinment')
  //               ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
  //                       'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
  //                       'clinic.ClinicID','clinic.Name as CLName',
  //                       'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
  //                       'doctor.Name as DocName')
  //               ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
  //               ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
  //               ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
  //               ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
  //               //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
  //               ->where('user_appoinment.BookDate', '>=', $startdate)
  //               ->where('user_appoinment.BookDate', '<=', $enddate)
		// 						->where('user_appoinment.Created_on', '>=', $created_startbooking)
		// 						->where('user_appoinment.Created_on', '<=', $created_endbooking)
  //               ->where('user_appoinment.event_type', '!=', 1)

  //               ->where(function($getBooking) use ($clinic, $doctor)
  //               {
  //                   if ($clinic) {
  //                       $getBooking->where('clinic.ClinicID', '=', $clinic);
  //                   }
  //                   if ($doctor) {
  //                       $getBooking->where('doctor.DoctorID', '=', $doctor);
  //                   }
  //               })
  //               //->where('clinic.ClinicID', '=', $clinic)
  //               //->where('doctor.DoctorID', '=', $doctor)
  //               ->get();

  //           return $getBooking;
		// 			} else if($created_startbooking && $created_endbooking) {
		// 				$getBooking = DB::table('user_appoinment')
  //               ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
  //                       'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
  //                       'clinic.ClinicID','clinic.Name as CLName',
  //                       'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
  //                       'doctor.Name as DocName')
  //               ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
  //               ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
  //               ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
  //               ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
  //               //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
		// 						->where('user_appoinment.Created_on', '>=', $created_startbooking)
		// 						->where('user_appoinment.Created_on', '<=', $created_endbooking)
  //               ->where('user_appoinment.event_type', '!=', 1)
  //               ->where(function($getBooking) use ($clinic, $doctor)
  //               {
  //                   if ($clinic) {
  //                       $getBooking->where('clinic.ClinicID', '=', $clinic);
  //                   }
  //                   if ($doctor) {
  //                       $getBooking->where('doctor.DoctorID', '=', $doctor);
  //                   }
  //               })
  //               //->where('clinic.ClinicID', '=', $clinic)
  //               //->where('doctor.DoctorID', '=', $doctor)
  //               ->get();

  //           return $getBooking;
		// 			} else {
		// 				$getBooking = DB::table('user_appoinment')
  //               ->select('user_appoinment.UserAppoinmentID','user_appoinment.ClinicTimeID','user_appoinment.UserID','user_appoinment.BookType','user_appoinment.DoctorID','user_appoinment.StartTime','user_appoinment.ProcedureID','user_appoinment.BookDate','user_appoinment.EndTime','user_appoinment.MediaType','user_appoinment.Gc_event_id','user_appoinment.event_type','user_appoinment.Remarks','user_appoinment.Status','user_appoinment.Created_on',
  //                       'clinic_procedure.Name as ProName','clinic_procedure.Duration','clinic_procedure.Price',
  //                       'clinic.ClinicID','clinic.Name as CLName',
  //                       'user.Name as UsrName', 'user.Email as USEmail', 'user.NRIC as USNRIC', 'user.PhoneNo as USPhone',
  //                       'doctor.Name as DocName')
  //               ->join('doctor', 'user_appoinment.DoctorID', '=', 'doctor.DoctorID')
  //               ->join('user', 'user_appoinment.UserID', '=', 'user.UserID')
  //               ->join('clinic_procedure', 'user_appoinment.ProcedureID', '=', 'clinic_procedure.ProcedureID')
  //               ->join('clinic', 'clinic_procedure.ClinicID', '=', 'clinic.ClinicID')
  //               //->where('user_appoinment.UserAppoinmentID', '=', $bookingid)
  //               ->where('user_appoinment.BookDate', '>=', $startdate)
  //               ->where('user_appoinment.BookDate', '<=', $enddate)
  //               ->where('user_appoinment.event_type', '!=', 1)

  //               ->where(function($getBooking) use ($clinic, $doctor)
  //               {
  //                   if ($clinic) {
  //                       $getBooking->where('clinic.ClinicID', '=', $clinic);
  //                   }
  //                   if ($doctor) {
  //                       $getBooking->where('doctor.DoctorID', '=', $doctor);
  //                   }
  //               })
  //               //->where('clinic.ClinicID', '=', $clinic)
  //               //->where('doctor.DoctorID', '=', $doctor)
  //               ->get();

  //           return $getBooking;
		// }

  //       }
}
