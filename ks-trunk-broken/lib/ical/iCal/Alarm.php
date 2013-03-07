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
 * Alaram extends BaseComponent
 */
require_once('BaseComponent.php');

/**
 * File_iCal_Alarm is the user implementation of VALARM
 *
 * @category File
 * @package iCal
 */
class File_iCal_Alarm extends File_iCal_BaseComponent
{
    //make public from BaseComponent
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

    public function setDuration($d) {
        File_iCal_BaseComponent::setDuration($d);
    }

    public function getDuration() {
        return File_iCal_BaseComponent::getDuration();
    }




    /**
     *  The action to be invoked when an alarm is triggered
     */
    protected $_action;

    /**
     *  Set the action to be invoked by this alarm
     */
    public function setAction($a) {

    }

    /**
     *  Get the action to be performed by this alarm
     */
    public function getAction() {

    }

    /**
     * The number of times the action should occur
     */
    protected $_repeat;

    /**
     *  Set the number of times the action should occur
     */
    public function setRepeatCount($r) {

    }

    /**
     *  Get the number of times the action should occur
     */
    public function getRepeatCount() {

    }

    /**
     *  Specified when an alarm will trigger
     */
    protected $_trigger;

    /**
     *  Set when the alarm will trigger
     */
    public function setTrigger($t) {

    }

    /**
     *  Get when the alarm will trigger
     */
    public function getTrigger() {

    }

}
