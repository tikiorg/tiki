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
 *  Event extends BaseComponent
 */
require_once 'File/iCal/BaseComponent.php';

/**
 * File_iCal_Event is the user implementation of VEVENT
 *
 * @category File
 * @package iCal
 */
class File_iCal_Event extends File_iCal_BaseComponent_ET {
    public function setDateEnd($dt) {
        File_iCal_BaseComponent::setDateEnd($dt);
    }

    public function getDateEnd() {
        return File_iCal_BaseComponent::getDateEnd();
    }

    /**
     *  Set the trasparency on your schedule
     */
    protected $_transparency;

    /**
     * Set the transparency of the event to opaque
     *
     *
     */
    public function setOpaque() {
        $this->_transparency= ICAL_TRANSPARENCY_OPAQUE;
    }

    /**
     * Set the transparency of the event to transparent
     *
     *
     */
    public function setTransparent() {
        $this->_transparency = ICAL_TRANSPARENCY_TRANSPARENT;
    }

    /**
     *  returns the transparency of the component
     */
    public function getTransparency() {
        return $this->_transparency;
    }


}


?>