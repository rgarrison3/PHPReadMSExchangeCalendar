<?php 

/**
 * Filename......: ExchangeSrvEvent_class.php
 * Author........: Rob Garrison
 * Created.......: 08/07/2011 4:40 PM
 * Description...: Exchange server calendar event class.
 * This class will take in information on your exchange event and setup
 * it in a manner that can make accessing it easier.
 * 
 * ---------------------------------------------------------------------------------
 * 							Example Instantiation and Use
 * --------------------------------------------------------------------------------- 
 * 
 * // iterate through calendar items and build array
 * foreach($x->data->A_MULTISTATUS[0]->A_RESPONSE as $key => $item) {
 *     $event = new ExchangeSrvEvent($item);
 *     
 *     if(((strtotime($event->dateTimeStart) <= strtotime($this->dateTimeStartConvert) && strtotime($event->dateTimeEnd) >= strtotime($this->dateTimeEndConvert)) ||
 *         (strtotime($event->dateTimeStart) >= strtotime($this->dateTimeStartConvert) && strtotime($event->dateTimeEnd) <= strtotime($this->dateTimeEndConvert)))){
 *         $this->results[] = $event;
 *     }
 * }
 * 
 * ---------------------------------------------------------------------------------
 * 									Change Log
 * ---------------------------------------------------------------------------------
 * 
 * Who			What											When
 * ------------ ----------------------------------------------- --------------------
 * 											-
 */
class ExchangeSrvEvent {
	
	//==========================================================================
    // Public Vars
    //==========================================================================	
	
	public $url;
	public $location;
	public $subject;
	public $htmlDescription;
	public $organizer;
	public $allDayEvent;
	public $dateTimeStart;
	public $dateTimeEnd;
	public $busyStatus;
	public $instanceType;
	public $uid;
	public $displayName;
	
	//==========================================================================
    // Constructor
    //==========================================================================
    
	/**
	 * Constructor
	 */
    public function __construct() {
		switch (func_num_args()) { 
        	case 1:
        		$this->url				= func_get_arg(0)->A_HREF[0]->_text;
        		$this->location			= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_LOCATION[0]->_text;
        		$this->subject			= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->E_SUBJECT[0]->_text;
        		$this->htmlDescription	= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->E_HTMLDESCRIPTION[0]->_text;
        		$this->organizer		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_ORGANIZER[0]->_text;
        		$this->allDayEvent		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_ALLDAYEVENT[0]->_text;
        		$this->dateTimeStart	= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_DTSTART[0]->_text;
        		$this->dateTimeEnd		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_DTEND[0]->_text;
        		$this->busyStatus		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_BUSYSTATUS[0]->_text;
        		$this->instanceType		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_INSTANCETYPE[0]->_text;
        		$this->uid				= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->D_UID[0]->_text;
        		$this->displayName		= func_get_arg(0)->A_PROPSTAT[0]->A_PROP[0]->A_DISPLAYNAME[0]->_text;
        		break;
        }
    }
    
}

?>