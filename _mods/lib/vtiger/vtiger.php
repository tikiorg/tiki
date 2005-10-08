<?php
/* @version $Id: vtiger.php,v 1.2 2005-10-08 16:27:57 michael_davey Exp $ */

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


/*
 *  So this file is just a file that defines constants, globals, and so forth, and
 *     makes sure the rest of the includes get included.  This *is* the core
 *     and *every* portal component needs to have it, and only it.
 *
 */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// Error defines and the soapError class
require_once( "includes/soapError.php" );

// Enable this to debug the component (might show password hashes and usernames)
// NOT SAFE FOR PUBLIC USERS
//define('_DEBUG',_SHOWDEBUG);

// Enable this to show errors (should be safe for public users)
//define('_DEBUG',_SHOWERRORS);

// Enable this to see the soap payloads
// NOT SAFE FOR PUBLIC USERS
define('_DEBUG',_SHOWSOAP);
//define('_DEBUG', '');

// bring in the support code

// bring in the soap stuff and anything else the includes are going to need
if ((!@include( "lib/nusoap/nusoap.php" ))) {
    $smarty->assign('msg', 'nusoap ' . tra("modular extension (mod) not present: please contact the administrator."));
    $smarty->display("error.tpl");
    die;
}

// Core
require_once( "includes/sugarCommunication.php" );

// Components
// Todo: scan dependencies and only include those that are actually needed by any
//       dependencies.  (needs the dependency checking in place)
require_once( "includes/sugarLeads.php" );
require_once( "includes/sugarUser.php" );
require_once( "includes/sugarContact.php" );
require_once( "includes/sugarCase.php" );
require_once( "includes/sugarBug.php" );
require_once( "includes/sugarDownload.php" );
require_once( "includes/sugarNote.php" );

// Application logic
//   brings in the core of the logic layer.  You still need to include the specific file that contains your logic class
require_once( "app/sugarApp.php" );
require_once( "app/sugarAppBug.php" );
require_once( "app/sugarAppCase.php" );
// require_once( "lib/solve/presentation.php" );
?>
