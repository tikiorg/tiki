<?php
/* @version $Id: sugarApp.php,v 1.3 2005-10-08 10:52:25 michael_davey Exp $ */
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

// This class is the base class that stores all the application logic of the component
global $vtiger_p_use_portal;
$vtiger_p_use_portal = false;

class sugarApp {
    var $request = null;
    var $sortBy = array('number'=>'desc');
    var $appFields = false;
    var $sessionID = false;
    var $moduleFields;
    var $username;


    function Initialize($config) {
        $this->sugarConf = $config;
        $this->soapError = false;

        // vtiger Sanity Check
        // Components should check the $soapError variable and output a useful
        // warning within their own contexts.
        if( ! $this->sugarConf->checkConfig() ) {
            $this->soapError = true;
        }
        if( ! $this->sugarConf->checkSoap($this->sugarConf) ) {
            $this->soapError = true;
        }
        $this->sortBy = $this->sugarConf->getSortBy();

    }


    function getAvailableFields() {
        if(!$this->appFields) {
            $this->sugarComm->getAvailableFields();
            $this->appFields = $this->sugarComm->moduleFields;
        }
        return $this->appFields;
    }



    function getComponentDesc() {
        $descToReturn = '';
        if( isset($this->componentDesc) && $this->componentDesc != '') {
            $descToReturn = $this->componentDesc;
        } else {
            $descToReturn = $this->globalDesc;
        }
        return $descToReturn;
    }


    function _getAppropriateConfigFormField($name,$field) {
        $retField = '';

        switch($field['type']) {
            case 'boolean':
                $retField = '<input name="' . $name . '" type="checkbox"';
                if($field['value']) $retField .= ' checked';
                $retField .= ' />';
                break;
            case 'password':
                $retField = 'Enter new:<br /> <input name="' . $name . '" type="password" /><br /><br />';
                $retField .= 'Confirm: <br /><input name="' . $name . 'confirm" type="password" />';
                break;
            case 'string':
            default:
                $retField = '<input name="' . $name . '" type="text" value="' . $field['value'] . '" />';
                break;
        }

        return $retField;
    }


    // Get all gets the entire basic dataset associated with this object.  If you need to
    // get more than one Sugar module's worth of data for your basic dataset,
    // override getAll in your app class.
    function getAll( $fields = array() ) {
        return $this->sugarComm->getSome(array(), $this->sortBy, $fields);
    }


    function checkConfig() {
        return $this->sugarConf->checkConfig();
    }


    // These are all for storing the sugar session ID in a session variable here.  It should be a configurable item



    // This will be used to start the session.  You *must* call setSessionStartCallback first!
    function startSession() {
        call_user_func($this->sessionStartCallback,$this);
    }

    // This will be used to stop the session.  You *must* call setSessionStopCallback first!
    function stopSession() {
        call_user_func($this->sessionStopCallback,$this);
    }

    function setCrmSessionID($id) {
        $this->sessionID = $id;
    }

    // createSession just logs into the sugar server and gets a session id
    function createSession() {
        $this->sessionID = $this->sugarComm->createSession();
    }

    // closeSession logs out of the sugar server
    function closeSession() {
        $this->sugarComm->closeSession();
    }

    // This is for the callback for session starting.  You should write a global function to pass in that will
    // use the portal's own session management API.  Your global function should take a reference to sugarApp as its
    // only parameter
    function setSessionStartCallback($callback) {
        $this->sessionStartCallback = $callback;
    }

    // As above, so below.  This is to set the callback that will be used to stop the session.
    function setSessionStopCallback($callback) {
        $this->sessionStopCallback = $callback;
    }

    function getCrmSessionID() {
        return $this->sessionID;
    }
}

?>
