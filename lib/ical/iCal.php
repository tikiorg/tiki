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
 * File_iCal_File is the heart of the package
 *
 * @category File
 * @package iCal
 */
class File_iCal {
    const VERSION = "0.0.3";

    const CLASSIFICATION_PUBLIC = "PUBLIC";
    const CLASSIFICATION_PRIVATE = "PRIVATE";
    const CLASSIFICATION_CONFIDENTIAL = "CONFIDENTIAL";

    const PRIORITY_NONE = 0;
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_MEDIUM = 5;
    const PRIORITY_LOWEST = 9;

    
    const TRANSPARENCY_OPAQUE = "OPAQUE";
    const TRANSPARENCY_TRANSPARENT = "TRANSPARENT";

    const ROLE_CHAIR = "CHAIR";
    const ROLE_REQUIRED = "REQ-PARTICIPANT";
    const ROLE_OPTIONAL = "OPT-PARTICIPANT";
    const ROLE_NONPARTICIPANT = "NON-PARTICIPANT";

    const STATUS_NEEDSACTION = "NEEDS-ACTION";
    const STATUS_ACCEPTED = "ACCEPTED";
    const STATUS_DECLINED = "DECLINED";
    const STATUS_TENTATIVE = "TENTATIVE";
    const STATUS_CONFIRMED = "CONFIRMED";
    const STATUS_CANCELLED = "CANCELLED";
    const STATUS_DELEGATED = "DELEGATED";
    const STATUS_COMPLETED = "COMPLETED";
    const STATUS_INPROGRESS = "IN-PROGRESS";

    const USERTYPE_INDIVIDUAL = "INDIVIDUAL";
    const USERTYPE_GROUP = "GROUP";
    const USERTYPE_RESOURCE = "RESOURCE";
    const USERTYPE_ROOM = "ROOM";
    const USERTYPE_UNKNOWN = "UNKNOWN";

    const FREQUENCY_SECONDLY = "SECONDLY";
    const FREQUENCY_MINUTELY = "MINUTELY";
    const FREQUENCY_HOURLY = "HOURLY";
    const FREQUENCY_DAILY = "DAILY";
    const FREQUENCY_WEEKLY = "WEEKLY";
    const FREQUENCY_MONTHLY = "MONTHLY";
    const FREQUENCY_YEARLY = "YEARLY";

    const DAY_SUNDAY = "SU";
    const DAY_MONDAY = "MO";
    const DAY_TUESDAY = "TU";
    const DAY_WEDNESDAY = "WE";
    const DAY_THURSDAY = "TH";
    const DAY_FRIDAY = "FR";
    const DAY_SATURDAY = "SA";


    /**
     * Holds iCalendars belonging to an iCal file
     *
     * individual iCal files can hold multiple iCalendar objects.  This array holds the parsed objects
     *
     * @access  private
     * @var array   array of File_iCal_iCalendar objects
     */
    private $_iCalendars = array();

    /**
     *  Open an existing iCal file and parse it
     *
     *  @static
     *  @access public
     *  @param  string  $filename   file to open for parsing
     *  @param  bool    $return_all Some iCalendar files contain multiple iCalendar objects
     *                              If this parameter is true, all iCalendar objects will be returned.
     *                              This automatically sets the return type to array
     *                              If false, the return type will always be File_iCal_iCalendar
     *  @todo   Implement multiple iCalendars per file
     */
    public static function ReadFile($filename, $return_all = false) {
    
        require_once 'File.php';
        $data = File::readAll($filename);
        require_once 'File/iCal/iCalendar.php';
        $icalendar = new File_iCal_iCalendar($data);
        return $icalendar;
    }

    /**
     *  Add an existing iCalendar to the file
     *
     *  @access public
     *  @param  File_iCal_iCalendar $ical   The iCalendar object to add
     */
    public function addCalendar(File_iCal_iCalendar $ical) {
        $this->_iCalendars[] = $ical;
    }


    /**
     *  Converts the current iCal object to its actually iCal representation
     *
     *  This function gets called automatically if you say `echo $this`
     *
     *  @return string  iCal string
     */
    public function __toString() {
        $return = "";

        foreach ($this->_iCalendars as $c) {
            $return .= $c->__toString();
        }

        return $return;
    }

    /**
     *  Write the current iCal object to a file
     *
     *  @param  string  Filename to which to write
     */
    public function WriteFile($filename) {
        require_once 'File.php';
        return File::write($filename, $this->__toString(), FILE_MODE_WRITE);
    }

    /**
    * Retrive the iCalendars contained within a parsed iCal_File
    *
    * @access  public
    * @return  array   An array containing objects of type File_iCal_iCalendar
    */
    public function getCalendars() {
        return $this->_iCalendars;
    }

    /**
    * Send the iCal content-type header to the browser
    *
    * @access  public
    * @static
    */
    public static function sendHeader() {
        header("Content-type: text/calendar");
	header("Content-disposition: inline; filename=toto.ics");
	
    }

    /**
     *  Returns a File_iCal_iCalendar object with just the basic requirements
     */
    public static function getSkeletonCalendar() {
        $skeleton =  "BEGIN:VCALENDAR\x0D\x0A" . 
                     "VERSION:".File_iCal::VERSION."\x0D\x0A" .
                     "PRODID:PEAR-File_iCal\x0D\x0A" . 
                     "CALSCALE:GREGORIAN\x0D\x0A" . 
                     "END:VCALENDAR\x0D\x0A";
        require_once 'File/iCal/iCalendar.php';
        return new File_iCal_iCalendar($skeleton);
    }
    /**
    * Create a new subobject Event/Calender etc.
    *
    * @access  public
    * @param  name   Event|Calender etc.
    * @param  array  values to use.
    * @static
    */
    function factory($str,$args = array()) 
    {
        require_once 'File/iCal/'. $str. '.php';
        $class = 'File_iCal_'. $str;
        $ret = new $class;
        if ($args) {
            $ret->assignFrom($args);
        }
        return $ret;
    }

}

?>
