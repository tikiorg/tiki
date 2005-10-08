<?php
/* @version $Id: sugarUser.php,v 1.2 2005-10-08 10:23:15 michael_davey Exp $ */

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

// since this file should never be included by anything *but* sugarportal.inc.php, we'll
// be lazy and not include the file that contains the definition of sugarCommunication
// Sue me.

class sugarUser extends sugarCommunication {
    function sugarUser(&$confObj, $portal_user) {
        $this->Initialize($confObj);
    }
    
    // Get a list of all Sugar users
    function getSugarUsers() {
        return $this->_getEntryList('Users');
    }
}

?>
