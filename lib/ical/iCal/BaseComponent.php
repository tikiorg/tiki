<?php

/*
    PHP iCal Interface Library
    Copyright (C) 2005  Gregory Szorc <gregory.szorc@case.edu>

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA

*/
/**
* iCal (RFC 2245) class definition
*
* PHP version 5
*
* @package    iCal
* @author     Gregory Szorc <gregory.szorc@case.edu>
* @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*/

 
/**
 *  The base component class defined variables and methods common to all, or much of the components
 *
 *  This class defines some methods which should not be available to derived classes.  We get around this
 *  problem by defining the methods as protected in this base function.  Child classes implement the methods
 *  by declaring the method's visibility as public and defining the function body to be a function call to
 *  parent::method().  We could get around this problem is PHP supported multiple inheritance, but that is not the case.
 *  Thankfully, PHP Documentor won't show methods that are protected or private.  Still, there is a small
 *  problem regarding overhead for every object creation.  The impact should be minimal, however.
 *
 *  @package iCal
 *  @author Gregory Szorc <gregory.szorc@case.edu>
 *  @abstract
 */
abstract class File_iCal_BaseComponent {
    /**
     *  A document associated with the component
     *
     *  @var    array
     *  @access private
     */
    private $_attachments = array();

    /**
     *  Add an attachment to the event
     *
     *  This function is defined in the BaseComponent class as protected.  It's visibility will be made
     *  public for the components which allow attachments.  It will remain protected for all other components.
     *
     *  @access protected
     */
    protected function addAttachment($a) {

    }

    /**
     *  Get the attachments belonging to the component
     *
     *  This function is defined in the BaseComponent class as protected.  It's visibility will be made
     *  public for the components which allow attachments.  It will remain protected for all other components.
     *
     *  @access protected
     *  @return array   Array of ??
     */
    protected function getAttachments() {

    }

    /**
     *  Comments about the component
     *
     *  @var    array
     *  @access private
     */
    private $_comments = array();

    /**
     * Adds a comment to the component
     *
     *  @access protected
     *  @param  string  $c  Comment text to add
     */
    protected function addComment($c) {
        if (!in_array($c, $this->_comments)) {
            $this->_comments[] = $c;
        }
    }

    /**
     * Returns an array of comments for the component
     *
     *  @access protected
     *  @return array   An array of strings
     */
    protected function getComments() {
        return $this->_comments;
    }


    /**
     *  A description of the component
     *
     *  @link   http://asg.web.cmu.edu/rfc/rfc2445.html#sec-4.8.1.5
     *  @var    string
     *  @access protected
     */
    protected $_description;

    /**
     * Set the description of the event to a specified string
     *
     *  @access protected
     *  @param  string  $d  The string to which to set the description of the component
     */
    protected function setDescription($d) {
        $this->_description = $d;
    }

    /**
     * Returns the description of the event
     *
     *  @access protected
     *  @return string  String representation the description for the component
     */
    protected function getDescription() {
        return $this->_description;
    }

    /**
     *  A short summary or subject for the component
     *  @var    string
     *  @access protected
     */
    private $_summary;

    /**
     * Set the summary for a component
     *
     *  @access protected
     *  @param  string  $s  Text to which to set the summary of the component
     */
    protected function setSummary($s) {
        $this->_summary = $s;
    }

    /**
     * returns the summary for a component
     *
     *  @access protected
     *  @return string  Text representing a summary of the component
     */
    protected function getSummary() {
        return $this->_summary;
    }

    /**
     *  The end date for the component
     *
     *  @var    int
     *  @access private
     */
    protected $_dtend = 0;

    /**
     *  Set thh end date for the component
     *
     *  @param  int $dt Timestamp for the date on which to end
     *  @access protected
     *
     */
    protected function setDateEnd($dt) {
        if (ctype_digit($dt)) {
            if ($dt > $this->_dtstart) {
                $this->_dtend = $dt;
              }
            else {
                trigger_error("The end date must be after the start date", E_USER_WARNING);
            }
        }
        else {
            trigger_error("The end date specified is not an integer", E_USER_WARNING);
        }
    }

    /**
     *  Get the end date for the component
     *
     *  Returns a UNIX timestamp
     *
     *  @return int UNIX timestamp for end date
     *  @access protected
     */
    protected function getDateEnd() {
        return $this->_dtend;
    }

    /**
     *  The start date for the component
     *
     *  @access private
     *  @var    int UNIX timestamp for the start date
     */
    protected $_dtstart = 0;

    /**
     *  set the start date for a component
     *
     *  type is a UNIX timestamp
     *
     *  @param  int $ds UNIX timestamp for start date/time
     *  @access protected
     */
    protected function setDateStart($ds) {
        if (ctype_digit($ds)) {
            if ($ds > 0) {
                $this->_dtstart = $ds;
            }
            else {
                trigger_error("Start date specified cannot be less than 0", E_USER_WARNING);
            }
        }
        else {
            trigger_error("Start date must be an integer");
        }
    }

    /**
     *  Get the start date for the component
     *
     *  @return int UNIX timestamp of start date
     */
    protected function getDateStart() {
        return $this->_dtstart;
    }

    /**
     *  The duration of the component
     *  @var    int
     */
    private $_duration;

    /**
     *  Set the duration of the component
     *
     *  @param  int length in seconds of the component
     */
    protected function setDuration($d) {
        if (ctype_digit($d)) {
            if ($d > 0) {
                $this->_duration = $d;
            }
            else {
                trigger_error("Duration must be greater than 0", E_USER_WARNING);
            }
        }
        else {
            trigger_error("Duration specified ($d) is not an integer", E_USER_WARNING);
        }
    }

    /**
     *  Get the duration of the component
     *
     */
    protected function getDuration() {
        return $this->_duration;
    }

    /**
     *  The attendees for the component
     */
    private $_attendees = array();

    /**
     *  Add an attendee to the component
     *
     *  @param  File_iCal_Attendee  $a
     */
    protected function addAttendee($a) { //File_iCal_Attendee 
        $this->_attendees[] = $a;
    }

    /**
     *  Gets attendees for an event
     *
     *  @return array   Array of File_iCal_Attendee
     */
    protected function getAttendees() {
        return $this->_attendees;
    }

    /**
     *  The date the component was last modified
     */
    private $_lastmodified;

    /**
     *  Set the date the component was last modified
     *
     *  @access protected
     *  @param  int $m  UNIX timestamp representing when the component was last modified
     */
    protected function setLastModified($m) {
        if (ctype_digit($m)) {
            if ($m > 0) {
                $this->_lastmodified = $m;
            }
            else {
                trigger_error("The last modified value is less than 0", E_USER_WARNING);
            }
        }
        else {
            trigger_error("The last modified value ($m) is not an integer", E_USER_WARNING);
        }
    }

    /**
     *  Get the date the component was last modified
     *
     *  @return int UNIX timestamp of last modified date
     */
    protected function getLastModified() {
        return $this->_lastmodified;
    }

}


/**
 *  Abstract class for methods and variables common to Event, FreeBusy, Journal, and Todo components
 *
 *  @package iCal
 *  @author Gregory Szorc <gregory.szorc@case.edu>
 *  @abstract
 */
abstract class File_iCal_BaseComponent_EFJT extends File_iCal_BaseComponent {
    /**
     *  {@inheritdoc}
     *  @access public
     */
    public function addComment($c) {
        File_iCal_BaseComponent::addComment($c);
    }

    /**
     *  {@inheritdoc}
     *  @access public
     */
    public function getComments() {
        return File_iCal_BaseComponent::getComments();
    }

    /**
     *  Contacts for the component
     */
    protected $_contacts = array();

    /**
     *  Add a contact to the component
     *
     *  @access public
     *  @param  string  $c  Text to which to set the contact info
     */
    public function addContact($c) {
        if (!in_array($c, $this->_contacts)) {
            $this->_contacts[] = $c;
        }
        else {
            trigger_error("Attempting to add contact which already exists", E_USER_NOTICE);
        }
    }

    /**
     *  Get the array of contacts for this component
     *
     *  @access public
     *  @return array   Array of strings containing contact info
     */
    public function getContacts() {
        return $this->_contacts;
    }

    /**
     *  The organizer for this component
     *
     *  @val array
     */
    protected $_organizer = array();

    /**
     *  Set the organizer for this component
     *
     *  @access public
     *  @param  string  $o  The address for the organizer
     */
    public function setOrganizer($o) {
        $this->_organizer['address'] = $o;
    }

    /**
     *  Set the common name for the organizer
     *
     *  @access public
     *  @param  string  $cn Common name of the organizer
     */
    public function setOrganizerCommonName($cn) {
        $this->_organizer['cn'] = $cn;
    }

    /**
     *  Get the organizer for this component
     *
     *  @access public
     *  @return string  Address for the organizer
     */
    public function getOrganizer() {
        
        return isset($this->_organizer['address']) ? $this->_organizer['address'] : '';
    }

    /**
     *  Get the common name for the organizer
     *
     *  @return string  Common name for the organizer
     *  @access public
     */
    public function getOrganizerCommonName() {
        return $this->_organizer['cn'];
    }

    /**
     *  The URL for this component
     *
     *  @access protected
     *  @var    string
     */
    protected $_url;

    /**
     *  Set a URL for this component
     */
    public function setURL($url) {

    }

    /**
     *  Get the URL for this component
     */
    public function getURL() {

    }

    /**
     *  The unique identifier for the component
     */
    protected $_uid;

    /**
     *  Set the unique identifier for this component
     */
    public function setUID($uid) {
        $this->_uid = $uid;
    }

    /**
     *  Get the unique identifier for this component
     */
    public function getUID() {
        return $this->_uid;
    }

    /**
     *  The date/time that this component was created
     */
    protected $_dtstamp;

    /**
     *  Set the date/time that this component was created
     */
    public function setDateStamp($ds) {

    }

    /**
     *  Get the date/time that this component was created
     */
    public function getDateStamp() {

    }

    /**
     *  The request status of this component
     */
    protected $_requeststatus;

    /**
     *  Set the requestion status of this component
     */
    public function setRequestStatus($rs) {

    }

    /**
     *  Get the request status for this component
     */
    public function getRequestStatus() {

    }

}




/**
 *  This is a parent class defining methods to be inherited by VEVENT, VTODO, and VJOURNAL
 *
 *  @author  Gregory Szorc <gregory.szorc@case.edu>
 *  @package iCal
 *  @abstract
 */
 abstract class File_iCal_BaseComponent_EJT extends File_iCal_BaseComponent_EFJT {
    //make some methods public
    public function addAttachment($a) {
        File_iCal_BaseComponent::addAttachment($a);
    }

    public function getAttachments() {
        return File_iCal_BaseComponent::getAttachments();
    }

    public function setDescription($d) {
        File_iCal_BaseComponent::setDescription($d);
    }

    public function getDescription() {
        return File_iCal_BaseComponent::getDescription();
    }

    public function setSummary($s) {
        File_iCal_BaseComponent::setSummary($s);
    }

    public function getSummary() {
        return File_iCal_BaseComponent::getSummary();
    }


    public function addAttendee($a) { //File_iCal_Attendee 
        File_iCal_BaseComponent::addAttendee($a);
    }

    public function getAttendees() {
        return File_iCal_BaseComponent::getAttendees();
    }

    public function setLastModified($m) {
        File_iCal_BaseComponent::setLastModified($m);
    }

    public function getLastModified() {
        return File_iCal_BaseComponent::getLastModified();
    }

    /**
     *  Defines the categories for a given component
     *
     *  @var    array
     *  @access protected
     */
    protected $_categories = array();

    /**
     * Add a category to an event
     *
     * Adds a category with name $c to the event
     *
     *  @access public
     */
    public function addCategory($c) {
        if (!in_array($c, $this->_categories)) {
            $this->_categories[] = $c;
        }
    }

    /**
     * Returns an array of categories
     *
     *  @access public
     */
    public function getCategories() {
        return $this->_categories;
    }

    /**
     *  The classification of the component
     *
     *  @link   http://asg.web.cmu.edu/rfc/rfc2445.html#sec-4.8.1.3
     *  @var    string
     *  @access protected
     */
    protected $_classification;

    /**
     * Makes this event a public event
     *
     * Set this event to be viewable by anyone
     *  @access public
     */
    public function makePublic() {
        $this->_classification = File_iCal::ICAL_CLASSIFICATION_PUBLIC;
    }

    /**
     * Set this event private
     *
     *  @access public
     */
    public function makePrivate() {
        $this->_classification = File_iCal::ICAL_CLASSIFICATION_PRIVATE;
    }

    /**
     * Makes this event confidential
     *
     * Sets the event classification to be confidential
     *
     *  @access public
     */
    public function makeConfidential() {
        $this->_classification = File_iCal::ICAL_CLASSIFICATION_CONFIDENTIAL;
    }

    /**
     * Sets the event classification
     *
     * Sets the classification to any aribitrary value
     *
     *  @access public
     */
    public function setClassification($c) {
        $this->_class = $c;
    }

    /**
     * returns the classification of the event
     *
     *
     *  @access public
     */
    public function getClassification() {
        return $this->_class;
    }

    /**
     *  The overall status or confirmation for the calendar component
     *  @var    string
     *  @access protected
     */
    protected $_status;

    /**
     * Set teh status of the event to tentative
     *
     *
     */
    public function makeTentative() {
        $this->_status = ICAL_STATUS_TENTATIVE;
    }

    /**
     * Set the status of the event to confirmed
     *
     *
     */
    public function makeConfirmed() {
        $this->_status = ICAL_STATUS_CONFIRMED;
    }

    /**
     * Set the status of the event to cancelled
     *
     *
     */
    public function makeCancelled() {
        $this->_status = ICAL_STATUS_CANCELLED;
    }

    /**
     *  returns the status of the component
     *
     *
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     *  Identifies a specific instance of a reucrring component.
     *
     *  The value is the effective value of the DTSTART property of the recurrence instance
     *
     *  @var    int
     *  @access protected
     */
    protected $_recurid;

    /**
     *  Set the recurrence id of the component
     *
     *
     */
    public function setRecurrenceId($rid) {
        $this->_recurid = $rid;
    }

    /**
     *  Get the recurrence id of the component
     */
    public function getRecurrenceId() {
        return $this->_recurid;
    }

    protected $_related = array();

    /**
     *
     *
     *
     */
    public function addRelatedTo($rt) {
        if (!in_array($this->_related)) {
            $this->_related[] = $rt;
        }
    }

    public function getRelatedTo() {

    }

    /**
     *  When the component was created

     *  @link   http://asg.web.cmu.edu/rfc/rfc2445.html#sec-4.8.7.1
     *  @var    int
     *  @access protected
     */
    protected $_created;

    /**
     *
     *
     *  @access public
     */
    public function setCreated($c) {

    }

    /**
     *  Gets the created time of the component
     *  @access public
     */
    public function getCreated() {
        return $this->_created;
    }

    /**
     *  The revision sequence number of the calendar component within a sequence of revisions
     *  @var    int
     *  @access protected
     */
    protected $_sequence;

    /**
     *
     *
     *
     */
    public function setSequence($s) {
        $this->_sequence = $s;
    }

    /**
     *  Get the sequence of this component
     */
    public function getSequence() {
        return $this->_sequence;
    }




 }

/**
 *  This class defines methods for VEVENT and VTODO components
 *
 *  @author  Gregory Szorc <gregory.szorc@case.edu>
 *  @package iCal
 *  @abstract
 */
class File_iCal_BaseComponent_ET extends File_iCal_BaseComponent_EJT {
    function assignFrom($array) 
    {
        foreach($array as $k=>$v) {
            if (method_exists($this,'add'.$k)) {
                $this->{'add'.$k}($v);
            } else {
                $this->{'set'.$k}($v);
            }
        }
    
    }

    public function setDateStart($ds) 
    {
        parent::setDateStart($ds);
    }

    public function getDateStart() {
        return parent::getDateStart();
    }

    public function setDuration($d) {
        File_iCal_BaseComponent::setDuration($d);
    }

    public function getDuration() {
        return File_iCal_BaseComponent::getDuration();
    }

    /**
     *  Holds the geographic position of the component
     */
    protected $_latitude, $_longitude;

    /**
     *  Set the geographic position of the event
     *
     *
     */
    public function setPosition($latitude, $longitude) {
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $this->_latitude = $latitude;
            $this->_longitude = $longitude;
        }
    }

    /**
     * returns the latitude of the component
     */
    public function getLatitude() {
        return $this->_latitude;
    }

    /**
     * returns the longitude of the component
     */
    public function getLongitude() {
        return $this->_longitude;
    }


    /**
     *  The venue for a component
     *
     *  @var    string
     *  @access protected
     */
    protected $_location;

    /**
     * Define the location of a component
     *
     *
     */
    public function setLocation($l) {
        $this->_location = $l;
    }

    /**
     * Returns the location of a component
     *
     *
     */
    public function getLocation() {
        return $this->_location;
    }

    /**
     * Set the priority of the component to the highest possible
     *
     *
     */
    public function setPriorityHighest() {
        $this->_priority = ICAL_PRIORITY_HIGHEST;
    }

    /**
     * Set the priority to a medium level
     *
     *
     */
    public function setPriorityMedium() {
        $this->_priority = ICAL_PRIORITY_MEDIUM;
    }

    /**
     * Set the priority to the lowest possible
     *
     *
     */
    public function setPriorityLowest() {
        $this->_priority = ICAL_PRIORITY_LOWEST;
    }

    /**
     * Set the priority to an arbitrary value
     *
     * Priority must be between 0 and 9.  Other values are ignored
     */
    public function setPriority($p) {
        if ($p >= 0 && $p <= 9) {
            $this->_priority = $p;
        }
    }

    /**
     * returns the priority of a component as an integer
     *
     *
     */
    public function getPriority() {
        return $this->_priority;
    }

    /**
     *  Holds resources associated with component
     */
    protected $_resources = array();

    /**
     *  Add a resource to a component
     */
    public function addResource($r) {

    }

    /**
     *  Get the resources associated with a component
     */
    public function getResources() {

    }

}

?>