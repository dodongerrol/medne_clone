<?php

class ClinicHelper {
    public static function getCheckClinicPeakHour($clinic, $date)
    {
        $day = strtolower(date('D', strtotime($date)));
        
        $clinic_peak = DB::table('clinic_peak')
                        ->where('clinic_id', $clinic->ClinicID)
                        ->where('day', $day)
                        ->where('active', 1)
                        ->first();
        
        if($clinic_peak) {
            if($clinic_peak->type == "full") {
                return array('status' => true, 'amount' => $clinic_peak->amount);
            } else {
                $start = strtotime(date('h:ia', $clinic_peak->start));
                $end = strtotime(date('h:ia', $clinic_peak->end));
                $time = strtotime(date('h:ia', strtotime($date)));
                $peak_status = $time >= $start && $time <= $end;

                if($peak_status) {
                    return array('status' => true, 'amount' => $clinic_peak->amount);
                }
            }
        }

        $month = strtolower(date('M', strtotime($date)));
        $day = date('j', strtotime($date));

        $holiday =  DB::table('clinic_peak_holiday')
                    ->where('clinic_id', $clinic->ClinicID)
                    ->where('day_number', $day)
                    ->where('month', $month)
                    ->where('active', 1)
                    ->first();

        if($holiday) {
            if($holiday->type == "full") {
                return array('status' => true, 'amount' => $holiday->amount);
            } else {
                $start = $holiday->start;
                $end = $holiday->end;
                $time = strtotime(date('h:ia', strtotime($date)));
                $peak_status = $time >= $start && $time <= $end;

                if($peak_status) {
                    return array('status' => true, 'amount' => $holiday->amount);
                }
            }
        }

        return array('status' => false);
    }

    public static function sendClinicPeakStatus($data)
    {
    	return Mail::send('email-templates.clinic-peak-hours', $data, function($message) use ($data){
          $message->from('noreply@medicloud.sg', 'MediCloud');
          $message->to($data['to'], 'MediCloud');
          $message->subject($data['subject']);
      	});
    }
}
?>