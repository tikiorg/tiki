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
 * Interfaces with File_iCal_Event
 */
require_once('File/iCal/Event.php');

/**
 * An actual calendar
 *
 * @package iCal
 * @category File
 */
class File_iCal_iCalendar {
        protected $_properties = array();   //an array of Properties
        protected $_components = array();   //an array of Components

        //takes a string (including line breaks) which represents calendar data
        //should begin with BEGIN:VCALENDAR
        //should end with END:VCALENDAR
        public function __construct($string)
        {
            //unfold long lines
            $string = self::_unfold($string);

            $string = trim($string);   //remove trailing CRLF if present

            //split the file into lines
            $lines = explode("\x0D\x0A", $string);

            $contentlines = array();
            require_once 'File/iCal/ContentLine.php';
            //assemble into contentlines
            foreach ($lines as $line)
            {
                    $contentlines[] = new File_iCal_ContentLine($line);
            }

            //verify the validity of the structure
            if (($contentlines[0]->name() == "BEGIN") && ($contentlines[0]->value() == "VCALENDAR")
                    && ($contentlines[count($contentlines)-1]->name() == "END") && ($contentlines[count($contentlines)-1]->value() == "VCALENDAR"))
            {
                    $begin = 0;

                    //search for the first component (the end of the header)
                    for ($i = 1; $i < count($contentlines); $i++)
                    {
                            if ($contentlines[$i]->name() == "BEGIN")
                            {
                                    $begin = $i;
                                    break;
                            }
                    }

                    //$begin is where the first component starts, so [1] to [$begin-1] are calprops
                    for ($i = 1; $i < $begin; $i++)
                    {
                            switch ($contentlines[$i]->name())
                            {
                                    case "PRODID":
                                    case "VERSION":
                                    case "CALSCALE":
                                    case "METHOD":
                                            $this->_properties[] = $contentlines[$i]->getProperty();
                                            break;

                                    default:
                                            $this->_properties[] = $contentlines[$i]->getProperty();
                            }
                    }

                    //now go through and find whole components
                    for ($i = $begin; $i < count($contentlines) - 1; $i++)
                    {
                            $component = $contentlines[$i]->value();

                            for ($j = $i + 1; $j < count($contentlines) -1; $j++)
                            {
                                    if (($contentlines[$j]->name() == "END") && ($contentlines[$j]->value() == $component))
                                    {
                                            //found the end of the component
                                            // $i is the beginning index
                                            // $j is the end index
                                            $comp = array_slice($contentlines, $i, $j - $i + 1);
                                            $this->ProcessComponent($comp);
                                            $i = $j;
                                            break;
                                    }
                            }

                    }

            }
            else
            {
                    trigger_error("Process error:  The first and last lines in an iCalendar string are not formatted properly", E_USER_ERROR);
            }
        }

        // $c is an array of contentlines
        private function ProcessComponent($c)
        {
            require_once 'File/iCal/Component.php';
            switch ($c[0]->value())
            {
                case "VEVENT":
                    $this->_components[] = new File_iCal_Component_Event($c);
                    break;

                case "VTODO":
                    $this->_components[] = new File_iCal_Component_ToDo($c);
                    break;

                case "VJOURNAL":
                    $this->_components[] = new File_iCal_Component_Journal($c);
                    break;

                case "VFREEBUSY":
                    $this->_components[] = new File_iCal_Component_FreeBusy($c);
                    break;

                case "VTIMEZONE":
                    $this->_components[] = new File_iCal_Component_Timezone($c);
                    break;

                case "VALARAM":
                    $this->_components[] = new File_iCal_Component_Alarm($c);
                    break;

                default:
                    trigger_error("Component ".$c[0]->value() ." is unknown!", E_USER_WARNING);
            }


        }

        private function _unfold($s)
        {
                $token = "\x0D\x0A\x20";
                return str_replace($token, '', $s);
        }

        public function __toString() {
            return $this->getCalendar();
        }

        //returns a string which represents a complete iCalendar
        public function getCalendar($fold =  true, $wrap_length = 72)
        {
            $r = array();
            require_once 'File/iCal/ContentLine.php';
            $header = new File_iCal_ContentLine("BEGIN:VCALENDAR");
            $footer = new File_iCal_ContentLine("END:VCALENDAR");


            //add the header first thing
            $r[] = $header->getProperty();

            //add on all calendar properties
            foreach ($this->_properties as $prop) {
                    $r[] = $prop;
            }

            //add on all calendar components
            foreach ($this->_components as $v)
            {
                    $r = array_merge($r, $v->getPropertyArray());
            }

            //finally add the footer
            $r[] = $footer->getProperty();

            $s = "";

            foreach ($r as $prop)
            {
                if (!is_a($prop, "File_iCal_Property")) {
                    print_r($prop);
                }
                else {
                    $line = $prop->getLine();
                    if ($fold) {
                        $arr = array();
                        for ($i = 0; $i <= strlen($line) / $wrap_length; $i++) {
                            $arr[] = substr($line, ($i)*$wrap_length, $wrap_length);
                        }

                        for ($i = 0; $i < count($arr) -1; $i++) {
                            $s .= $arr[$i]."\x0D\x0A\x20";
                        }

                        $s .= $arr[count($arr)-1]."\x0D\x0A";

                    }
                    else {
                        $s .= $line;
                    }
                }
            }

            return $s;

        }

        /**
         *  Get the component at the internal index key
         *
         *  @access public
         *  @param  int $key    The key of the internal array to return
         */
        public function getComponent($key) {
            if (isset($this->_components[$key])) {
                return $this->_components[$key];
            }
            else {
                return false;
            }
        }

        public function ComponentKeyExists($key) {
            return isset($this->_components[$key]);
        }

        /**
         *  Set the component at specific index
         *
         *  @access public
         *  @param  object  $component  Component to add
         *  @param  int     $key        The array index to set
         */
        public function setComponent($component, $key) {
            $this->_components[$key] = $component;
        }

        public function addComponent($c) {
            $this->_components[] = $c;
        }

        /**
         * Returns an array of File_iCal_Event
         *
         *
         */
        public function getEvents() {
            $return = array();

            foreach ($this->_components as $c) {
                if (is_a($c, "File_iCal_Component_Event")) {
                    $return[] = $c->getEvent();
                }
            }

            return $return;
        }

        /*
         * Adds an event to the calendar
         *
         *
         */
        public function addEvent( $e) { //File_iCal_Event
            require_once 'File/iCal/Component.php';
            $this->_components[] = File_iCal_Component_Event::getComponent($e);
        }

        public function deleteComponent($key) {
            if (isset($this->_components[$key])) {
                unset($this->_components[$key]);
            }
            else {
                trigger_error("Attempting to delete a component with key $key that doesn't exist", E_USER_WARNING);
            }
        }

        public function deleteEvents() {
            foreach ($this->_components as $k=>$c) {
                if (is_a($c, "File_iCal_Component_Event")) {
                    unset($this->_components[$k]);
                }
            }

            //do we need to reindex the array?
        }

}
?>
