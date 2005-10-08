<?php
/* @version $Id: sugarContact.php,v 1.2 2005-10-08 10:23:15 michael_davey Exp $ */

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version
 * 1.1.2 ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied.  See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) 2005 SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// A struct that exists to pass flags about contact records back and forth
class sugarContactFlags {
    // Obviously, it's a contact
    var $isContact=false;
    // Not obviously, it's a contact that has a lead
    var $hasLead=false;
    // Obviously, it's a lead
    var $isLead=false;
    // Can he handle cases?
    var $isCaser=false;
}

class sugarContact extends sugarCommunication {
    var $module = "Contacts";

    function sugarContact(&$confObj, $portal_user) {
        $this->Initialize($confObj, $portal_user);
    }
    
    // Gets available Contact fields.  
    function getAvailableFields() {
        $fields = $this->_getModuleFields();
        
        return $fields;
    }
    
    // This monster will do whatever it can to return a valid contact or lead for a given
    // ID.  It lives on the assumption that the person exists in Sugar.  If he doesn't,
    // for some reason, this class returns a list of false values (2).  If he does,
    // then the class returns list($contact, $flags), where flags tells you if the $contactid
    // passed in refers to the contact directly, a lead the contact is linked to, or
    // a lead directly (in which case $contact will be a lead)
    function getContact($pusername=false) {
        $this->createAutosession();

        $returnFlags = new sugarContactFlags;
        
        $filter = array('portal_name'=>$pusername); // the users' portal username

        // Get contact record, if it exists
        $result = $this->_getEntryList($filter,'date_entered DESC');
        //echo "<pre>"; echo $this->sugarClientProxy->debug_str; echo "</pre>";
        // if it returned a contact, set flags and return
        if ( is_array($result) ) {
            $returnFlags->isContact = true;
            
            // This flag is temporarily set and should be corrected
            //$returnFlags->isPortalUser = (bool)$result['portal_active'];
            
            $this->closeAutosession();
            
            return array($result, $returnFlags);
        }

        /*
        // If this didn't return a contact, get all leads with this TikWiki username,
        // sorted by created date descending
        // We need a sugarLead object to do this part
        $tmpLead = sugarLead($this->sugarConf, $this->portal_user);
        $result1 = $tmpLead->_getEntryList($filter,'date_entered DESC');
        // If this returned a lead, set flags and return
        if ( is_array($result) ) {
            $this->closeAutosession();
            $returnFlags->isLead = true;
            return array($result, $returnFlags);
        }
        if($pusername) {
            // None of these worked, so now we'll search for a Contact with the TikiWiki
            // username stored in Sugar's database
            $result = $this->sugarClientProxy->get_contact_by_portal_user($this->username, $this->password, $pusername);
                // indented because it's just error checking and I don't want to clutter
                // the code
                if ( !$this->_noError('Failed to connect to Sugar') ) {
                    $this->closeAutosession();
                    return array($result, false);
                }

            // if it returned a contact, set flags and return
            if ( is_array($result) ) {
                $returnFlags->isContact = true;

                // This flag is temporarily set and should be corrected
                $returnFlags->isCaser = (bool)$result['portal_active'];

                $this->closeAutosession();

                return array($result, $returnFlags);
            }
        } */

        // If we made it all the way down here, something is seriously wrong
        // we'll fail quietly for right now while we figure out what to do about it
        $this->closeAutosession();
        
        return array(false, false);
    }
    
    // Create a new contact.  $contact should be an array of Contact fields and values.  Not
    // Not every field need be present, default values will be used for any not present.
    // Disabled for now, theoretically works
    /*
    function createNewContact(&$contact) {
        $this->modifyContact($contact);
    }
    */
    
    // Modify contact.  $contact should be an array of Contact fields and values.  Not
    // every field need be present, default values will be used for any not present.
    // The contactID *must* be present, however.
    // Returns the contactID on success, false on failure
    function modifyContact(&$contact) {
        $tmpContact = $this->bindContact($contact);
        
        return $this->_setEntry($tmpContact);
    }
    
    function bindContact($leadArr) {
        $leadFields = $this->availableFields;
        $tmpLead = array();
        
        foreach($leadFields as $field) {
            if ( array_key_exists($field, $leadArr) ) {
                $tmpLead[$field] = $leadArr[$field];
            } else {
                $tmpLead[$field] = '';
            }
        }
        
        $tmpLead['portal_name'] = $this->portal_user;
        $tmpLead['portal_app'] = $this->appName;
        
        return $tmpLead;
    }
        
}


?>
