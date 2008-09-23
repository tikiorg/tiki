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
 * Constants defined in File_iCal needed
 */
//require_once('File_iCal.php');

/**
 * Attendee for an event
 *
 * Attendee represent persons who attend events.  Attendees have many different properties that can set.
 * For this reason, this library implements an attendee object.
 *
 * @package iCal
 * @category File
 */
class File_iCal_Attendee {
    /**
     * @access protected
     */
    protected $_address;

    /**
     * @access protected
     */
    protected $_cn;

    /**
     * @access protected
     */
    protected $_role;

    /**
     * @access protected
     */
    protected $_status;

    /**
     * @access protected
     */
    protected $_rsvp;

    /**
     * @access protected
     */
    protected $_cutype;
    /**
     * @access protected
     */
    protected $_member = array();

    /**
     * @access protected
     */
    protected $_delegatedto = array();

    /**
     * @access protected
     */
    protected $_delegatedfrom = array();

    /**
     * @access protected
     */
    protected $_sentby;

    /**
     * @access protected
     */
    protected $_dir;

    /**
     * Create an an attendee
     *
     * The parameter for the constructor is NOT the name of the person.  Instead, it is a contact address, such as e-mail.
     *
     * The constructor will also set some default values for the new attendee.  1) The status will be set to "NEEDS ACTION" 2) The type of attendee will be set to an individual
     *
     * @access  public
     * @param   string  The contact address for the attendee
     */
    public function __construct($address) {
        $this->_address = $address;
        self::setStatusNeedsAction();
        self::makeIndividual();
    }

    /**
     * Returns the address associated with an attendee
     *
     * @access  public
     * @return  string  The address for the attendee
     */
    public function getAddress() {
        return $this->_address;
    }

    /**
     * Sets the common name (CN) of the attendee
     *
     * The common name of the attendee is the human-readable name.  e.g. John Smith
     *
     * @access  public
     * @param   string  A string to which the common name will be set
     */
    public function setCommonName($cn) {
        $this->_cn = $cn;
    }

    /**
     * Returns the common name for this attendee
     *
     * @access  public
     * @return  string  The common name for this attendee
     */
    public function getCommonName() {
        return $this->_cn;
    }

    public function makeChair() {
        $this->_role = File_iCal::ICAL_ROLE_CHAIR;
    }

    public function makeRequiredParticipant() {
        $this->_role = File_iCal::IICAL_ROLE_REQUIRED;
    }

    public function makeOptionalParticipant() {
        $this->_role = File_iCal::IICAL_ROLE_OPTIONAL;
    }

    public function makeNonParticipant() {
        $this->_role = File_iCal::IICAL_ROLE_NONPARTICIPATING;
    }

    public function getRole() {
        return $this->_role;
    }

    public function setStatusNeedsAction() {
        $this->_status = File_iCal::IICAL_STATUS_NEEDSACTION;
    }

    public function setStatusAccepted() {
        $this->_status = File_iCal::IICAL_STATUS_ACCEPTED;
    }

    public function setStatusDeclined() {
        $this->_status = File_iCal::IICAL_STATUS_DECLINED;
    }

    public function setStatusTentative() {
        $this->_status = File_iCal::IICAL_STATUS_TENTATIVE;
    }

    public function setStatusDelegated() {
        $this->_status = File_iCal::IICAL_STATUS_DELEGATED;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function setRSVP($bool) {
        $bool ? $this->_rsvp = true : $this->_rsvp = false;
    }

    public function getRSVP() {
        return $this->_rsvp;
    }

    public function makeIndividual() {
        $this->_cutype = File_iCal::IICAL_USERTYPE_INDIVIDUAL;
    }

    public function makeGroup() {
        $this->_cutype = File_iCal::IICAL_USERTYPE_GROUP;
    }

    public function makeResource() {
        $this->_cutype = File_iCal::IICAL_USERTYPE_RESOURCE;
    }

    public function makeRoom() {
        $this->_cutype = File_iCal::IICAL_USERTYPE_ROOM;
    }

    public function getUserType() {
        return $this->_cutype;
    }

    public function addMember($m) {
        if (!in_array($m, $this->_member)) {
            $this->_member[] = $m;
        }
    }

    public function getMembers() {
        return $this->_member;
    }

    public function addDelegatee($d) {
        if (!in_array($d, $this->_delegatedto)) {
            $this->_delegatedto = $d;
        }
    }

    public function getDelegatees() {
        return $this->_delagatedto;
    }

    public function addDelegator($d) {
        if (!in_array($d, $this->_delegatedfrom)) {
            $this->_delegatedfrom = $d;
        }
    }

    public function getDelegators() {
        return $this->_delegatedfrom;
    }

    public function setSentBy($s) {
        $this->_sentby = $s;
    }

    public function getSentBy() {
        return $this->_sentby;
    }

    public function setDirectory($d) {
        $this->_dir = $d;
    }

    public function getDirectory() {
        return $this->_dir;
    }

}


?>