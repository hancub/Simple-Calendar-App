<?php
/**
 * 	GoogleCalendar.php
 *
 * 	Author: Hans Cube
 * 	Date: 2020-12-11
 * 	Description: A simple class for basic communication with Google Calender API 
 */
namespace App\Services;

require_once __DIR__ . '/../../../vendor/autoload.php';

class GoogleCalendar {
	private $client;
	private $connectionStatus;
	private $tokenPath;
	private $clientSecret;
    
	
	/**
	* 	Constructor - initialize properties
	*/
	function __construct() {
		$this->tokenPath = __DIR__ . '/token.json';
		$this->clientSecret = __DIR__ . '/client_secret.json';
		$this->connectionStatus = false;
		$this->client = new \Google_Client(); 
	}
	
	
	/**
	*  	Creates a connection to Google Calender API and 
	*   retrives an authorized API client.
	*  	
	*	@param $appName - The application name
	*   @throws - An authorization error if it fails    
	*/
	function connect($appName) {
		$this->client->setApplicationName($appName);
		$this->client->setScopes(\Google_Service_Calendar::CALENDAR_EVENTS);
		$this->client->setAuthConfig($this->clientSecret);
		$this->client->setAccessType('offline');
		$this->client->setPrompt('select_account consent');
		
		if(file_exists($this->tokenPath)) {
			$accessToken = json_decode(file_get_contents($this->tokenPath), true);
			$this->client->setAccessToken($accessToken);
		}
		if($this->client->isAccessTokenExpired()) {
			if ($this->client->getRefreshToken()) {
				$this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
			} else {				
				$this->connectionStatus = false;
				throw new \Exception('Need an authorization code');
			}
			if (!file_exists(dirname($this->tokenPath))) {
				mkdir(dirname($this->tokenPath), 0700, true);
			}
			file_put_contents($this->tokenPath, json_encode($this->client->getAccessToken()));
		}
		$this->connectionStatus = true;
	}
	
	
	/**
	*  	Creates a connection to Google Calender API with a specified
	* 	authorization code and retrives an authorized API client
	*  	
	*	@param $appName - The application name
	*   @param $authCode - An authorization code
	*   @throws - A connection error if it fails    
	*/
	function connectWithAuthCode($appName, $authCode) {		
		$this->client->setApplicationName($appName);
		$this->client->setScopes(\Google_Service_Calendar::CALENDAR_EVENTS);
		$this->client->setAuthConfig($this->clientSecret);
		$this->client->setAccessType('offline');
		$this->client->setPrompt('select_account consent');
		
		if(file_exists($this->tokenPath)) {
			$accessToken = json_decode(file_get_contents($this->tokenPath), true);
			$this->client->setAccessToken($accessToken);
		}
		if($this->client->isAccessTokenExpired()) {
			if($this->client->getRefreshToken()) {
				$this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
			} else {
				$accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
				$this->client->setAccessToken($accessToken);
				
				if(array_key_exists('error', $accessToken)) {
					throw new \Exception(join(', ', $accessToken));
				}
			}
			if(!file_exists(dirname($this->tokenPath))) {
				mkdir(dirname($this->tokenPath), 0700, true);
			}
			file_put_contents($this->tokenPath, json_encode($this->client->getAccessToken()));
		}
		$this->connectionStatus = true;
	}
	
	
	/**
	* 	Returns an authorization url   
	*  	
	*   @return - The requested authorization url 
	*   @throws - A missing client error if the client object is null     
	*/
	function getAuthUrl(){
		if($this->client != null)
			return $this->client->createAuthUrl();
		else
			throw new \Exception('Missing client');
	}
	
	/**
	*  	Retrives a list of events from the users primary calender. 
	*  	
	*	@param $nrEvents - The maximum number of events to retrieve
	*   @return - An array containing the events    
	*/
	function getEventList($nrEvents){
		if($this->isConnected()){
			$service = new \Google_Service_Calendar($this->client);
			$calendarId = 'primary';
			$optParams = array('maxResults' => $nrEvents, 'orderBy' => 'startTime', 'singleEvents' => true, 'timeMin' => date(DATE_ATOM, mktime(0, 0, 0, 1, 1, 1000))/*date('c')*/);
			$results = $service->events->listEvents($calendarId, $optParams);
			return $results->getItems();	
		}
		else
			return '';
	}
	
	
	/**
	*  	Deletes an event from the users primary calender. 
	*  	
	*	@param $eventId - The unique event identifier
	*   @return - An empty response body if the operation succeeded or the string 'failed' otherwise
	*/
	function deleteEvent($eventId){
		if($this->isConnected()){
			$service = new \Google_Service_Calendar($this->client);
			return $service->events->delete('primary', $eventId);
		}
		return 'failed';
	}
	
	
	/**
	*  	Updates an event in the users primary calender. 
	*  	
	*	@param $event - An event object containing id, summary, start, end
	*   @return - An event response body if the operation succeeded or an empty string if it failed	
	*/
	function updateEvent($event){
		if($this->isConnected()){
			$service = new \Google_Service_Calendar($this->client);
			
			$updateEvent = $service->events->get('primary', $event->{'id'});
			$updateEvent->setSummary($event->{'title'});
			
			$start = new \Google_Service_Calendar_EventDateTime();
			$start->setDateTime($event->{'start'});
			$start->setTimeZone('Europe/Stockholm');
			$updateEvent->setStart($start);
			
			$end = new \Google_Service_Calendar_EventDateTime();
			$end->setDateTime($event->{'end'});
			$end->setTimeZone('Europe/Stockholm');
			$updateEvent->setEnd($end);
			
			return $service->events->update('primary', $updateEvent->getId(), $updateEvent);
		}
		else
			return '';
	}
	
	
	/**
	*  	Creates a new event in the users primary calender. 
	*  	
	*	@param $event - An event object containing summary, start, end
	*   @return - A unique event identifier if the operation succeeded or an empty string if it failed	
	*/
	function createEvent($event){
		if($this->isConnected()){
			$service = new \Google_Service_Calendar($this->client);
			$calendarEvent = new \Google_Service_Calendar_Event(array(
			  'summary' => $event->{'title'},
			  'start' => array(
				'dateTime' => $event->{'start'},
				'timeZone' => 'Europe/Stockholm',
			  ),
			  'end' => array(
				'dateTime' => $event->{'end'},
				'timeZone' => 'Europe/Stockholm',
			  ),
			));  
			$calendarId = 'primary';
			$newEvent = $service->events->insert($calendarId, $calendarEvent);
			return $newEvent->id;
		}
		else 
			return '';
	}
	
	
	/**
	*  	Returns the current connection status. 
	*  	
	*   @return - True if a connection exist or false otherwise  	
	*/
	function isConnected() {
		return $this->connectionStatus;
	}
}
?>