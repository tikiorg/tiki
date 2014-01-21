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
*   iCal (RFC 2245) class definition
*
*   PHP version 5
*
*   @package    iCal
*   @subpackage Parser
*   @author     Gregory Szorc <gregory.szorc@case.edu>
*   @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
*/

/**
 * File_iCal_Parameter is the base class for all iCal parameters
 *
 * @category File
 * @package iCal
 */
abstract class File_iCal_Parameter
{
        protected $_name;
        protected $_values = array();

        protected $_valueDoubleQuote = false;

        public function __construct($name, &$values)
        {
            if (!is_array($values)) {
                $values = array($values);
            }
        }

        public function getName() { return $this->_name; }

        //returns an array of value data type objects
        public function getValues() {
            return $this->_values;
        }

        //returns a string formatted for content lines
        public function getValuesString() {
            $r = array();

            foreach ($this->_values as $v) {
                switch ($this->_valueDoubleQuote) {
                    case true:
                        $r[] = "\"".$v->getValue()."\"";
                        break;

                    case false:
                        $r[] = $v->getValue();
                        break;

                    case "unknown":
                        if (self::stringNeedsQuoted($v->getValue())) {
                            $r[] = "\"".$v->getValue()."\"";
                        }
                        else {
                            $r[] = $v->getValue();
                        }
                        break;

                    default:
                        trigger_error("Unknown internal double quote value in parameter", E_USER_ERROR);

                }

            }

            return implode(',', $r);
        }

        static public function stringNeedsQuoted($s) {
            if (strpbrk($s, ";:,")) return true;

            return false;
        }

        //returns an array with key and value keys
        public function getArray() {
            $r = array();
            $r['name'] = self::getName();
            $r['values'] = self::getValueString();
        }

        //overload at child class level
        public function getDefaultValue() {

        }

        //overload at child class level
        public function getDefaultValueString() {

        }

        static public function getParameter($name, $values) {
            if (!is_array($values)) {
                $values = array($values);
            }

            switch (strtoupper($name)) {
                case "ALTREP":
                    return new File_iCal_Parameter_AlternateTextRepresentation($name, $values);

                case "CN":
                    return new File_iCal_Parameter_CommonName($name, $values);

                case "CUTYPE":
                    return new File_iCal_Parameter_CalendarUserType($name, $values);

                case "DELEGATED-FROM":
                    return new File_iCal_Parameter_Delegators($name, $values);

                case "DELEGATED-TO":
                    return new File_iCal_Parameter_Delegatees($name, $values);

                case "DIR":
                    return new File_iCal_Parameter_Delegators($name, $values);

                case "ENCODING":
                    return new File_iCal_Parameter_Encoding($name, $values);

                case "FMTTYPE":
                    return new File_iCal_Parameter_FormatType($name, $values);

                case "FBTYPE":
                    return new File_iCal_Parameter_FreeBusyTimeType($name, $values);

                case "LANGUAGE":
                    return new File_iCal_Parameter_Language($name, $values);

                case "MEMBER":
                    return new File_iCal_Parameter_Membership($name, $values);

                case "PARTSTAT":
                    return new File_iCal_Parameter_ParticipationStatus($name, $values);

                case "RANGE":
                    return new File_iCal_Parameter_RecurrenceIdentifierRange($name, $values);

                case "RELATED":
                    return new File_iCal_Parameter_AlarmTriggerRelationship($name, $values);

                case "RELTYPE":
                    return new File_iCal_Parameter_RelationshipType($name, $values);

                case "ROLE":
                    return new File_iCal_Parameter_ParticipationRole($name, $values);

                case "RSVP":
                    return new File_iCal_Parameter_RSVP($name, $values);

                case "SENT-BY":
                    return new File_iCal_Parameter_SentBy($name, $values);

                case "TZID":
                    return new File_iCal_Parameter_TimeZoneIdentifier($name, $values);

                case "VALUE":
                    return new File_iCal_Parameter_Value($name, $values);

                default:
                    echo "The parameter type $name is not yet supported\n";
            }
        }

}

/**
 * Implements an alternate text representation parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_AlternateTextRepresentation extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "ALTREP";
        $this->_valueDoubleQuote = true;

        //issue a warning if the document doesn't fully comply with RFC 2445
        if (count($values) > 1) {
            trigger_error("Altrepparam parameter must only contain a single value!", E_USER_WARNING);
        }

        foreach ($values as $v) {
            $matches;
            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_URI($matches[1]);
            }
            else {
                trigger_error("Altrepparam must be double quoted", E_USER_WARNING);
            }
        }

    }

}

/**
 * Implements a common name parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_CommonName extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);

        $this->_name = "CN";

        if (count($values) > 1) {
            trigger_error("CN parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            //the value may or may not be in double quotes
            $matches;

            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_Text($matches[1]);
                $this->_valueDoubleQuote = true;
            }
            else {
                $this->_values[] = new File_iCal_ValueDataType_Text($v);
                $this->_valueDoubleQuote = false;
            }
        }
    }
}

/**
 * Implements a calendar user type parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_CalendarUserType extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "CUTYPE";

        if (count($values) > 1) {
            trigger_error("CUTYPE parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "INDIVIDUAL":
                case "GROUP":
                case "RESOURCE":
                case "ROOM":
                case "UNKNOWN":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                //need to handle cases for x-name and iana-token
                    trigger_error("CUTYPE parameter value $v is not recognized", E_USER_WARNING);

            }
        }

    }

    public function getDefaultValue() {
        return new File_iCal_ValueDataType_Text("INDIVIDUAL");
    }

    public function getDefaultValueString() {
        $v = self::getDefaultValue();
        $return = $v->getValue();

        unset($v);  //prevent memory leak

        return $return;
    }
}

/**
 * Implements a delegator parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Delegators extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "DELEGATED-FROM";
        $this->_valueDoubleQuote = true;



        foreach ($values as $v) {
            $matches;
            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                    $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($matches[1]);
                    $this->_valueDoubleQuote = true;
            }
            else {
                trigger_error("Delegator parameter must be specified in double quotes", E_USER_WARNING);
            }
        }
    }
}

/**
 * Implements a delegatee parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Delegatees extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "DELEGATED-TO";
        $this->_valueDoubleQuote = true;

        foreach ($values as $v) {
            $matches;

            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($v);
                $this->_valueDoubleQuote = true;
            }
            else {
                trigger_error("Delegatee parameter must be specified in double quotes", E_USER_WARNING);
            }

        }
    }
}

/**
 * Implements a directory entry reference parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_DirectoryEntryReference extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "DIR";

        if (count($values) > 1) {
            trigger_error("There cannot be multiple values for a DIR parameter", E_USER_WARNING);
        }

        foreach ($values as $v) {
            $matches;

            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_URI($matches[1]);
                $this->_valueDoubleQuote = true;
            }
            else {
                trigger_error("A DIR parameter must be enclosed in double quotes", E_USER_WARNING);
            }
        }

    }
}

/**
 * Implements an encoding parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Encoding extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "ENCODING";

        if (count($values) > 1) {
            trigger_error("ENCODING parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "8BIT":
                case "BASE64":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                //need to handle cases for x-name and iana-token
                    trigger_error("ENCODING parameter value $v is not recognized", E_USER_WARNING);

            }
        }


    }
}

/**
 * Implements a format type parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_FormatType extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "FMTTYPE";

        if (count($values) > 1) {
            trigger_error("FMTTYPE parameter cannot have more than one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            $this->_values[] = new File_iCal_ValueDataType_Text($v);
        }
    }
}

/**
 * Implements a free-busy time type parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_FreeBusyTimeType extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "FBTYPE";

        if (count($values) > 1) {
            trigger_error("FBTYPE parameter cannot have more than one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "FREE":
                case "BUSY":
                case "BUSY-UNAVAILABLE":
                case "BUSY-TENTATIVE":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("FBTYPE value $v is not yet supported", E_USER_WARNING);
            }
        }
    }
}

/**
 * Implements a language parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Language extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "LANUAGE";

        if (count($values) > 1) {
            trigger_error("LANGUAGE parameter cannot have more than one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            $this->_values[] = new File_iCal_ValueDataType_Text($v);
        }
    }
}

/**
 * Implements a membership parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Membership extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "MEMBER";

        foreach ($values as $v) {
            $matches;

            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($matches[1]);
                $this->_valueDoubleQuote = true;
            }
            else {
                trigger_error("A MEMBER parameter must be enclosed in double quotes", E_USER_WARNING);
            }
        }
    }
}

/**
 * Implements a participation status parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_ParticipationStatus extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "PARTSTAT";

        if (count($values) > 1) {
            trigger_error("PARTSTAT parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "NEEDS-ACTION":
                case "ACCEPTED":
                case "DECLINED":
                case "TENTATIVE":
                case "DELEGATED":
                case "COMPLETED":
                case "IN-PROGRESS":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                //need to handle cases for x-name and iana-token
                    trigger_error("PARTSTAT parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }

    }
}

/**
 * Implements a recurrence identifier range parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_RecurrenceIdentifierRange extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "RANGE";

        if (count($values) > 1) {
            trigger_error("RANGE parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "THISANDPRIOR":
                case "THISANDFUTURE":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("RANGE parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }

    }
}

/**
 * Implements alart trigger relationship parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_AlarmTriggerRelationship extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "RELATED";

        if (count($values) > 1) {
            trigger_error("RELATED parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "START":
                case "END":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("RELATED parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }
    }
}

/**
 * Implements relationship type paramter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_RelationshipType extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "RELTYPE";

        if (count($values) > 1) {
            trigger_error("RELTYPE parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "PARENT":
                case "CHILD":
                case "SIBLING":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("RELTYPE parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }

    }
}

/**
 * Implements participation role paramter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_ParticipationRole extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "ROLE";

        if (count($values) > 1) {
            trigger_error("ROLE parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "CHAIR":
                case "REQ-PARTICIPANT":
                case "OPT-PARTICIPANT":
                case "NON-PARTICIPANT":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                //need to handle cases for x-name and iana-token
                    trigger_error("ROLE parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }


    }
}

/**
 * Implements RSVP parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_RSVP extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "RSVP";
        $this->_valueDoubleQuote = false;

        if (count($values) > 1) {
            trigger_error("RSVP parameter must have at most one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "TRUE":
                case "FALSE":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("RSVP parameter value $v is not yet recognized", E_USER_WARNING);

            }
        }
    }
}

/**
 * Implements a sent-by parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_SentBy extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "SENT-BY";
        $this->_valueDoubleQuote = true;

        if (count($values) > 1) {
            trigger_error("SENT-BY parameter can have at most 1 value", E_USER_WARNING);
        }

        foreach ($values as $v) {
           $matches;

            if (preg_match("/^\"(.*)\"\$/", $v, $matches)) {
                $this->_values[] = new File_iCal_ValueDataType_CalendarUserAddress($matches[1]);
                $this->_valueDoubleQuote = true;
            }
            else {
                trigger_error("A SENT-BY parameter must be enclosed in double quotes", E_USER_WARNING);
            }
        }

    }
}

/**
 * Implements a time zone identifier parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_TimeZoneIdentifier extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "TZID";
        $this->_valueDoubleQuote = false;

        if (count($values) > 1) {
            trigger_error("TZID parameter must only have one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            $this->_values[] = new File_iCal_ValueDataType_Text($v);
        }
    }
}

/**
 * Implements a value parameter
 *
 * @category File
 * @package iCal
 */
class File_iCal_Parameter_Value extends File_iCal_Parameter
{
    public function __construct($name, $values) {
        parent::__construct($name, $values);
        $this->_name = "VALUE";
        $this->_valueDoubleQuote = false;

        if (count($values) > 1) {
            trigger_error("VALUE parameter must only have one value", E_USER_WARNING);
        }

        foreach ($values as $v) {
            switch ($v) {
                case "BINARY":
                case "BOOLEAN":
                case "CAL-ADDRESS":
                case "DATE":
                case "DATE-TIME":
                case "DURATION":
                case "FLOAT":
                case "INTEGER":
                case "PERIOD":
                case "RECUR":
                case "TEXT":
                case "TIME":
                case "URI":
                case "UTC-OFFSET":
                    $this->_values[] = new File_iCal_ValueDataType_Text($v);
                    break;

                default:
                    trigger_error("VALUE parameter $v is unknown type", E_USER_WARNING);

            }
        }

    }
}
