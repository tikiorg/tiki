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
 * File_iCal_ContentLine uses File_iCal_Property internally
 */
require_once('Property.php');

/**
 * File_iCal_ContentLine uses File_iCal_Parameter internally
 */
require_once('Parameter.php');

/**
 *  File_iCal_ContentLine is a helper class that stores information about one line in an iCal file
 *
 *  @category File
 *  @package iCal
 */
class File_iCal_ContentLine
{
    /**
     * The contentline stores data in a File_iCal_Property
     *
     * The ContentLine and the Property have an interesting relationship.
     * A ContnetLine is principally a helper class.  It serves as an interface
     * to create properties transparently.
     *
     * @access protected
     * @var File_iCal_Property  Property representation of this contentline
     */
    protected $_property;


    /**
     * Create a new ContentLine
     *
     * @access  public
     * @param   mixed   $line   The parameter can be of a few types:
     *                          1) a File_iCal_Property object
     *                          2) a line of text in an iCal file
     */
    public function __construct($line)
    {
        //if we ar econstructing a contentline out of an existing property
        if (is_a($line, "File_iCal_Property")) {
            $this->_property = $line;
        } else {

            //remove all special characters from beginning and end
            $line = trim($line);

            //now it is time again to rewrite the tokenizer (for the 4th time)

            $tokens = array();  //array of strings which represent tokens

            $i = 0; //string position
            $l = strlen($line);

            //every entry will start looking for a new token
            do {
                $t = "";    //our new token
                $begin = $i;

                //behave differently depending on what type of character we encounter
                switch ($line{$i}) {
                    case '\"';  //encounter the beginning of a double quoted string
                        $valid = true;

                        while ($valid) {
                            $i = strpos($line, '\"', $i);
                            if ($line{$i-1} == '\\') $valid = false;
                        }

                        $t = substr($line, $begin, $i - $begin);
                        $i++;
                        break;

                    case ':';
                    case ';';
                        $t = $line{$i};
                        $i++;
                        break;


                    default:
                        //if we don't have a semicolon or colon in the tokens array yet, we are reading the name string
                        $present_colon = in_array(':', $tokens);
                        $present_semi = in_array(';', $tokens);


                        if (!$present_colon && !$present_semi) {
                            $m;
                            preg_match("/^([\\w\\d-]+)[;:]?/", $line, $m);
                            //$m[1] contains the name
                            $t = $m[1];
                            $i += strlen($t);

                        } else if ($present_colon && $tokens[count($tokens) -1] == ':') {
                            //if the last token is a colon, read out the value
                            $t = substr($line, $i);
                            $i += strlen($t);

                        } else if ($present_semi && $tokens[count($tokens) -1] == ';') {
                            //if the last token is a semicolon
                            $t = substr($line, $i, strpos($line, ':', $i) - $i);
                            $i += strlen($t);

                        } else {
                            $t = $line{$i};
                            $i++;
                        }

                }

                $tokens[] = $t;
            } while ($i < $l);

            //print_r($tokens);

            $colon_key = array_search(':', $tokens);

            $name_string = $tokens[0];
            $value_string = $tokens[$colon_key+1];

            $params = array();

            if ($tokens[1] == ';') {
                $arr = explode(';', $tokens[2]);

                foreach ($arr as $v) {
                    $val = explode('=', $v);

                    $name = $val[0];
                    $values = array($val[1]);

                    $params[] = File_iCal_Parameter::getParameter($name, $values);

                }
            }

            $this->_property = File_iCal_Property::getProperty($name_string, $params, $value_string);

        }

    }


    /**
     * Get the name associate with this ContentLine
     *
     * @access  public
     * @deprecated
     * @return  string  the string value of the property name
     */
    public function name()
    {
            if (is_a($this->_property, "File_iCal_Property")) {
                return $this->_property->getName();
            }
    }

    /**
     * Gets the parameters from this ContentLine
     *
     * @access  public
     * @return  array   An array of File_iCal_Parameter
     */
    public function params()
    {
            return $this->_property->getParams();
    }

    /**
     * @deprecated
     */
    public function value()
    {
            return $this->_property->getValue();
    }


    /**
     * Returns the property this contentline represents
     *
     * @access  public
     * @return  File_iCal_Property
     */
    public function getProperty() {
        return $this->_property;
    }

    /**
     * returns a contentLine formatted for an iCal file
     *
     * The function will return a property as it is to be formatted for an iCal file
     *
     * @access  public
     * @return  string
     * @param   bool    Whether to wrap long lines
     */
    public function getLine($wrap = true)
    {
        $s = $this->_property->getName();

        if ($params = $this->_property->getParams()) {
            foreach ($params as $p) {
                $s .= ';'.$p->getName().'='.$p->getValuesString();
            }
        }

        $s .= ":";

        //echo "$s\n";

        $values = array();
        foreach ($this->_property->getValues() as $v) {
            //$values[] = self::encodeString($v->getValue());
            $values[] = $v->getValue();
        }

        $s .= implode($this->_property->getValueSeparator(), $values);

        //$s .= "\r\n";
        return $s;
    }

    /**
     * Returns an string encoded for presense on a contentline
     *
     * Some characters in the iCal file format are special and need to be delimited.  This function
     * will handle the delimination
     *
     * @access  private
     * @return  string  Encoded string
     * @param   string  String to encode
     */
    private function encodeString($s) {
        return str_replace(array("\\", ",", "\"", "'", ";", ":"), array("\\\\", "\\,", "\\\"", "\\\'", "\\;", "\\:"), $s);
    }

    /**
     * Returns a string that has been decoded
     *
     * @access  private
     * @return  string  Decoded string suitable for storage in data classes
     * @param   string  String to decode
     */
    private function decodeString($s) {
        return str_replace(array("\\\\", "\\,", "\\\"", "\\\'", "\\;", "\\:"), array("\\", ",", "\"", "'", ";", ":"), $s);
    }
}
