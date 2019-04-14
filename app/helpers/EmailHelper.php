<?php

class EmailHelper{
    public static function sendEmail($dataArray){
        
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            
        }); 
    }

    public static function sendEmailWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.member-successful-transac', $dataArray);
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendPaymentAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView($dataArray['pdf_file'], $dataArray);
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailRefundWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.member-refunded-transac', $dataArray);
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailCompanyInvoiceWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('invoice.hr-statement-invoice', $dataArray);
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            $message->attachData($pdf->output(), $dataArray['statement_number'].'.pdf');
        }); 
    }


    public static function sendEmailClinicWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.health-partner-successful-transac', $dataArray);
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailDirect($dataArray){
        
        Mail::send($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            
        }); 
    }

    public static function sendEmailQueue($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray)
        {
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
        });
    }

    public static function sendMultipleSenderEmail($dataArray, $clinic_email, $clinic_name){
        
        Mail::send($dataArray['emailPage'], $dataArray, function($message) use ($dataArray, $clinic_email, $clinic_name){       
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->bcc($clinic_email, $clinic_name);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['info@medicloud.sg']);
            
        }); 
    }

    public static function QueueEmail($dataArray){
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){
            $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
        });
    }

    public static function sendErrorLogs($dataArray) {
        $dataArray['emailTo'] = 'developer.mednefits@gmail.com';
        Mail::queue('email-templates.error-logs', $dataArray, function ($message) use ($dataArray){
         $message->from('noreply@medicloud.sg', 'MediCloud');
            $message->to($dataArray['emailTo'], 'Developer');
            $message->subject($dataArray['emailSubject']);
        });
    }    
    
}
