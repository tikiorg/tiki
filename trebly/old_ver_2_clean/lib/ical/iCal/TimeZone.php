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
 *  TimeZone extends BaseComponent class
 */
require_once('BaseComponent.php');

/**
 * File_iCal_TimeZone is the user implementation of VTIMEZONE
 *
 * @category File
 * @package iCal
 */
class File_iCal_TimeZone extends File_iCal_BaseComponent
{
    public function addComment($c) {
        File_iCal_BaseComponent::addComment($c);
    }

    public function getComments() {
        return File_iCal_BaseComponent::getComments();
    }

    public function setDateStart($ds) {
        File_iCal_BaseComponent::setDateStart($ds);
    }

    public function getDateStart() {
        return File_iCal_BaseComponent::getDateStart();
    }

    public function setLastModified($m) {
        File_iCal_BaseComponent::setLastModified($m);
    }

    public function getLastModified() {
        return File_iCal_BaseComponent::getLastModified();
    }


    /**
     *
     */
    protected $_tzid;

    /**
     *
     */
    public function setTimeZoneIdentifier($tz) {

    }

    /**
     *
     */
    public function getTimeZoneIdentifier() {

    }

    /**
     *
     */
    protected $_tzname;

    /**
     *
     */
    public function setTimeZoneName($n) {

    }

    /**
     *
     */
    public function getTimeZoneName() {

    }

    /**
     *
     */
    protected $_offsetfrom;

    /**
     *
     */
    public function setTimeZoneOffsetFrom($of) {

    }

    /**
     *
     */
    public function getTimeZoneOffsetFrom() {

    }

    /**
     *
     */
    protected $_offsetto;

    /**
     *
     */
    public function setTimeZoneOffsetTo($ot) {

    }

    /**
     *
     */
    public function getTimeZoneOffsetTo() {

    }

    /**
     *
     */
    protected $_url;

    /**
     *
     */
    public function setTimeZoneURL($url) {

    }

    /**
     *
     */
    public function getTimeZoneURL() {

    }
}
