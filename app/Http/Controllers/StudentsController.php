<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class StudentsController extends Controller{
    
    public function book(Request $request){
    	$week_days = [1 => "Saturday", 2 => 'Sunday', 3 => "Monday", 4 => 'Tuesday', 5 => 'Wednesday', 6 => 'Thursday', 7 => "Friday"];
        $validator = Validator::make($request->all(),[
            'start_date' => 'required|date',
            'no_of_sessions' => 'required|int',
            'days' => 'required|array|in:1,2,3,4,5,6,7',
        ])->validate();
        $days = $request->days;
        $start_date = $request->start_date;
        $no_of_sessions = $request->no_of_sessions;

        $sessionsDaysCount = $no_of_sessions * 30;

        $weekday = date('l', strtotime($start_date));
        $keyOfStartDate = array_search ($weekday, $week_days);

        if(!in_array($keyOfStartDate, $days)){
            return response()->json(['success' => false, 'message' => 'Start Date Not In Your Days'], 500);
        }
        
        $dates = [];
        $x = 0;
        $dates[$x++] = $start_date . ' - ' . $week_days[$keyOfStartDate];
        do{
            foreach($days as $key => $day){
                if($x == $sessionsDaysCount)
                    break;

                if($key == 0 && $keyOfStartDate < $key)
                    continue;
                
                $date = date('d-m-Y' ,strtotime("next $week_days[$day]", strtotime($start_date)));
                $start_date = $date;
                $dates[$x++] = $date . ' - ' . $week_days[$day];
            }
        }while ($x < $sessionsDaysCount);

        return response()->json(['dates' => $dates, 'success' => true], 200);
    }
}
