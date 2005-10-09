<?php
/* @version $Id: sugarCommunication.php,v 1.3 2005-10-09 19:04:33 michael_davey Exp $ */

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

/*
  sugarCommunication is the class that handles communicating with Sugar through the SOAP
    interface.  To use it, you instantiate the class, passing it your current config
    object.  Then call sugarCommunication::createSession() before calling anything else.
    Finally, when you are done with the class, call sugarCommunication::closeSession().
    
    All functions return either true or the value of the result of the operation on success,
    false on failure.
    
    Do NOT override Initialize, but DO call it before you call anything else, and pass
    it a config object.  It is recommended you do this from your constructor in your
    derived class.  Try not to dump it on class users, ok?
*/

/*  This array is solely to handle sorting.  You see, the soap server can't sort on all
    the fields that we display in our lists here, so for those fields, we do the sorting
	here.  This array contains those fields.
*/

$illegalSortFields = array(
						   // Case fields
						   'created_by_name',
						   'modified_by_name',
						   'assigned_user_name',
						   'team_name',
						   // Bug fields
						   'fixed_in_release_name',
						   'fixed_in_release',
						   'release_name'
						   );


class sugarCommunication {
    var $sugarConf = null;
    var $sugarClient = null;
    var $sugarClientProxy = null;
    var $module = '';
    var $err = false;
    var $errText = "";
    var $sessionStarted = false;
    var $result = '';
    var $autoSession = false;
    var $sessionID = false;
    var $portal_user = false;
    var $sugarNote = false;
    var $moduleFields = null;

    // This is part of the sort hack
    var $clientSortBy = array();
        
    function Initialize($confObj, $puser = false) {
        $this->sugarConf = &$confObj;
      
        $this->sugarAuth = $this->sugarConf->getAuth();
        
        $this->portal_user = $puser;
        
        $this->sugarClientProxy = $this->getProxy();
        if($this->module != 'Notes'){
                $this->sugarNote = new sugarNote($confObj, $puser);
        }
    }
    
    function setPortalUser($puser) {
        $this->portal_user = $puser;
    }
    
    // These two functions make it possible to share a sugar session across multiple
    // instances of sugarCommunication children.  They also make it possible to cache
    // the sugar session ID somewhere else, you know, for performance reasons, by
    // reusing an existing sugar session ID until you're done with it.
    function setCrmSessionID($newID) {
        $this->sessionStarted = true;
        $this->sessionID = $newID;
    }

    function getCrmSessionID() {
        return $this->sessionID;
    }
        
    function getErrorText() {
        return $this->errText;
    }
    
    // This function prepares a string to be sent over soap to sugar and should
    // be called on all strings sent.  It takes an array and will prepare each string
    // in the array.  Make sure you don't send any important data like row identifiers
    // to this function because it might mangle it slightly.
    
    // It should leave any alphanumeric or numeric information alone, it's purpose is to
    // make sure information is alphanumeric and doesn't include any control codes that
    // might be used to subvert the Sugar server or the MySQL server
    function prepareString($listofStrings) {
        $newArray = array();
        
        if( is_array($listofStrings) ) {
            foreach($listofStrings as $key=>$value) {
                if(is_string($value)) {
                    if( get_magic_quotes_gpc() ) {
                        $value = stripslashes($value);
                    }
                }
                
                $newArray[$key] = $value;
            }
            return $newArray;
        } else {
            return array();
        }        
    }

    // call createSession from the class user to create a new session and disable autoSession.
    // If you don't do this, then every single call to any other soap function will result
    // in three calls, the create_session call, the call wanted, and the close_session call.
    // If you only need to do one thing, fine, just call the function needed.  If you need
    // to do several, use this function to create a session, then make sure you close it
    // when you're done.
    // returns true on success, false on failure
    function createSession() {
        $result = $this->sugarClient->call('portal_login',
                        array(
                            'portal_auth'=>$this->sugarAuth,
                            'user_name'=>$this->portal_user,
                            'application_name'=>$this->sugarConf->getAppName()
                        )                        
                    );

        if ($this->_noError($result['error'])) {
            $this->sessionStarted = true;
            $this->sessionID = $result['id'];
           
            return true;
        } else {
            $this->_showErrors(array(
                            'sugar_auth'=>$this->sugarAuth,
                            'portal_user'=>$this->portal_user,
                            'app_name'=>$this->sugarConf->getAppName()
                        )
                   );
                
            return false;
        }
    }
    
    function closeSession() {
        $result = $this->sugarClientProxy->portal_logout($this->sessionID);
        
        if ($this->_noError($result)) {
            $this->sessionID = false;
            $this->sessionStarted = false;
            return true;
        } else {
            $this->sessionStarted = false;
            return false;
        }
    }

    function createAutosession() {
        // handle creating a session automagically if needed
        if( !$this->sessionStarted ) { 
            $testCreateSession = $this->createSession();
            if( $testCreateSession == true) {
                $this->autoSession = true;
            }
        }
    }

    function closeAutosession() {
    	//signals something invalid (login, portal, or session)
    	if($this->err->sugarNumber >= 10 && $this->err->sugarNumber < 20){
        	if( $this->autoSession ) {
            	$result = $this->closeSession();
            	$this->autoSession = false;
            	
       		 }
    	}
    }
    
    function _setEntry($entrylist, $module = '') {
        $this->createAutosession();
        
        $entrylistnv = $this->_createNameValue($entrylist);
        
        //$this->_showDebug($entrylistnv);
        // portal_set_entry is executed on the sugar server and should return
        // the contact ID on success
        if(empty($module)){
        	$module = $this->module;
        }
        $result = $this->sugarClientProxy->portal_set_entry($this->sessionID, $module, $entrylistnv);
        
        //echo "<pre>" . $this->sugarClientProxy->debug_str . "</pre>";
        //echo "<pre>" . $this->sugarClientProxy->response . "</pre>";
        if ($this->_noError($result['error']) ) {
            $entrylist['id'] = $result['id'];
           
            
            $this->_showDebug($entrylist);
            
            return $entrylist;
        }
        
        $this->_showErrors($result);
        $this->closeAutosession();
        
        return false;
    }
    
    function _getEntry($id, $selectFields = array()) {
        $this->createAutosession();
        $selectFields = $this->availableFields;

        $tmpReturn = $this->_getEntryList(array('id'=>$id));
        
        return $tmpReturn[0];

        
        /*  This stuff is kept for reference purposes but is not needed anymore
        if ($this->_noError($result['error']) ) {
        	$entry = $result['entry_list'];
			$fields = $this->_convertToDict($entry[0]);
            return $fields;
        }
        $this->_showErrors();
        $this->closeAutosession();
        
        return false;*/
    }
    
    function _getEntryList($filter=array(), $orderBy='', $selectFields=array()) {

        $this->createAutosession();
        $where = $this->_getWhereFromFilter($filter);
        
        // portal_set_entry is executed on the sugar server and should return
        // the contact ID on success

        // check that vtiger server is accepting SOAP portal method calls
        if ( ! method_exists($this->sugarClientProxy, "portal_get_entry_list") ) {
            global $smarty;
            $smarty->assign('msg', tra("vtiger server not accepting portal conenctions: please contact the administrator."));
            $smarty->display("error.tpl");
            die;
        }

        $result = $this->sugarClientProxy->portal_get_entry_list($this->sessionID, $this->module, $where, $orderBy,$selectFields);
        
        if ($this->_noError($result['error']) ) {
            $this->closeAutosession();
            $results = array();
            if(!empty($result['entry_list'])){
	            foreach($result['entry_list'] as $entry){
	            	$results[] = $this->_convertToDict($entry);
	            }
				// I know this is slow, but it's meant to be easy to find when it's time to
				// remove/update the sort hack, and this is part of the sort hack
				$tmpResults = array();
				foreach($this->clientSortBy as $key=>$value) {
					foreach($results as $innerkey=>$singleResult) {
						// Create an array using the key that we intend to sort by
						$tmpResults[$innerkey] = $singleResult[$key];
					}
					// Now sort it
					if($value == 'desc') {
						arsort($tmpResults);
					} else {
						asort($tmpResults);
					}
					reset($tmpResults);
					$innertmpResults = array();
					foreach($tmpResults as $innerkey=>$innervalue) {
						$innertmpResults[] = $results[$innerkey];
					}
					$results = $innertmpResults;
					break;
				}
				// End sort hack
            }
            return $results;
        }
        
        $this->_showErrors();
  
        $this->closeAutosession();
        
        return false;
    }
    
    function _convertToDict($entry) {
   		if(empty($entry)){
   			return array();	
   		}
       	 $tmplist = array();
      		$name_value_list = $entry['name_value_list'];
      		if(isset($entry['id'])){
      			$tmplist['id'] = $entry['id'];
      		}
      		foreach($name_value_list as $name_value) {		
        		$tmplist[$name_value['name']] = $name_value['value'];
      		}
     	return $tmplist;
    }
    
    function _getWhereFromFilter($filter) {
        $tempfields = array();
        $tmpMod = strtolower($this->module);
    
        // A wereclause is a beast that turns into a human and a clause.
        // We construct one here.
        foreach($filter as $key=>$value) {
            if(isset($key)) {
                switch($key) {
                    case 'contact_id':
                        $tempfields[] = "$tmpMod.$key LIKE '$value'";
                    case 'name':
                        $tempFields[] = "$tmpMod.$key LIKE '%$value%'";
                        break;
                    default:
                        $tempFields[] = "$tmpMod.$key='$value'";
                        break;
                }
            }
        }
        if(!empty($tempFields)){
       	 $thisquery = implode(" AND ",$tempFields);
       	 return $thisquery;
        }        
        return '';

        
    }
        
    // Takes an array of $key = field, $value = asc or desc and converts it to
    // an order by clause for the SQL statement.  Should be rewritten when we're no
    // longer writing parts of the SQL statements for the Sugar server on the client
    // side
    function _getOrderBy($sortBy) {
        global $illegalSortFields;

        $orderby = '';
		
        if(is_array($sortBy)) {
            // a quick hack to get the first sortby and use it and only it
            foreach($sortBy as $key=>$value) {
                if( in_array($key,$illegalSortFields) ) {
                    $this->clientSortBy[$key] = $value;
                } else {
                    $orderby = $key . " " . strtoupper($value);
                }
                break;
            }
        } else {
            $orderby = 'number DESC';
        }
        
        return $orderby;
    }

    function _getModuleFields() {
        $this->createAutosession();
  		
        // $this->_showErrors();
        $result = $this->sugarClientProxy->portal_get_module_fields($this->sessionID, $this->module);
        if ($this->_noError($result['error'])) {
            $tmpFields = array();
            
            $this->moduleFields = $result['module_fields'];
            
            foreach($result['module_fields'] as $field) {
                $tmpFields[] = $field['name'];
            }
            
            $this->availableFields = $tmpFields;
            $this->closeAutosession();
        
            //$this->_showDebug($this->moduleFields);
            
            return $this->availableFields;
        }
        $this->closeAutosession();
        
        $this->_showErrors($result);
        //$this->_showDebug($result);
        
        return false;    
    }
    
    function _getRelatedNotes($modID, $selectFields = array()) {
        $this->sugarNote->setCrmSessionID($this->sessionID);
        return $this->sugarNote->getAllNotes($this->module,$modID, $selectFields);
    
    }
     
    function _createRelatedNote($modID, $note) {
        $this->sugarNote->setCrmSessionID($this->sessionID);
        $this->sugarNote->createRelatedNote($this->module, $note, $modID);
    
    }
    
    function _createNameValue($valueArr) {
        $tmpArr = array();
        
        foreach($valueArr as $name=>$value) {
            $tmpArr[] = array('name'=>$name, 'value'=>$value);
        }
        
        return $tmpArr;
    }
    
    function _showDebug(&$controlVar) {
        switch(_DEBUG) {
            case _SHOWERRORS:
                // do nothing, this is for debug information only
                break;
            case _SHOWDEBUG:
                echo '<div style="text-align: left"><pre>'; var_dump($controlVar); echo "</pre></div>";
                break;
            default:
                // do nothing
        }
    
    }
    
    function _showErrors($debugstring=false) {
        switch(_DEBUG) {
            case _SHOWERRORS:
                echo $this->errorObj->getErrorText();
                break;
            case _SHOWDEBUG:
                echo $this->errorObj->getErrorText();
                if($debugstring) {
                    echo '<div style="text-align: left"><pre>'; var_dump($debugstring); echo "</pre></div>";
                }
                break;
            case _SHOWSOAP:
                echo $this->errorObj->getErrorText(_NUSOAP_ERROR);
                echo $this->errorObj->getDebugText();
                break;
            default:
                // do nothing
        }
    }
    
    function _noError($error) {
        $this->errorObj = new soapError($this->sugarClientProxy,$error);
        
        if ($this->errorObj->hasError()) {
            // There is an error.  The caller should get the errorObj.
            // todo: make the class support a list of errorObjs
            $this->err = true;
        }
        
        return !$this->err;    
    }
    
    // Binds this module to a web form, filtering out any $_POST items that don't
    // exist in this sugarCommunication module.  Also prepares the string for storage
    // and does some security checks on the strings with the prepareString method
    function _bind($leadArr) {
        if(!isset($this->availableFields))
            $this->_getModuleFields();

        $leadFields = $this->availableFields;
        $leadFields[] = 'id';
    
        $tmpLead = array();
        
        foreach($leadFields as $field) {
            if ( array_key_exists($field, $leadArr) ) {
                $tmpLead[$field] = $leadArr[$field];
            } else {
                $tmpLead[$field] = '';
            }
        }

        //$this->_showDebug($tmpLead);
        
        return $this->prepareString($tmpLead);
    }
    
    function getProxy() {
        $configDir = $this->sugarConf->getServer() . '?wsdl';
        
        $this->sugarClient = new soapclient($configDir, true);
        
        // Check for an error
        $err = $this->sugarClient->getError();
        if ($err) {
            $this->err = true;
            // Display the error
            $this->errText .= '<p><b>Constructor error: ' . $err . '</b></p>';
            return false;
        }
        
        $tmpProxy = $this->sugarClient->getProxy();
        
        if(!$tmpProxy) {
            $this->err = true;
            echo "Failed";
            $this->errText .= "<p><b>Couldn't get proxy</b></p>";
            return false;
        }
        
        return $tmpProxy;
    }
}

?>
