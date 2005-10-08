<?php
/* @version $Id: sugarBug.php,v 1.2 2005-10-08 10:23:15 michael_davey Exp $ */

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

// pass $contact to the constructor, you *must* give it a contact, otherwise it will
// behave unpredictably, also needs a pusername, which is the portal_name in Sugar
class sugarBug extends sugarCommunication {
	var $module = "Bugs";
    
    function sugarBug(&$confObj, $pusername=false) {
        $this->Initialize($confObj, $pusername);
    }
    
    function getAvailableFields() {
        return $this->_getModuleFields();
    }
    
    function getSome($fieldstosearch = array(), $orderby = array('number'=>'desc'), $fieldstoReturn = array() ) {
        $this->createAutosession();
        
        $orderby = $this->_getOrderBy($orderby);
        
        $result = $this->_getEntryList($fieldstosearch, $orderby, $fieldstoReturn);
                
        if( is_array($result) ) {
        	 return $result;
        } else {
            return array();
        }
    }
    
    function getOne($caseID) {
        $this->createAutosession();

        $thecase = $this->_getEntry($caseID);

        if($this->err) { 
            $this->closeAutosession();
            
            return false;
        }
        //$notes= $this->_getRelatedNotes($caseID);
       
       
        
        return $thecase;
    }
    
    function createNew($case) {
        $case['status'] = "New";
        return $this->modify($case);
    }
    
    function modify($case) {
        // Let's "purify" the case data
        $tmpArray = $this->_bind($case);
        
        $tmpArray = $this->prepareString($tmpArray);
        
        $result = $this->_setEntry($tmpArray);
        
        return $result;
    }
    
}

?>
