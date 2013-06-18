<?php 

require_once("HTTPScreenScrape.php");
require_once("XMLUtility.php");
require_once("ExchangeSrvEvent.php");

/**
 * Filename......: ExchangeSrvReadCalendar_class.php
 * Author........: Rob Garrison
 * Created.......: 08/07/2011 2:40 PM
 * Description...: Exchange server calendar reader class.
 * This class will take in information on your exchange server and then go out and 
 * read the folder and return back the information about the events it finds inside 
 * of it.
 * 
 * ---------------------------------------------------------------------------------
 * 							Example Instantiation and Use
 * --------------------------------------------------------------------------------- 
 * 
 * $obj = new ExchangeSrvReadPublicCalendarControl("http://exchangesrv", "exchange/username/Calendar", "exchangeAdmin", "exchangeAdminPassword");
 * $obj->setDateTimeStart("08/01/2011"); // Start Date
 * $obj->setDateTimeEnd("08/31/2011");   // End Date
 * // Results can be brought back on the exec or 
 * // called back in later via the method below
 * // $results = $obj->getResults();
 * $results = $obj->exec();
 * 
 * // Would return back the count of events for 
 * // the date requested
 * $obj->countForDate("08/19/2011 12:30 PM");
 * 
 * // Would return back a array of events for
 * // the date requested
 * $results = $obj->eventsForDate("08/19/2011 12:30 PM");
 * 
 * ---------------------------------------------------------------------------------
 * 									Change Log
 * ---------------------------------------------------------------------------------
 * 
 * Who			What											When
 * ------------ ----------------------------------------------- --------------------
 * Rob			Added in a method to return count of events for	08/17/2011 2:45 PM
 * 				day requested.
 * -			-												-
 * Rob			Changed the search algorithm to include events	08/17/2011 3:31 PM
 * 				that could span over top of the range
 * -			-												-
 * Rob			Added in method that would return back all		08/18/2011 4:45 PM
 * 				events for a date that was requested
 * -			-												-
 */
class ExchangeSrvReadCalendar {
	
	//==========================================================================
    // Private Vars
    //==========================================================================

	public $errorLog = "";
	
	private $results = null;
	
	private $h = null;
	
	private $dateTimeStart = "";
	private $dateTimeStartConvert = "";
	private $dateTimeEnd = "";
	private $dateTimeEndConvert = "";
	
	private $exchangeServer = "";
	private $exchangeCalendarFullLocation = ""; 
	private $exchangeUsername = ""; 
	private $exchangePassword = "";	
    
	//==========================================================================
    // Constructor
    //==========================================================================
    
	/**
	 * Constructor
	 */
    public function __construct() {    	
        switch (func_num_args()) {
            case 4:
                $this->exchangeServer = func_get_arg(0);
                $this->exchangeCalendar = func_get_arg(1);
                $this->exchangeUsername = func_get_arg(2);
                $this->exchangePassword = func_get_arg(3);
                break;
                
            case 6:
                $this->exchangeServer = func_get_arg(0);
                $this->exchangeCalendar = func_get_arg(1);
                $this->exchangeUsername = func_get_arg(2);
                $this->exchangePassword = func_get_arg(3);
                $this->setDateTimeStart(func_get_arg(4));
                $this->setDateTimeEnd(func_get_arg(5));
                break;
                
            default:
                die("Cannot create ExchangeSrvReadCalendar object");
        }
        
    	$this->h = new HTTPScreenScrape();
        $this->h->headers["Host"] = $exchangeServer; 
		$this->h->headers["Content-Type"] = "text/xml"; 
    }
    
	//==========================================================================
    // Setters/Getters
    //==========================================================================
    
    
	/**
	 * Method to set the exchangeServer
	 * 
	 * @param string $value exchangeServer
	 */
    public function setExchangeServer($value) { // sets the exchangeServer
		$this->exchangeServer = $value;
	}
	
	/**
	 * Method to get the exchangeServer
	 * 
	 * @staticvar integer $this->exchangeServer return back the exchangeServer
	 * @return string 
	 */
    public function getExchangeServer() { // gets the exchangeServer
		return $this->exchangeServer;
	}
    
	/**
	 * Method to set the exchangeCalendarFullLocation
	 * 
	 * @param string $value exchangeCalendarFullLocation
	 */
    public function setExchangeCalendarFullLocation($value) { // sets the exchangeCalendar full location
		$this->exchangeCalendarFullLocation = $value;
	}
	
	/**
	 * Method to get the exchangeCalendarFullLocation
	 * 
	 * @staticvar integer $this->exchangeCalendarFullLocation return back the exchangeCalendarFullLocation
	 * @return string 
	 */
    public function getExchangeCalendarFullLocation() { // gets the exchangeCalendar full location
		return $this->exchangeCalendarFullLocation;
	}
    
	/**
	 * Method to set the exchangeUsername
	 * 
	 * @param string $value exchangeUsername
	 */
    public function setExchangeUsername($value) { // sets the exchangeUsername
		$this->exchangeUsername = $value;
	}
	
	/**
	 * Method to get the exchangeUsername
	 * 
	 * @staticvar integer $this->exchangeUsername return back the exchangeUsername
	 * @return string 
	 */
    public function getExchangeUsername() { // gets the exchangeUsername
		return $this->exchangeUsername;
	}
    
	/**
	 * Method to set the exchangePassword
	 * 
	 * @param string $value exchangePassword
	 */
    public function setExchangePassword($value) { // sets the exchangePassword
		$this->exchangePassword = $value;
	}
	
	/**
	 * Method to get the exchangePassword
	 * 
	 * @staticvar integer $this->exchangePassword return back the exchangePassword
	 * @return string 
	 */
    public function getExchangePassword() { // gets the exchangePassword
		return $this->exchangePassword;
	}
    
	/**
	 * Method to set the dateTimeStart
	 * 
	 * @param string $value dateTimeStart
	 */
    public function setDateTimeStart($value) { // sets the dateTimeStart
		$this->dateTimeStart = $value;
		
		$this->dateTimeStartConvert = date("Y/m/d 00:00:00", strtotime($this->dateTimeStart)); 
	}
	
	/**
	 * Method to get the dateTimeStart
	 * 
	 * @staticvar integer $this->dateTimeStart return back the dateTimeStart
	 * @return string 
	 */
    public function getDateTimeStart() { // gets the dateTimeStart
		return $this->dateTimeStart;
	}
    
	/**
	 * Method to set the dateTimeEnd
	 * 
	 * @param string $value dateTimeEnd
	 */
    public function setDateTimeEnd($value) { // sets the dateTimeEnd
		$this->dateTimeEnd = $value;
		
		$this->dateTimeEndConvert = date("Y/m/d 23:59:59", strtotime($this->dateTimeEnd)); 
	}
	
	/**
	 * Method to get the dateTimeEnd
	 * 
	 * @staticvar integer $this->dateTimeEnd return back the dateTimeEnd
	 * @return string 
	 */
    public function getDateTimeEnd() { // gets the dateTimeEnd
		return $this->dateTimeEnd;
	}
	
	/**
	 * Method to get the results
	 * 
	 * @staticvar integer $this->results return back the results
	 * @return string 
	 */
    public function getResults() {
		return $this->results;
	}
	
	//==========================================================================
    // Protected Methods
    //==========================================================================
    
	/**
	 * Method to send a webdav request to the specified exchange server, 
	 * the request would query the folder for any calendar events and return them back in asc order.
	 * 
	 * @return bool 
	 */
	protected function searchCalendar() {
		$this->h->xmlrequest = <<<xmlRequest
			<?xml version="1.0"?>
			<g:searchrequest xmlns:g="DAV:">
				<g:sql> 
					Select
						"urn:schemas:calendar:location", 
						"urn:schemas:httpmail:subject",
						"urn:schemas:httpmail:htmldescription",
						"urn:schemas:calendar:contact",
						"urn:schemas:calendar:organizer",
						"urn:schemas:calendar:alldayevent",
						"urn:schemas:calendar:dtstart", 
						"urn:schemas:calendar:dtend",
						"urn:schemas:calendar:busystatus",
						"urn:schemas:calendar:instancetype",
						"urn:schemas:calendar:uid",
						"DAV:displayname"
					FROM 
						Scope('SHALLOW TRAVERSAL OF "/{$this->exchangeCalendar}/"')
					WHERE 
						NOT "urn:schemas:calendar:instancetype" = 1
						AND "DAV:contentclass" = 'urn:content-classes:appointment'
					ORDER BY 
						"urn:schemas:calendar:dtstart" ASC
			         </g:sql>
			</g:searchrequest>
xmlRequest;

		if (!$this->h->fetch($this->exchangeServer . "/" . $this->exchangeCalendar, 0, null, $this->exchangeUsername, $this->exchangePassword, "SEARCH")) { 
			$this->errorLog .= "<h2>There is a problem with the http request!</h2>";
			return false; 
		}
		
		// XML result back from the Exchange Server
		$x = new XMLUtility(); 
		
		if (!$x->fetch($this->h->body)) { 
		    $this->errorLog .= "<h2>There was a problem parsing the XML!</h2><br />"; 
		    $this->errorLog .= "<pre>".$this->h->log."</pre><hr /><br />"; 
		    $this->errorLog .= "<pre>".$this->h->header."</pre><hr /><br />"; 
		    $this->errorLog .= "<pre>".$this->h->body."</pre><hr /><br />"; 
		    $this->errorLog .= "<pre>".$x->log."</pre><hr />"; 
		    return false; 
		}
		
		$this->results = array();
		
		// iterate through calendar items and build array
		foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $key => $item) {
			$event = new ExchangeSrvEvent($item);
			
			if(((strtotime($event->dateTimeStart) <= strtotime($this->dateTimeStartConvert) && strtotime($event->dateTimeEnd) >= strtotime($this->dateTimeEndConvert)) ||
			    (strtotime($event->dateTimeStart) >= strtotime($this->dateTimeStartConvert) && strtotime($event->dateTimeEnd) <= strtotime($this->dateTimeEndConvert)))){
				$this->results[] = $event;
			}
		}
		
		return true;
	}
	
	//==========================================================================
    // Public Methods
    //==========================================================================
	
	/**
	 * Method to return back events for 
	 * a given date
	 * 
	 * @param $dateRequested string
	 * @return array Made up of ExchangeSrvEvent classes
	 * @see ExchangeSrvEvent 
	 */
	public function eventsForDate($dateRequested) {
		$sd = strtotime(date("m/d/Y 00:00:00", strtotime($dateRequested)));
		$ed = strtotime(date("m/d/Y 23:59:59", strtotime($dateRequested)));
		$eventsForDateRequested = array();
		
		foreach ($this->results as $key => $record) {
			if ((strtotime($record->dateTimeStart) >= $sd && strtotime($record->dateTimeStart) <= $ed) ||
				(strtotime($record->dateTimeStart) <= $sd && strtotime($record->dateTimeEnd) >= $ed)) {
				$eventsForDateRequested[] = $record;
			}
		}
		
		return $eventsForDateRequested;
	}
	
	/**
	 * Method to return back a count of events
	 * for a given date
	 * 
	 * @param $dateRequested string
	 * @return int
	 */
	public function countForDate($dateRequested) {
		$sd = strtotime(date("m/d/Y 00:00:00", strtotime($dateRequested)));
		$ed = strtotime(date("m/d/Y 23:59:59", strtotime($dateRequested)));
		
		$x = 0;
		
		foreach ($this->results as $key => $record) {
			if ((strtotime($record->dateTimeStart) >= $sd && strtotime($record->dateTimeStart) <= $ed) ||
				(strtotime($record->dateTimeStart) <= $sd && strtotime($record->dateTimeEnd) >= $ed)) {
				$x++;
			}
		}
		
		return $x;
	}
	
	/**
	 * Method to send a webdav request to the specified exchange server
	 * 
	 * @return array
	 * @see ExchangeSrvEvent 
	 */
	public function exec() {
		if ($this->searchCalendar()) {
			return $this->results;
		} else {
			die($this->errorLog);
		}
	}
}
?>