<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Event;

class EventController extends Controller
{
    //
    public function home(){

        $fromDate = new Carbon('first day of this month');
        $from = $fromDate;
        $fromDate = Carbon::parse($fromDate)->format('Y-m-d');
        $toDate = new Carbon('last day of this month');
        $to = $toDate;
        $toDate = Carbon::parse($toDate)->format('Y-m-d');
        $monthNow = Carbon::now();
        $monthNow = Carbon::parse($monthNow)->format('M Y');

        $days = $this->getDays($from, $to);

        return view('calendar')
            ->with('fromDate', $fromDate)
            ->with('toDate', $toDate)
            ->with('monthNow', $monthNow)
            ->with('days', $days);
    }

    public function createEvent(){ 
        //in here what you need to do is that, call this using ajax on blade
        //after that in the jquery part after the ajax is successful, change the monthInput(monthNow)
        //and the days from the user's date input
        $eventName = $_GET['eventName'];
        $fromDate = Carbon::parse($_GET['fromDate']);
        $toDate = Carbon::parse($_GET['toDate']);
        $checkedDays = $_GET['days'];

        $days = $this->getDays($fromDate, $toDate);

        // i fixed it like this because if I use the $fromDate after doing the getDays func, I get the next month of that. maybe the $date->addDay() causes that, so I used the $_GET again to reset it
        $monthNow= Carbon::parse($_GET['fromDate'])->format('M Y');

        // do the mysql connection here
        $event = new Event;
        $event->label = $eventName;
        $event->from = $fromDate;
        $event->to = $toDate;

        $daysConc = "";
        foreach ($checkedDays as $chckDay) {
            $daysConc .= $chckDay . ", ";
        }
       
        $event->days = $daysConc;
        $event->save();

        return json_encode(array('monthNow' => $monthNow, 'days' => $days, 'eventName' => $eventName));
    }

    private function getDays(Carbon $from, Carbon $to){
        //carbon lte -> gets the interval from initial to end
        //carbon addDay() -> increments the initial date
        $dates = [];

        for($date = $from; $date->lte($to); $date->addDay()) {
            $dates[] = $date->format('d l');
        }
        return $dates;
    }
}
