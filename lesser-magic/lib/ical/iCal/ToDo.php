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
 * Todo extends BaseComponent_ET
 */
require_once('BaseComponent.php');

/**
 * File_iCal_Event is the user implementation of VTODO
 *
 * @category File
 * @package iCal
 */
class File_iCal_ToDo extends File_iCal_BaseComponent_ET {
    /**
     *  The percentage complete of the ToDo
     */
    protected $_percentcomplete;

    /**
     *  Set the percentage complete of this todo
     */
    public function setPercentComplete($p) {

    }

    /**
     *  Get the percentage complete of this todo
     */
    public function getPercentComplete() {
        return $this->_percentcomplete;
    }

    /**
     *  The date/time the todo was completed
     */
    protected $_completed;

    /**
     *  Set the date/time the todo was completed
     */
    public function setCompleted() {

    }

    /**
     *  Get the date/time the todo was completed
     */
    public function getCompleted() {

    }

    /**
     *  Date/time todo is due
     */
    protected $_due;

    /**
     *  Set the date/time the todo is due
     */
    public function setDue($d) {

    }

    /**
     *  Get the date/time the todo is due
     */
    public function getDue() {

    }

}

?>