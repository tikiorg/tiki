<?php
/* @version $Id: sugarAppCase.php,v 1.3 2005-10-11 12:31:41 michael_davey Exp $ */

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

class VtigerAppCase extends sugarApp {
    // The standard object that represents the app data
    var $sugarComm = null;
    // The contact object--needed for some apps
    var $sugarContact = null;
    // The sugar session to be shared among all communication objects
    var $sugarSessionID = null;
    var $username = null;
    
    function VtigerAppCase($request = false, $pusername = '') {
        $this->Initialize($request);

        $this->myusername = $pusername;

        $this->sugarComm = new sugarCase($this->sugarConf, $this->myusername);
        $this->sugarContact = new sugarContact($this->sugarConf, $this->myusername);
        
    }
    
    function login() {
        global $vtiger_p_use_portal, $smarty;
        if( $this->sugarComm->createSession() ) {
            $this->sugarSessionID = $this->sugarComm->getCrmSessionID();
            $this->sugarContact->setCrmSessionID($this->sugarSessionID);

            list($contact, $contactFlags) = $this->sugarContact->getContact($this->username);
            $this->contact = $contact;

            $this->contactFlags = $contactFlags;

            $vtiger_p_use_portal = true;
        } else {
            // do error handling here
            $vtiger_p_use_portal = false;
            $smarty->assign('msg', tra("You do not have permission to use this feature").": vtiger_p_use_portal");
            $smarty->display("error.tpl");
            die;
        }    
    }
    
    function logout() {
        global $vtiger_p_use_portal;
        $this->sugarSessionID = false;
        $this->sugarContact->closeSession();
        $this->sugarComm->setCrmSessionID(false);
        $this->contactFlags = false;
        $this->sugarAuthorizedPortalUser = false;
        $vtiger_p_use_portal = false;
    }
    
    function create($record) {
        return $this->sugarComm->createNew($record);
    }

    function modify($record) {
        return $this->sugarComm->modify($record);
    }
    
    function get($recordID) {
        if( !isset($recordID) || !$recordID ) {
            return array(null,null);
        }

        $bug = $this->sugarComm->getOne($recordID);
        $notes = $this->getNotes($recordID);
        return array($bug,$notes);
    }
    
    function getNotes($recordID, $selectFields = array()) {
        $sugNote = new sugarNote($this->sugarConf, $this->username);
        
		$sugNote->setCrmSessionID($this->sugarSessionID);
        
        return $sugNote->getAllNotes($this->sugarComm->module,$recordID, $selectFields);
    }

    function getNoteAttachment($bugID, $noteID) {
        $sugNote = new sugarNote($this->sugarConf, $this->username);
        $throwAway = $this->getAll();

        $sugNote->setCrmSessionID($this->sugarSessionID);

        $fileContents = $sugNote->getNoteAttachment("Cases", $bugID, $noteID);

        $retArray = $fileContents;
        return $retArray;
    }

    function createNote($recordID, $note, $files) {
        $sugNote = new sugarNote($this->sugarConf, $this->username);
        $this->sugarComm->getSome();
        
        $sugNote->setCrmSessionID($this->sugarSessionID);
        return $sugNote->createRelatedNote($this->sugarComm->module, $note, $recordID, $files);
    }

    function search($filter) {
        return $this->sugarComm->getSome($filter,$this->sortBy);
    }
}
?>
