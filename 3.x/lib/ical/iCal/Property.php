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
 * Properties store data in File_iCal_ValueDataType objects, so we need to include them
 */
require_once('ValueDataType.php');

/**
 * Properties make up the components of iCal files
 *
 * Every line in an iCal file is basically a property.  Properties consist of
 * a name, optional parameters, and value(s).
 *
 * The base property class is abstract because every property type is different.  If new properties
 * are to be defined, they should derive from this base class for compatibility with the parser.
 *
 * @category File
 * @package iCal
 */
abstract class File_iCal_Property
{
    protected $_name;
    protected $_params = array();
    protected $_values = array();

    protected $_parameters_allowed = array(); //list of allowed parameters
    protected $_parameters_single = array();  //parameters only allowed once
    protected $_parameters_multiple = array();//parameters allowed multiple times
    protected $_parameters_exclusive = array(); //array of arrays containing parameters that can't coexist
    protected $_parameters_joint = array(); //array of arrays containing parameters that must coexist
    protected $_parameters_required = array();

    protected $_value_separator;

    public function __construct($valuestring, $param_array)
    {
        $this->_parameters_allowed = array_merge($this->_parameters_single, $this->_parameters_multiple);

        if (is_array($param_array)) {
            foreach ($param_array as $p) {
                if (is_a($p, "File_iCal_Parameter")) {
                    if (in_array($p->getName(), $this->_parameters_allowed)) {
                        $this->_params[] = $p;
                    }
                    else {
                        trigger_error("The parameter ".$p->getName()." is not allowed in this property type\n", E_USER_WARNING);
                    }

                }
                else {
                    trigger_error("Parameter passed to property is not a parameter object", E_USER_ERROR);
                }
            }
        }
        else {
            trigger_error("2nd parameter of property constructor must be an array", E_USER_ERROR);
        }

    }

    /*
     * Returns the value of the first value
     *
     * Can be used as a shortcut when you don't want to deal with multiple values or parameters
     */
    public function getValue() {
        return $this->_values[0];
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function getParams() {
        return $this->_params;
    }

    public function getName() {
        return $this->_name;
    }

    public function getLine() {
        $cl = new File_iCal_ContentLine($this);

        $r = $cl->getLine();
        unset($cl);

        return $r;
    }

    public function getValueSeparator() {
        return $this->_value_separator;
    }


    //this should be overwrote in child classes, if necessary
    public function addValue($v) {
        $return = false;

        switch (gettype($v)) {
            case "string":

                $this->_values[] = new File_iCal_ValueDataType_Text($v);
                $return = true;
                break;

            default:

        }

        return $return;

    }


    static public function getClassName($name) {
        $name = strtoupper($name);

        switch ($name) {
            case "PRODID":
                return "File_iCal_Property_ProductIdentifier";

            case "VERSION":
                return "File_iCal_Property_Version";

            case "CALSCALE":
                return "File_iCal_Property_Calscale";

            case "METHOD":
                return "File_iCal_Property_Method";

            case "SUMMARY":
                return "File_iCal_Property_Summary";

            case "LOCATION":
                return "File_iCal_Property_Location";

            case "DTSTART":
                return "File_iCal_Property_DateTimeStart";

            case "UID":
                return "File_iCal_Property_UID";

            case "DTSTAMP":
                return "File_iCal_Property_DateTimeStamp";

            case "COMPLETED":
                return "File_iCal_Property_DateTimeCompleted";

            case "SEQUENCE":
                return "File_iCal_Property_SequenceNumber";

            case "STATUS":
                return "File_iCal_Property_Status";

            case "DESCRIPTION":
                return "File_iCal_Property_Description";

            case "EXDATE":
                return "File_iCal_Property_ExceptionDateTimes";

            case "RRULE":
                return "File_iCal_Property_RecurrenceRule";

            case "TZID":
                return "File_iCal_Property_TimeZoneIdentifier";

            case "LAST-MODIFIED":
                return "File_iCal_Property_LastModified";

            case "RECURRENCE-ID":
                return "File_iCal_Property_RecurrenceID";

            case "ATTACH":
                return "File_iCal_Property_Attachment";

            case "CATEGORIES":
                return "File_iCal_Property_Categories";

            case "CLASS":
                return "File_iCal_Property_Classification";

            case "COMMENT":
                return "File_iCal_Property_Comment";

            case "GEO":
                return "File_iCal_Property_GeographicPosition";

            case "PERCENT-COMPLETE":
                return "File_iCal_Property_PercentComplete";

            case "PRIORITY":
                return "File_iCal_Property_Priority";

            case "RESOURCES":
                return "File_iCal_Property_Resources";

            case "DTEND":
                return "File_iCal_Property_DateTimeEnd";

            case "DUE":
                return "File_iCal_Property_DateTimeDue";

            case "DURATION":
                return "File_iCal_Property_Duration";

            case "FREEBUSY":
                return "File_iCal_Property_FreeBusyTime";

            case "TRANSP":
                return "File_iCal_Property_TimeTransparency";

            case "TZNAME":
                return "File_iCal_Property_TimeZoneName";

            case "TZOFFSETFROM":
                return "File_iCal_Property_TimeZoneOffsetFrom";

            case "TZOFFSETTO":
                return "File_iCal_Property_TimeZoneOffsetTo";

            case "TZURL":
                return "File_iCal_Property_TimeZoneURL";

            case "ATTENDEE":
                return "File_iCal_Property_Attendee";

            case "CONTACT":
                return "File_iCal_Property_Contact";

            case "ORGANIZER":
                return "File_iCal_Property_Organizer";

            case "RELATED-TO":
                return "File_iCal_Property_RelatedTo";

            case "URL":
                return "File_iCal_Property_URL";

            case "EXRULE":
                return "File_iCal_Property_ExceptionRule";

            case "RDATE":
                return "File_iCal_Property_RecurrenceDateTimes";

            case "ACTION":
                return "File_iCal_Property_Action";

            case "REPEAT":
                return "File_iCal_Property_RepeatCount";

            case "TRIGGER":
                return "File_iCal_Property_Trigger";

            case "CREATED":
                return "File_iCal_Property_DateTimeCreated";

            case "REQUEST-STATUS":
                return "File_iCal_Property_RequestStatus";

            default:
                trigger_error("Unknown propert name specified", E_USER_WARNING);

        }

    }


    static public function getProperty($name, $params, $value) {
            //internally, we have already checked validity of the contentline
            $name = strtoupper($name);

            switch ($name) {
                case "BEGIN":
                case "END":
                    return new File_iCal_Property_NonStandard($value, $params, $name);
                case "PRODID":
                    return new File_iCal_Property_ProductIdentifier($value, $params);
		case "X-WR-CALNAME":
		    return new File_iCal_Property_NonStandard($value, $params,$name);
                case "VERSION":
                    return new File_iCal_Property_Version($value, $params);

                case "CALSCALE":
                    return new File_iCal_Property_CalendarScale($value, $params);

                case "METHOD":
                    return new File_iCal_Property_Method($value, $params);

                case "SUMMARY":
                    return new File_iCal_Property_Summary($value, $params);

                case "LOCATION":
                    return new File_iCal_Property_Location($value, $params);

                case "DTSTART":
                    return new File_iCal_Property_DateTimeStart($value, $params);
		
		case "X-WR-RELCALID":
		    return new File_iCal_Property_NonStandard($value, $params,$name);
                
		case "UID":
                    return new File_iCal_Property_UID($value, $params);

                case "DTSTAMP":
                    return new File_iCal_Property_DateTimeStamp($value, $params);

                case "COMPLETED":
                    return new File_iCal_Property_DateTimeCompleted($value, $params);

                case "SEQUENCE":
                    return new File_iCal_Property_SequenceNumber($value, $params);

                case "STATUS":
                    return new File_iCal_Property_Status($value, $params);

                case "DESCRIPTION":
                    return new File_iCal_Property_Description($value, $params);

                case "EXDATE":
                    return new File_iCal_Property_ExceptionDateTimes($value, $params);

                case "RRULE":
                    return new File_iCal_Property_RecurrenceRule($value, $params);
		case "X-WR-TIMEZONE":
		    return new File_iCal_Property_NonStandard($value, $params,$name);
                case "TZID":
                    return new File_iCal_Property_TimeZoneIdentifier($value, $params);

                case "LAST-MODIFIED":
                    return new File_iCal_Property_LastModified($value, $params);

                case "RECURRENCE-ID":
                    return new File_iCal_Property_RecurrenceID($value, $params);

                case "ATTACH":
                    return new File_iCal_Property_Attachment($value, $params);

                case "CATEGORIES":
                    return new File_iCal_Property_Categories($value, $params);

                case "CLASS":
                    return new File_iCal_Property_Classification($value, $params);

                case "COMMENT":
                    return new File_iCal_Property_Comment($value, $params);

                case "GEO":
                    return new File_iCal_Property_GeographicPosition($value, $params);

                case "PERCENT-COMPLETE":
                    return new File_iCal_Property_PercentComplete($value, $params);

                case "PRIORITY":
                    return new File_iCal_Property_Priority($value, $params);

                case "RESOURCES":
                    return new File_iCal_Property_Resources($value, $params);

                case "DTEND":
                    return new File_iCal_Property_DateTimeEnd($value, $params);

                case "DUE":
                    return new File_iCal_Property_DateTimeDue($value, $params);

                case "DURATION":
                    return new File_iCal_Property_Duration($value, $params);

                case "FREEBUSY":
                    return new File_iCal_Property_FreeBusyTime($value, $params);

                case "TRANSP":
                    return new File_iCal_Property_TimeTransparency($value, $params);

                case "TZNAME":
                    return new File_iCal_Property_TimeZoneName($value, $params);

                case "TZOFFSETFROM":
                    return new File_iCal_Property_TimeZoneOffsetFrom($value, $params);

                case "TZOFFSETTO":
                    return new File_iCal_Property_TimeZoneOffsetTo($value, $params);

                case "TZURL":
                    return new File_iCal_Property_TimeZoneURL($value, $params);

                case "ATTENDEE":
                    return new File_iCal_Property_Attendee($value, $params);

                case "CONTACT":
                    return new File_iCal_Property_Contact($value, $params);

                case "ORGANIZER":
                    return new File_iCal_Property_Organizer($value, $params);

                case "RELATED-TO":
                    return new File_iCal_Property_RelatedTo($value, $params);

                case "URL":
                    return new File_iCal_Property_URL($value, $params);

                case "EXRULE":
                    return new File_iCal_Property_ExceptionRule($value, $params);

                case "RDATE":
                    return new File_iCal_Property_RecurrenceDateTimes($value, $params);

                case "ACTION":
                    return new File_iCal_Property_Action($value, $params);

                case "REPEAT":
                    return new File_iCal_Property_RepeatCount($value, $params);

                case "TRIGGER":
                    return new File_iCal_Property_Trigger($value, $params);

                case "CREATED":
                    return new File_iCal_Property_DateTimeCreated($value, $params);

                case "REQUEST-STATUS":
                    return new File_iCal_Property_RequestStatus($value, $params);

                default:

                    echo "Cannot get $name property yet\n";

            };
        }
}

/**
 * Implements a calendar scale property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_CalendarScale extends File_iCal_Property
{
    public function __construct($value, $params) {
        parent::__construct($value, $params);

        $this->_name = "CALSCALE";


        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }
}

/**
 * Implements the method property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Method extends File_iCal_Property
{
    public function __construct($value, $params) {
        parent::__construct($value, $params);
        $this->_name = "METHOD";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the product identifier property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_ProductIdentifier extends File_iCal_Property
{
    public function __construct($value, $params) {
        parent::__construct($value, $params);
        $this->_name = "PRODID";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }


}

/**
 * Implements the version property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Version extends File_iCal_Property
{
    public function __construct($value, $params) {
        parent::__construct($value, $params);
        $this->_name = "VERSION";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements a non-standard property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_NonStandard extends File_iCal_Property
{
    public function __construct($value, $params, $name) {
        parent::__construct($value, $params);
        $this->_name = $name;
        $this->_values[] = new File_iCal_ValueDataType_Text($value);

    }

    public function getValue() {
        return $this->_values[0]->getValue();
    }

}


/**
 * Implements an attachment property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Attachment extends File_iCal_Property
{
    protected $_parameters_single = array("ENCODING", "VALUE", "FMTTYPE");
    protected $_parameters_multiple = array("X-");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "ATTACH";

        $binary = false;

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Paramter_Value")) {
                if ($p->getValueString() == "BINARY") {
                    $binary = true;
                }
            }
        }

        if ($binary) {
            $this->_values[] = new File_iCal_ValueDataType_Binary($value);
        }
        else {
            $this->_values[] = new File_iCal_ValueDataType_URI($value);
        }

    }

}

/**
 * Implements a category property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Categories extends File_iCal_Property
{
    protected $_parameters_single = array("LANGUAGE");
    protected $_parameters_multiple = array("X-");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "CATEGORIES";

        //there can be multiple categories specified!!!
        $this->_values[] = new File_iCal_ValueDataType_Text($value);

    }
}

/**
 * Implements the classification property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Classification extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "CLASS";

        switch (strtoupper($value))
        {
            case "PUBLIC":
            case "PRIVATE":
            case "CONFIDENTIAL":
                $this->_values[] = new File_iCal_ValueDataType_Text($value);
                break;

            default:
               trigger_error("Classification property encountered an unknown value", E_USER_WARNING);
        }


    }


}


/**
 * Implements the comment property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Comment extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "COMMENT";
        $this->_values[] = new File_iCal_ValueDataType_Text($value);

    }


}

/**
 * Implements the description property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Description extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DESCRIPTION";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the geographic position property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_GeographicPosition extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "GEO";

        trigger_error("GEO property is not yet supported", E_USER_WARNING);
    }

}

/**
 * Implements the location property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Location extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "LOCATION";

        $v = $value;
        $this->_values[] = new File_iCal_ValueDataType_Text($v);

    }
}

/**
 * Implements the percent complete property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_PercentComplete extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "PERCENT-COMPLETE";

        $this->_values[] = new File_iCal_ValueDataType_Integer($v);
    }

}

/**
 * Implements the priority property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Priority extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "PRIORITY";

        $this->_values[] = new File_iCal_ValueDataType_Integer($value);
    }

}

/**
 * Implements the resource properts
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Resources extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "RESOURCES";

        //multiple resources can be specified!
        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the status property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Status extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "STATUS";
        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the summary property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Summary extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "SUMMARY";
        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }
}

/**
 * Implements the date/time completed property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeCompleted extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params) {
        parent::__construct($value, $params);
        $this->_name = "COMPLETED";

        $this->_values[] = new File_iCal_ValueDataType_DateTime($value);

    }

}

/**
 * Implements the date/time end proeprty
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeEnd extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DTEND";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Parameter_Value")) {
                if ($p->getValuesString() == "DATE") {
                    $type = "DATE";
                }
            }
        }

        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }


    }

}

/**
 * Implements the date/time due property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeDue extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DUE";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Parameter_Value")) {
                if ($p->getValuesString() == "DATE") {
                    $type = "DATE";
                }
            }
        }


        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }


    }

}

/**
 * Implements the date/time start property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeStart extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DTSTART";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Parameter_Value")) {
                if ($p->getValuesString() == "DATE") {
                    $type = "DATE";
                }
            }
        }

        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }

    }
}

/**
 * Implements the duration property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Duration extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DURATION";

        $this->_values[] = new File_iCal_ValueDataType_Duration($value);
    }

}

/**
 * Implements the freebusy time type property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_FreeBusyTime extends File_iCal_Property
{
    protected $_parameters_single = array("FBTYPE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "FREEBUSY";

        $this->_values[] = new File_iCal_ValueDataType_PeriodOfTime($value);

    }

}

/**
 * Implements the time transparency property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeTransparency extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TRANSP";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the time zone identifier property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeZoneIdentifier extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TZID";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the time zone name property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeZoneName extends File_iCal_Property
{
    protected $_parameters_single = array("LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TZNAME";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * implements the timezone offset from property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeZoneOffsetFrom extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TZOFFSETFROM";

        $this->_values[] = new File_iCal_ValueDataType_UTCOffset($value);
    }

}

/**
 * Implements the timezone offset to property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeZoneOffsetTo extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TZOFFSETTO";

        $this->_values[] = new File_iCal_ValueDataType_UTCOffset($value);
    }

}

/**
 * Implements the timezone url property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_TimeZoneURL extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TZURL";

        $this->_values[] = new File_iCal_ValueDataType_URI($value);
    }

}

/**
 * Implements the attendee property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Attendee extends File_iCal_Property
{
    protected $_parameters_single = array("CUTYPE", "MEMBER", "ROLE", "PARTSTAT", "RSVP", "DELEGATED-TO", "DELEGATED-FROM", "SENT-BY", "CN", "DIR", "LANGUAGE");

    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "ATTENDEE";

        $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($value);

    }

}

/**
 * Implements the contact property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Contact extends File_iCal_Property
{
    protected $_parameters_single = array("ALTREP", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "CONTACT";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the organizer property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Organizer extends File_iCal_Property
{
    protected $_parameters_single = array("CN", "DIR", "SENT-BY", "LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "ORGANIZER";

        $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($value);

    }

}

/**
 * Implements the recurrence id property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RecurrenceID extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID", "RANGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "RECURRENCE-ID";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Parameter_Value")) {
                if ($p->getValuesString() == "DATE") {
                    $type = "DATE";
                }
            }
        }

        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }


    }

}

/**
 * Implements the related-to property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RelatedTo extends File_iCal_Property
{
    protected $_parameters_single = array("RELTYPE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "RELATED-TO";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the URL property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_URL extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "URL";

        $this->_values[] = new File_iCal_ValueDataType_URI($value);
    }

}

/**
 * Implements the UID property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_UID extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params) {
        parent::__construct($value, $params);
        $this->_name = "UID";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);

    }

}

/**
 * Implements exception date/time property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_ExceptionDateTimes extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "EXDATE";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Paramter_Value")) {
                if ($p->getValueString() == "DATE") {
                    $type = "DATE";
                }
            }
        }

        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }


    }

}

/**
 * Implements the exception rule property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_ExceptionRule extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "EXRULE";

        $this->_values[] = new File_iCal_ValueDataType_RecurrenceRule($value);

    }

}

/**
 * Implements the recurrence date/time property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RecurrenceDateTimes extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "TZID");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "RDATE";

        $type = "DATE-TIME";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Paramter_Value")) {
                if ($p->getValueString() == "DATE") {
                    $type = "DATE";
                }
                else if ($p->getValueString() == "PERIOD") {
                    $type = "PERIOD";
                }
            }
        }

        if ($type == "DATE") {
            $this->_values[] = new File_iCal_ValueDataType_Date($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }
        else if ($type == "PERIOD") {
            $this->_values[] = new File_iCal_ValueDataType_PeriodOfTime($value);
        }


    }

}

/**
 * Implements the recurrence rule property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RecurrenceRule extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "RRULE";

        $this->_values[] = new File_iCal_ValueDataType_RecurrenceRule($value);

    }

}

/**
 * Implements the action property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Action extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "ACTION";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

/**
 * Implements the repeat conut property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RepeatCount extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "REPEAT";

        $this->_values[] = new File_iCal_ValueDataType_Integer($value);
    }

}

/**
 * Implements the trigger property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_Trigger extends File_iCal_Property
{
    protected $_parameters_single = array("VALUE", "RELATED");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "TRIGGER";

        $type = "DURATION";

        foreach ($params as $p) {
            if (is_a($p, "File_iCal_Paramter_Value")) {
                if ($p->getValueString() == "DATE-TIME") {
                    $type = "DATE-TIME";
                }
            }
        }

        if ($type == "DURATION") {
            $this->_values[] = new File_iCal_ValueDataType_Duration($value);
        }
        else if ($type == "DATE-TIME") {
            $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
        }


    }

}

/**
 * Implements date/time created property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeCreated extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "CREATED";

        $this->_values[] = new File_iCal_ValueDataType_DateTime($value);
    }

}

/**
 * Implements the date/time stamp property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_DateTimeStamp extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "DTSTAMP";

        $this->_values[] = new File_iCal_ValueDataType_DateTime($value);

    }

}

/**
 * Implements the last modified property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_LastModified extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "LAST-MODIFIED";
        $this->_value[] = new File_iCal_ValueDataType_DateTime($value);

    }

}

/**
 * Implements the sequence number property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_SequenceNumber extends File_iCal_Property
{
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "SEQUENCE";

        $this->_values[] = new File_iCal_ValueDataType_Integer($value);
    }
}

/**
 * Implements the request status property
 *
 * @category File
 * @package iCal
 */
class File_iCal_Property_RequestStatus extends File_iCal_Property
{
    protected $_parameters_single = array("LANGUAGE");
    protected $_parameters_multiple = array("X-*");

    public function __construct($value, $params)
    {
        parent::__construct($value, $params);
        $this->_name = "REQUEST-STATUS";

        $this->_values[] = new File_iCal_ValueDataType_Text($value);
    }

}

?>
