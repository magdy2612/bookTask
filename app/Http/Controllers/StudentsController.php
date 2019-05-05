<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class StudentsController extends Controller{
    
    public function book(Request $request){
    	$week_days = [1 => "Saturday", 2 => 'Sunday', 3 => "Monday", 4 => 'Tuesday', 5 => 'Wednesday', 6 => 'Thursday', 7 => "Friday"];
        $validator = Validator::make($request->all(),[
            'start_date' => 'required|date',
            'no_of_sessions' => 'required|numeric',
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

        return response()->json(['dates' => $dates, 'count' => sizeof($dates), 'success' => true], 200);
    }

    public function rebook(Request $request){
    	$week_days = [1 => "Saturday", 2 => 'Sunday', 3 => "Monday", 4 => 'Tuesday', 5 => 'Wednesday', 6 => 'Thursday', 7 => "Friday"];
        $validator = Validator::make($request->all(),[
            'start_date' => 'required|date',
            'no_of_sessions' => 'required|numeric',
            'days' => 'required|array|in:1,2,3,4,5,6,7',
        ])->validate();
        $days = $request->days;
        $start_date = $request->start_date;
        $no_of_sessions = $request->no_of_sessions;

        $sessionsDaysCount = $no_of_sessions * 30;

        $start_date = Carbon::parse($request->start_date);
        $weekday = $start_date->englishDayOfWeek;
        $keyOfStartDate = array_search ($weekday, $week_days);

        $x = 0;

        if(!in_array($keyOfStartDate, $days)){
            return response()->json(['success' => false, 'message' => 'Start Date Not In Your Days'], 404);
        }

        $dates[$x++] = $start_date->format('d-m-Y');
        do{
        	foreach($days as $key => $day){
                if($x == $sessionsDaysCount)
                    break;

                if($key == 0 && $keyOfStartDate < $key)
                    continue;

                $dates[$x] = Carbon::parse($dates[$x-1])->modify("next ".$week_days[$day])->format('d-m-Y');
                $x++;
            }
        }while($x < $sessionsDaysCount);

        return response()->json(['dates' => $dates, 'count' => sizeof($dates), 'success' => true], 200);
    }
}
