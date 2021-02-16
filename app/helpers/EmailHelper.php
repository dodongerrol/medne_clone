<?php

class EmailHelper{
    public static function sendEmail($dataArray){
        
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
        }); 
    }

    public static function sendEmailWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.member-successful-transac', $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailClinicInvoiceFile($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){    
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);

            $pdf_transactions = PDF::loadView('pdf-download.clinic_invoice', $dataArray['data']);
            $pdf_transactions->getDomPDF()->get_option('enable_html5_parser');
            $pdf_transactions->setPaper('A4', 'portrait');
            $message->attachData($pdf_transactions->output(), $dataArray['data']['invoice_number'].' - '.time().'.pdf');
        }); 
    }

    public static function sendPaymentAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView($dataArray['pdf_file'], $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendPaymentAttachmentHealth($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView($dataArray['pdf_file'], $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailRefundWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.pdf-member-refunded-transaction', $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendNewEmailCompanyInvoiceWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('invoice.hr-statement-invoice', $dataArray);
            $pdf->getDomPDF()->get_option('enable_html5_parser');

            $pdf_transaction = PDF::loadView('pdf-download.company-transaction-list-invoice', $dataArray);
            $pdf_transaction->getDomPDF()->get_option('enable_html5_parser');
            $pdf_transaction->setPaper('A4', 'landscape');

            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc($dataArray['ccs']);
            $message->attachData($pdf->output(), $dataArray['statement_number'].'.pdf');
            $message->attachData($pdf_transaction->output(), $dataArray['statement_number'].'.pdf');
        }); 
    }

    public static function sendEmailCompanyInvoiceWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('invoice.hr-statement-invoice', $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['statement_number'].'.pdf');
        }); 
    }


    public static function sendEmailClinicWithAttachment($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $pdf = PDF::loadView('pdf-download.health-partner-successful-transac', $dataArray);
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            $message->attachData($pdf->output(), $dataArray['transaction_id'].'.pdf');
        }); 
    }

    public static function sendEmailDirect($dataArray){
        
        Mail::send($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){       
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            
        }); 
    }

    public static function sendEmailQueue($dataArray) {
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray)
        {
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
        });
    }

    public static function sendMultipleSenderEmail($dataArray, $clinic_email, $clinic_name){
        
        Mail::send($dataArray['emailPage'], $dataArray, function($message) use ($dataArray, $clinic_email, $clinic_name){       
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->bcc($clinic_email, $clinic_name);
            $message->subject($dataArray['emailSubject']);
            $message->cc(['no_reply@mednefits.com']);
            
        }); 
    }

    public static function QueueEmail($dataArray){
        Mail::queue($dataArray['emailPage'], $dataArray, function($message) use ($dataArray){
            $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'],$dataArray['emailName']);
            $message->subject($dataArray['emailSubject']);
        });
    }

    public static function sendErrorLogs($dataArray) {
        $dataArray['emailTo'] = 'developer.mednefits@gmail.com';
        Mail::queue('email-templates.error-logs', $dataArray, function ($message) use ($dataArray){
         $message->from('no_reply@mednefits.com', 'Mednefits');
            $message->to($dataArray['emailTo'], 'Developer');
            $message->subject($dataArray['emailSubject']);
        });
    }    
    
}
