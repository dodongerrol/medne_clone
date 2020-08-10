<?php
use Illuminate\Support\Facades\Input;
class UserWebController extends BaseController {

	public function __construct(){

    }


     public function index( )
     {    
          $hostName = $_SERVER['HTTP_HOST'];
          $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
          $data['server'] = $protocol.$hostName;
          $data['date'] = new DateTime();
     	return View::make('user.index', $data);
     }

     public function login( )
     {
     	
     	return View::make('user.login');
     }

     public function getBankDetails( ) 
     {    
          $data = [];
          $hostName = $_SERVER['HTTP_HOST'];
          $protocol = $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
          $data['server'] = $protocol.$hostName;
          $bank = new PartnerDetails();
          $getSessionData = StringHelper::getMainSession(3);
          // return $getSessionData->Ref_ID;
          $data['details'] = $bank->getBankDetails($getSessionData->Ref_ID);
          return $data;
     }

     public function createBankDetails()
     {
          $input = Input::all();
          $clinic = new Clinic();
          $bank = new PartnerDetails();
          $check_clinic = $clinic->ClinicDetails($input['partner_id']);
          if(!$check_clinic) {
               return array(
                    'status'  => 400,
                    'message' => 'Clinic does not exist'
               );
          } else {
               $check_bank = $bank->checkExistence($input['partner_id']);
               if($check_bank == 0) {
                    $data = array(
                         'partner_id'             => $input['partner_id'],
                         'bank_name'             => $input['bank_name'],
                         'billing_address'        => $input['billing_address'],
                         'company_billing_name'      => $input['bank_account_type'],
                         'bank_account_number'    => $input['bank_account_number']
                    );
                    $result = $bank->createBankDetails($data);
                    if($result) {
                         return array(
                              'status'  => 200,
                              'message' => 'Successfully created bank details'
                         );
                    } else {
                         return array(
                              'status'  => 400,
                              'message' => $result
                         );
                    }
               } else {
                    $data = array(
                         'bank_name'             => $input['bank_name'],
                         'billing_address'        => $input['billing_address'],
                         'company_billing_name'      => $input['bank_account_type'],
                         'bank_account_number'    => $input['bank_account_number']
                    );
                    $result = $bank->updateBankDetails($data, $input['partner_id']);
                    if($result) {
                         return array(
                              'status'  => 200,
                              'message' => 'Successfully updated bank details'
                         );
                    } else {
                         return array(
                              'status'  => 400,
                              'message' => $result
                         );
                    }
               }
          }

          return array(
               'status'  => 400,
               'message' => 'Clinic already have bank details.'
          );
          
     }

     public function getPreviousBookings( )
     {
          $date = strtotime(date('Y-m-d'));
          $app = new UserAppoinment();

          $books = $app->getPreviousBookings($date);
          return sizeof($books);
          $temp = [];
          foreach ($books as $key => $value) {
               $t = array(
                    'status'  => date('Y-m-d', $value->BookDate)
               );
               array_push($temp, $t);
          }

          return $temp;
     }

}
