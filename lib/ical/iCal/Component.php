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
 *  iCal (RFC 2245) class definition
 *
 *  PHP version 5
 *
 *  @package    iCal
 *  @subpackage Parser
 *  @author     Gregory Szorc <gregory.szorc@case.edu>
 *  @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 */

 
 
/**
 *  iCal components when put together form an iCalendar.
 *
 *  File_iCal_Components are the representation of iCal components.  The base class is abstract
 *  and provides methods common to all types of components.
 *
 *  @author Gregory Szorc <gregory.szorc@case.edu>
 *  @package iCal
 *  @abstract
 */
abstract class File_iCal_Component {

    /**
     * The name for this component
     *
     * @access protected
     * @var string
     */
    protected $_name;

    /**
     * The properties associated with this component
     *
     * @access protected
     * @var array
     */
    protected $_properties = array();

    /**
     * Array of possible properties for this component
     *
     * @access protected
     * @var array
     */
    protected $_properties_possible = array();

    /**
     * Array of possible properties for this component that can only occur once
     *
     * @access protected
     * @var array
     */
    protected $_properties_single = array();

    /**
     * Array of possible properties for this component which can occur more than once
     *
     * @access protected
     * @var array
     */
    protected $_properties_multiple = array();

    /**
     * Array of arrays of properties which are exclusive to each other
     *
     * @access protected
     * @var array
     */
    protected $_properties_exclusive = array();

    /**
     * Creates a component
     *
     * @access  public
     * @param   array   Array of properties
     */
    public function __construct($s)
    {
        $this->_properties_possible = array_merge($this->_properties_single, $this->_properties_multiple);

        if (is_array($s)) {
            //verify integrity of the array
            foreach ($s as $v) {
                if (!is_a($v, "File_iCal_ContentLine")) {
                    trigger_error("Component constructor passed array does not contain all ContentLines", E_USER_ERROR);
                }
            }

            //all elements are ConentLines

            //verify first and last lines are BEGIN and END, respectively
            if (($s[0]->name() == "BEGIN") && ($s[count($s)-1]->name() == "END")) {
                $this->_name = $s[0]->value();

                //go through and create the properties
                for ($i = 1; $i < count($s) - 1; $i++) {
                    $name = strtoupper($s[$i]->name());

                    if (in_array($name, $this->_properties_possible)) {
                        //now we know we have a good property

                        //easiest case is to add it to an array
                        if (in_array($name, $this->_properties_multiple)) {
                            $prop = $s[$i]->getProperty();
                            self::addProperty($prop, true);

                        }
                        else if (in_array($name, $this->_properties_single)) {
                            $check_conflict = false;
                            foreach ($this->_properties_exclusive as $pe) {
                                if (is_array($pe) && in_array($name, $pe)) $check_conflict = true;
                            }

                            if ($check_conflict) {
                                $is_conflict = false;
                                foreach ($this->_properties_exclusive as $pe) {

                                    foreach ($pe as $name_conflict) {
                                        if (array_key_exists($name_conflict, $this->_properties)) {
                                            trigger_error("A property conflicts with another!  The iCal file is not valid", E_USER_WARNING);
                                            $is_conflict = true;
                                        }
                                    }
                                }

                                if (!$is_conflict) {
                                    self::addProperty($s[$i]->getProperty(), false);
                                }

                            }
                            else {
                                $prop = $s[$i]->getProperty();
                                self::addProperty($prop, false);

                            }
                        }
                        else {
                            trigger_error("Not sure what to do with property", E_USER_WARNING);
                        }

                    }
                    else {
                        //trigger_error("Unexpected property in component.  Found $name", E_USER_WARNING);
                    }

                }

            }
            else {
                trigger_error("Component array must start and end with BEGIN and END tags", E_USER_ERROR);
            }


        }
        else {
            //trigger_error("Component constructor expects an array", E_USER_ERROR);
        }

    }

    /**
     *  Add a property to the current component
     *
     *  @access protected
     *  @param  File_iCal_Property  $property   The property to add to this component
     *  @param  bool                $multiple   Whether this is a multiple property
     */
    protected function addProperty($property, $multiple = false) { //File_iCal_Property 
        $name = $property->getName();

        if (!array_key_exists($name, $this->_properties)) {
            if ($multiple) {
                $this->_properties[$name] = array();
                $this->_properties[$name][] = $property;
                return;
            }
            else {
                $this->_properties[$name] = $property;
                return;
            }
        }
        else if ($multiple) {
            $this->_properties[$name][] = $property;
            return;
        }
        else {
            //the logic before addProperty should have prevented this from happening...
            trigger_error("Trying to insert a property (".$name.") that already exists.  This function should never have been called", E_USER_ERROR);
        }

    }


    /**
     *  Get all of the properties associated with this component
     *
     *  @access public
     *  @return array   An array of File_iCal_Property objects
     */
    public function getPropertyArray() {
        $r = array();
        require_once 'File/iCal/Property.php';
        $r[] = File_iCal_Property::getProperty("BEGIN", array(), $this->_name);

        foreach ($this->_properties as $prop) {
            if (is_array($prop)) {
                foreach ($prop as $propv) {
                    $r[] = $propv;
                }
            }
            else {
                $r[] = $prop;
            }
        }

        $r[] = File_iCal_Property::getProperty("END", array(), $this->_name);

        return $r;

    }

    /**
     *  get an array of contentLines for this component
     *
     *  @access public
     *  @return array   Array of File_iCal_ContentLine objects
     */
    public function getCalendar()
    {
            $r = array();
            require_once 'File/iCal/ContentLine.php';
            $r[] = new File_iCal_ContentLine("BEGIN:".$this->_name);

            foreach ($this->_properties as $prop) {
                if (is_array($prop)) {
                    foreach ($prop as $propv) {
                        if (is_a($propv, "File_iCal_Property")) {
                            //$r[] = $propv->getName().':'.$propv->getValue();
                            $r[] = new File_iCal_ContentLine($propv);
                        }
                    }
                }
                else {
                    if (is_a($prop, "File_iCal_Property")) {
                        //$r[] = $prop->getName().':'.$prop->getValue();
                        $r[] = new File_iCal_ContentLine($prop);
                    }
                }
            }

            $r[] = new File_iCal_ContentLine("END:".$this->_name);

            return $r;
    }

    /**
     *  See if a property exists
     *
     *  @access protected
     *  @return int The number of occurences
     *  @param  string  $name   The name of the property
     *  @param  int     $key    By Reference: will be set to an integer or array of intergers.
     *                          The values represent the internal array keys for properties of this type.
     */
    protected function propertyExists($name, &$key = null) {
        require_once 'File/iCal/Property.php';
        $pn = File_iCal_Property::getClassName($name);

        $count = 0;
        $keys = array();

        foreach ($this->_properties as $k=>$p) {
            if (is_a($p, $pn)) {
                $count++;
                $keys[] = $k;
            }
        }

        if (count($keys) == 1) {
            $key = $keys[0];
        }
        else {
            $key = $keys;
        }

        return $count;

    }

    /**
     *  Add a property value to an existing property if it exists
     *
     *  @access protected
     *  @param  string  $name           The name of the property to which to add the value
     *  @param  string  $value          The value to add to the property
     *  @param  bool    $match_param    If multiple properties with the name are present, we don't know for sure to which to add the new value
     *                                  If set to true, the function will check for matching parameters when selecting the property to which to add
     */
    protected function addPropertyValue($name, $value, $match_param = null) {
        require_once 'File/iCal/Property.php';
        if (in_array($name, $this->_properties_possible)) {
            $pn = File_iCal_Property::getClassName($name);

            if (in_array($name, $this->_properties_single)) {

                foreach ($this->_properties as $p) {
                    if (is_a($p, $pn)) {
                        return $p->addValue($value);
                    }
                }

            }
            else if (in_array($name, $this->_properties_multiple)) {
                foreach ($this->_properties as $p) {
                    if (is_a($p, $pn)) {
                        //match the $match_param here
                        //ignore for now
                        return $p->addValue($value);
                    }
                }
            }
        }
        else {
            trigger_error("The property $name is not allowed for component ".$this->_name, E_USER_WARNING);
        }
    }

    /**
     *  Returns a new component for a passed File_iCal_{Event, Alarm, FreeBusy, TimeZone, ToDo}
     *
     *  @access  public
     *  @static
     *  @param  object  $e  The event for which you wish to return a component object
     *  @return File_iCal_Component_Event   The new component object
     */
    public static function getComponent($e) {
        //ideally we call the component constructor with an array of contentlines
        $props = array();
        $component_name = "";

        //print_r($e);

        switch (get_class($e)) {
            case "File_iCal_Alarm":
                $component_name = "VALARM";
                break;

            case "File_iCal_Event":
                $component_name = "VEVENT";
                break;

            case "File_iCal_FreeBusy":
                $component_name = "VFREEBUSY";
                break;

            case "File_iCal_TimeZone":
                $component_name = "VTIMEZONE";
                break;

            case "File_iCal_ToDo":
                $component_name = "VTODO";
                break;

            case "File_iCal_Journal":
                $component_name = "VJOURNAL";
                break;

            default:
                trigger_error("Passed object not valid type (is a ".get_class($e).")", E_USER_WARNING);


        }
        require_once 'File/iCal/Property.php';
        $props[] = File_iCal_Property::getProperty("BEGIN", array(), $component_name);

        //attachment is defined in the basecomponent class, so method_exists is always true
        if (is_subclass_of($e, "File_iCal_BaseComponent_EJT") || is_a($e, "File_iCal_Alarm")) {

        }

                //needs to be finished
        if (is_subclass_of($e, "File_iCal_BaseComponent_EFJT") || is_a($e, "File_iCal_TimeZone")) {
                foreach ($e->getComments() as $c) {

                }

        }

        //this method is defined in the BaseComponent class, so it always exists
        //method should only be public for AEJT
        if (is_subclass_of($e, "File_iCal_BaseComponent_EJT") || is_a($e, "File_iCal_Alarm")) {
                if ($description = $e->getDescription()) {
                    $props[] = File_iCal_Property::getProperty("DESCRIPTION", array(), $description);
                }
        }

        if (is_subclass_of($e, "File_iCal_BaseComponent_EJT") || is_a($e, "File_iCal_Alarm")) {
            if ($summary = $e->getSummary()) {
                $props[] = File_iCal_Property::getProperty("SUMMARY", array(), $summary);
            }
        }

        if (is_a($e, "File_iCal_Event") || is_a($e, "File_iCal_FreeBusy")) {
           
                        if (($de = $e->getDateEnd()) > 0) {
                                $props[] = File_iCal_Property::getProperty("DTEND", array(), $de);
                                
                        }

        }
        
        
        if (is_subclass_of($e, "File_iCal_BaseComponent_EFJT")) {
            if (($ds = $e->getDateStart()) > 0) {
                    $props[] = File_iCal_Property::getProperty("DTSTART", array(), $ds);
                    
            }
        }

        if (is_subclass_of($e, "File_iCal_BaseComponent_ET") || is_a($e, "File_iCal_Alarm") || is_a($e, "File_iCal_FreeBusy")) {
                        if (($dur = $e->getDuration()) > 0) {
                                $props[] = File_iCal_Property::getProperty("DURATION", array(), $dur);
                        }
        }

                //should be changed
        if (method_exists($e, "getAttendees")) {

        }

                if (is_subclass_of($e, "File_iCal_BaseComponent_EJT") || is_a($e, "File_iCal_TimeZone")) {

        }

        if (method_exists($e, "getContacts")) {

        }

        if (method_exists($e, "getOrganizer")) {
                        if ($o = $e->getOrganizer()) {
                                $props[] = File_iCal_Property::getProperty("ORGANIZER", array(), $o);
                        }
        }

        if (method_exists($e, "getURL")) {
                        if ($url = $e->getURL()) {
                                $props[] = File_iCal_Property::getProperty("URL", array(), $url);
                        }
        }

        if (method_exists($e, "getUID")) {
            if ($uid = $e->getUID()) {
                $props[] = File_iCal_Property::getProperty("UID", array(), $uid);
            }
        }

        if (method_exists($e, "getDateStamp")) {
                        if ($ds = $e->getDateStamp()) {
                                $props[] = File_iCal_Property::getProperty("DTSTAMP", array(), $uid);
                        }
        }

        if (method_exists($e, "getRequestStatus")) {

        }

        if (method_exists($e, "getCategories")) {

        }

        if (method_exists($e, "getClassification")) {

        }

        if (method_exists($e, "getStatus")) {

        }

        if (method_exists($e, "getRecurrenceId")) {

        }

        if (method_exists($e, "getRelatedTo")) {

        }

        if (method_exists($e, "getCreated")) {

        }

        if (method_exists($e, "getSequence")) {
            if ($sequence = $e->getSequence()) {
                $props[] = File_iCal_Property::getProperty("SEQUENCE", array(), $sequence);
            }

        }

        if (method_exists($e, "getLatitude")) {

        }

        if (method_exists($e, "getLongitude")) {

        }

        if (method_exists($e, "getLocation")) {
            if ($location = $e->getLocation()) {
                $props[] = File_iCal_Property::getLocation("LOCATION", array(), $location);
            }
        }

        if (method_exists($e, "getPriority")) {

        }

        if (method_exists($e, "getResources")) {

        }

        if (method_exists($e, "getTransparency")) {

        }

        $props[] = File_iCal_Property::getProperty("END", array(), $component_name);


        //convert these properties to contentlines
        require_once 'File/iCal/ContentLine.php';
        foreach ($props as $k=>$p) {
            $props[$k] = new File_iCal_ContentLine($p);
        }
        //echo '<PRE>';print_R(array($e, $component_name,$props));
        
        switch ($component_name) {
            case "VEVENT":
                return new File_iCal_Component_Event($props);
                break;

            case "VTODO":
                return new File_iCal_Component_Todo($props);
                break;

            case "VTIMEZONE":
                return new File_iCal_Component_Timezone($props);
                break;

            case "VALARM":
                return new File_iCal_Component_Alarm($props);
                break;

            case "VFREEBUSY":
                return new File_iCal_Component_FreeBusy($props);
                break;

            case "VJOURNAL":
                return new File_iCal_Component_Journal($props);
                break;

            default:
                trigger_error("Cannot yet retrieve component");
        }

    }

    /**
     *  Returns the user-equivalent object for a parsed component
     *
     *  Depending on the component type, this function could return one of the following:
     *  File_iCal_Event, File_iCalAlarm, File_iCal_FreeBusy, File_iCal_TimeZone, File_iCal_TodDo
     *
     *  @access public
     *  @return object
     */
    public function getUserComponent() {
        $component_name = "";
        $c = null;

        switch (get_class($this)) {
            case "File_iCal_Component_Event":
                $component_name = "VEVENT";
                require_once 'File/iCal/Event.php';
                $c = new File_iCal_Event();
                break;

            case "File_iCal_Component_ToDo":
                $component_name = "VTODO";
                $c = new File_iCal_ToDo();
                break;

            case "File_iCal_Component_Journal":
                $component_name = "VJOURNAL";
                $c = new File_iCal_Journal();
                break;

            case "File_iCal_Component_Alarm":
                $component_name = "VALARM";
                $c = new File_iCal_Alarm();
                break;

            case "File_iCal_Component_FreeBusy":
                $component_name = "VFREEBUSY";
                $c = new File_iCal_FreeBusy();
                break;

            case "File_iCal_Component_Timezone":
                $component_name = "VTIMEZONE";
                $c = new File_iCal_TimeZone();
                break;

            default:
                trigger_error("Component not yet supported (". get_class($this).")", E_USER_WARNING);
        }

        foreach ($this->_properties as $p) {
                        if (is_array($p)) {
                                foreach ($p as $p2) {
                                        self::addPropertyToUserComponent($c, $p2);
                                }
                        }
                        else {
                                self::addPropertyToUserComponent($c, $p);
                        }
        }


        return $c;

    }

        protected static function addPropertyToUserComponent(&$component, $property) {
                switch (get_class($property)) {
                        case "File_iCal_Property_Attachment":
                        case "File_iCal_Property_Category":
                        case "File_iCal_Property_Classification":
                        case "File_iCal_Property_Comment":
                                break;

                        case "File_iCal_Property_Description":
                                $component->setDescription($property->getValue()->getValue());
                                break;

                        case "File_iCal_Property_GeographicPosition":
                                break;

                        case "File_iCal_Property_Location":
                                $component->setLocation($property->getValue()->getValue());
                                break;
                        case "File_iCal_Property_PercentComplete":
                        case "File_iCal_Property_Priority":
                        case "File_iCal_Property_Resources":
                        case "File_iCal_Property_Status":
                                break;

                        case "File_iCal_Property_Summary":
                                $component->setSummary($property->getValue()->getValue());
                                break;

                        case "File_iCal_Property_DateTimeCompleted":
                        case "File_iCal_Property_DateTimeEnd":
                        case "File_iCal_Property_DateTimeDue":
                                break;

                        case "File_iCal_Property_DateTimeStart":
                                $component->setDateStart($property->getValue()->getIntegerValue());
                                break;

                        case "File_iCal_Property_Duration":
                        case "File_iCal_Property_FreeBusyTime":
                        case "File_iCal_Property_TimeTransparency":
                        case "File_iCal_Property_TimeZoneIdentifier":
                        case "File_iCal_Property_TimeZoneName":
                        case "File_iCal_Property_TimeZoneOffsetFrom":
                        case "File_iCal_Property_TimeZoneOffsetTo":
                        case "File_iCal_Property_TimeZoneURL":
                        case "File_iCal_Property_Attendee":
                        case "File_iCal_Property_Contact":
                        case "File_iCal_Property_Organizer":
                        case "File_iCal_Property_RecurrenceID":
                        case "File_iCal_Property_RelatedTo":
                        case "File_iCal_Property_URL":

                                break;
                        case "File_iCal_Property_UID":
                                $component->setUID($property->getValue()->getValue());
                                break;

                        case "File_iCal_Property_ExceptionDateTimes":
                        case "File_iCal_Property_ExceptionRule":
                        case "File_iCal_Property_RecurrenceDateTimes":
                        case "File_iCal_Property_RecurrenceRule":
                        case "File_iCal_Property_Action":
                        case "File_iCal_Property_RepeatCount":
                        case "File_iCal_Property_Trigger":
                        case "File_iCal_Property_DateTimeCreated":
                        case "File_iCal_Property_DateTimeStamp":
                        case "File_iCal_Property_LastModified":
                        case "File_iCal_Property_SequenceNumber":
                        case "File_iCal_Property_RequestStatus":
                                break;

                        default:
                                trigger_error("Uknown property ".get_class($property), E_USER_WARNING);
                }
        }



}

/**
 * Implements the VALARM component
 *
 * @package iCal
 */
class File_iCal_Component_Alarm extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "ACTION",
        "TRIGGER",
        "DURATION",
        "REPEAT",
        "ATTACH"
        );

    /**
     *  Defines the properties that may occur more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "X-*"
    );


    /**
     *  Create a new VALARM component
     *
     *  @access public
     *  @param  array   $a  An array of File_iCal_Property objects
     */
    public function __construct($s) {

        parent::__construct($s);
        $this->_name = "VALARM";

    }

}

/**
 * Implements the VEVENT component
 *
 * @package iCal
 */
class File_iCal_Component_Event extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "CLASS",
        "CREATED",
        "DESCRIPTION",
        "DTSTART",
        "GEO",
        "LAST-MODIFIED",
        "LOCATION",
        "ORGANIZER",
        "PRIORITY",
        "DTSTAMP",
        "SEQUENCE",
        "STATUS",
        "SUMMARY",
        "TRANSP",
        "UID",
        "URL",
        "RECURRENCE-ID",
        "DTEND",
        "DURATION"
        );

    /**
     *  Defines the properties that are exclusive
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_exclusive = array(
        array("DTEND","DURATION")
    );

    /**
     *  Defines the properties that may more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "ATTACH",
        "ATTENDEE",
        "CATEGORIES",
        "COMMENT",
        "CONTACT",
        "EXDATE",
        "EXRULE",
        "REQUEST-STATUS",
        "RELATED-TO",
        "RESOURCES",
        "RDATE",
        "RRULE",
        "X-*"
    );

    /**
     *  Create a new VEVENT component
     *
     *  @access public
     *  @param  array   $s  An array of File_iCal_Property objects
     */
    public function __construct($s) {
        parent::__construct($s);
        $this->_name = "VEVENT";

    }

}

/**
 * Implements the FREEBUSY component
 *
 * @package iCal
 */
class File_iCal_Component_FreeBusy extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "CONTACT",
        "DTSTART",
        "DTEND",
        "DURATION",
        "DTSTAMP",
        "ORGANIZER",
        "UID",
        "URL"
        );

    /**
     *  Defines the properties that may occur more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "ATTENDEE",
        "COMMENT",
        "FREEBUSY",
        "RSTATUS",
        "X-*"
    );


    /**
     *  Create a new Freebusy component
     *
     *  @access public
     *  @param    array   $s    Array of File_iCal_Property objects
     */
    public function __construct($s) {
        parent::__construct($s);
        $this->_name = "VFREEBUSY";

    }

}

/**
 * Implements the VJOURNAL component
 *
 * @package iCal
 */
class File_iCal_Component_Journal extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "CLASS",
        "CREATED",
        "DESCRIPTION",
        "DTSTART",
        "DTSTAMP",
        "LAST-MODIFIED",
        "ORGANIZER",
        "RECURRENCE-ID",
        "SEQUENCE",
        "STATUS",
        "SUMMARY",
        "UID",
        "URL"
        );

    /**
     *  Defines the properties that may occur more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "ATTACH",
        "ATTENDEE",
        "CATEGORIES",
        "COMMENT",
        "CONTACT",
        "EXDATE",
        "EXRULE",
        "RELATED-TO",
        "RESOURCES",
        "RDATE",
        "RRULE",
        "RSTATUS",
        "X-*"
    );


    /**
     *  Create a new VJOURNAL component
     *
     *  @access public
     *  @param  array   $s  Array of File_iCal_Property objects
     */
    public function __construct($s) {
         parent::__construct($s);
         $this->_name = "VJOURNAL";

    }

}

/**
 * Implements the VTIMEZONE component
 *
 * @package iCal
 */
class File_iCal_Component_Timezone extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "TZID",
        "LAST-MODIFIED",
        "TZURL"
        );

    /**
     *  Defines the properties that may more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "STANDARDC",
        "DAYLIGHTC",
        "X-*"
    );


    /**
     *  Create a new VTIMEZONE component
     *
     *  @access public
     *  @param  array   $s  array of File_iCal_Proeperty objects
     */
    public function __construct($s) {
        parent::__construct($s);
        $this->_name = "VTIMEZONE";

    }

}

/**
 * Implements the VTODO component
 *
 * @package iCal
 */
class File_iCal_Component_ToDo extends File_iCal_Component
{
    /**
     *  Defines the properties that may only occur once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_single = array(
        "CLASS",
        "COMPLETED",
        "CREATED",
        "DESCRIPTION",
        "DTSTAMP",
        "DTSTART",
        "GEO",
        "LAST-MODIFIED",
        "LOCATION",
        "ORGANIZER",
        "PERCENT-COMPLETE",
        "PRIORITY",
        "RECURRENCE-ID",
        "SEQUENCE",
        "STATUS",
        "SUMMARY",
        "UID",
        "URL",
        "DUE",
        "DURATION"
    );

    /**
     *  Defines the properties that are exclusive to one another
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_exclusive = array(
        array("DUE","DURATION")
    );

    /**
     *  Defines the properties that may occur more than once
     *
     *  @access protected
     *  @var    array   Array of property names
     */
    protected $_properties_multiple = array(
        "ATTACH",
        "ATTENDEE",
        "CATEGORIES",
        "COMMENT",
        "CONTACT",
        "EXDATE",
        "EXRULE",
        "REQUEST-STATUS",
        "RELATED-TO",
        "RESOURCES",
        "RDATE",
        "RRULE",
        "X-*"
    );

    /**
     *  Create a new VTODO component
     *
     *  @access public
     *  @param    array   $s    Array of File_iCal_Property objects
     */
    public function __construct($s) {
        parent::__construct($s);
        $this->_name = "VTODO";

    }
}

?>
