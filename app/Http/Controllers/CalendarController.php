<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleCalendar;

class CalendarController extends Controller
{
	
    /**
     * Display general information.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		return view('calendar');
    }


	/**
     * Retrive events from the primary calender.
     *
     * @return \Illuminate\Http\Response
     */
	public function getEventList()
    {
		$calendar = new GoogleCalendar();
		$nrEvents = 25;
	    
		try {
			// Connect to Google Calender API without an authorization code
			$calendar->connect('Simple Calendar App');
			
			$events = $calendar->getEventList($nrEvents);
			$message = $this->initEvents($events);
			
			return response()->json(['success' => 1, 'message' => $message]);
		}	
		catch (\Exception $excep_conn) {
			// Connection failed, return an authorization url
			try{				
				$url = $calendar->getAuthUrl();
				return response()->json(['success' => 0, 'url' => $url]);
			}
			catch (\Exception $excep_url) {
				// An unexpected error occured
				return response()->json(['message' => $excep_url->getMessage()], 503);
			}
		}
    }


	/**
     * Retrive events from the primary calender.
	 * 
	 * @param (post body) $authCode - The authorization code 
     * @return \Illuminate\Http\Response
     */
	public function getEventListAuth()
    {
		$calendar = new GoogleCalendar();
		$nrEvents = 25;
		
		// Non-form-encoded posts do not populate $_POST in PHP. We have to read the post body.
		$json_str = file_get_contents('php://input');
		
		// Convert to a JSON-object
		$json_obj = json_decode($json_str); 
		
		// Get the authCode
		$authCode = $json_obj->{'params'}->{'authCode'};
		
		try{
			// Connect to Google Calender API with an authorization code
			$calendar->connectWithAuthCode('Simple Calendar App', $authCode);
			$events = $calendar->getEventList($nrEvents);
			
			// Return a list of events from the primary calender
			if(empty($events))
				// Calendar is empty
				return response()->json(['success' => 1, 'message' => "No upcoming events"]);
			else {
				$message = $this->initEvents($events);
				return response()->json(['success' => 1, 'message' => $message]);
			}
		}
		catch (\Exception $excep_conn) {
			// An unexpected error occured  
			return response()->json(['message' => $excep_conn->getMessage()], 503);
		}
	}
    
	
	/**
     * Create a new event in the primary calender.
	 *
	 * @param (post body) $event - An event object containing summary, start, end
     * @return \Illuminate\Http\Response
     */
	public function createEvent()
    {
		$calendar = new GoogleCalendar();
		
		// Non-form-encoded posts do not populate $_POST in PHP. We have to read the post body.
		$json_str = file_get_contents('php://input');
		
		// Convert to a JSON-object
		$json_obj = json_decode($json_str);

		// Get the event object
		$event = $json_obj->{'params'};	
		
		try {
			// Connect to Google Calender API 
			$calendar->connect('Simple Calendar App');
			
			// Create the event
			$eventId = $calendar->createEvent($event);
			
			if(!empty($eventId))
				return response()->json(['success' => 1, 'eventId' => $eventId]);
			else	
				return response()->json(['success' => 0]);
		}	
		catch (\Exception $excep_conn) {
			return response()->json(['message' => $excep_conn->getMessage()], 503);
		}
	}
	
	
	/**
     * Delete an event from the primary calender.
	 *
	 * @param (post body) $eventId - The unique event identifier
     * @return \Illuminate\Http\Response
     */
	public function deleteEvent()
    {
		$calendar = new GoogleCalendar();
		
		// Non-form-encoded posts do not populate $_POST in PHP. We have to read the post body.
		$json_str = file_get_contents('php://input');
		
		// Convert to a JSON-object
		$json_obj = json_decode($json_str);
		
		// Get eventId 
		$eventId = $json_obj->{'params'}->{'eventId'};
		
		try {
			// Connect to Google Calender API 
			$calendar->connect('Simple Calendar App');
			
			// Delete the event 
			$deleteEvent = $calendar->deleteEvent($eventId);
			
			if($deleteEvent !== 'failed')
				return response()->json(['success' => 1]);
			else
				return response()->json(['success' => 0]);
		}
		catch (\Exception $excep_conn) {
			return response()->json(['message' => $excep_conn->getMessage()], 503);
		}
	}
	
	
	/**
     * Update an event in the primary calender.
	 *
     * @param (post body) $event - An event object containing eventId, summary, start, end
     * @return \Illuminate\Http\Response
     */
	public function updateEvent()
    {
		$calendar = new GoogleCalendar();
		
		// Non-form-encoded posts do not populate $_POST in PHP. We have to read the post body.
		$json_str = file_get_contents('php://input');
		
		// Convert to a JSON-object
		$json_obj = json_decode($json_str);
	
		// Get the event object
		$event = $json_obj->{'params'};
		
		try {
			// Connect to Google Calender API 
			$calendar->connect('Simple Calendar App');	
			
			// Update the event
			$updatedEvent = $calendar->updateEvent($event);
			
			if(!empty($updatedEvent))
				return response()->json(['success' => 1]);
			else
				return response()->json(['success' => 0]);
		}
		catch (\Exception $excep_conn) {
			return response()->json(['message' => $excep_conn->getMessage()], 503);
		}
	}
	
	
	/**
	*  	Create and return an array with events (id, summary, start, end)    
	*  	
	*	@param $events - An array of events (Google Calendar API - getEventList())
	*   @return - An array with simplified events
	*/
	private function initEvents($events){
		$eventList = array();
				
		foreach ($events as $event) {
			$start = $event->start->dateTime;
			if (empty($start)) {
				$start = $event->start->date;
			}
			$end = $event->end->dateTime;
			if (empty($end)) {
				$end = $event->end->date;
			}
			$newEvent = array('eventId' => $event->id, 'eventTitle' => $event->getSummary(), 'startTime' => $start, 'endTime' => $end);
			array_push($eventList, $newEvent); 
		}
		return $eventList;
	}
}
