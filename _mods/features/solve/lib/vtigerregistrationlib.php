<?php
/**
 * @version $Id: vtigerregistrationlib.php,v 1.5 2005-10-01 15:32:42 michael_davey Exp $
 * @package TikiWiki
 * @subpackage Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once ('tiki-setup.php');
require_once( 'lib/db/tikitable.php' );
require_once( 'lib/db/tiki_registration_fields.php' );

require_once( 'lib/solve/solvelib.php');
require_once( 'lib/solve/configuration.php' );

$option = '';
$sh = new SolveLib( $dbTiki, $option, '.' );

define('_MYNAMEIS', 'vtigerregistration');
require_once( "lib/vtiger/vtiger.php" );

class vtigerRegistrationLib {

    function vtigerRegistrationLib($db) {
        $this->_db = new TikiRegistrationFields($db);
        $this->_config = new SolveConfiguration('TikiWiki');
    }

/**
 *  This is a callback from tikisignal event handler.
 *  It is called before the tiki user is created.
 */
function callback_vtiger_registration( $raisedBy, &$data) {
	global $allowRegister;

        if ($allowRegister != 'y') {
            header("location: index.php");
            exit;
            die;
        }

	$lead = new VtigerLead($this->_config);
	if( $lead->err ) {
            echo "<script> alert('".$lead->getErrorText()."'); window.history.go(-1); </script>\n";
            exit();
        }
        $this->_config->availableFields = $lead->getAvailableFields();

    $this->_db->setQuery("SELECT * FROM tiki_registration_fields WHERE `show`=1");
    $rows = $this->_db->loadObjectList();

    $contact = array();

    foreach($rows as $row) {
        if( in_array($row['field'], $lead->availableFields) ) {
			$contact[$row['field']] = $data[$row['field']];
		}
	}	
    $firstname = '';
    $lastname = '';
    
    $customName = false;
    
    if( array_key_exists('first_name', $data) ) {
        $firstname = $data['first_name'];
        $customName = true;
    }
    
    if( array_key_exists('last_name', $data) ) {
        $lastname = $data['last_name'];
        $customName = true;
    }
    
    if( $customName ) {
        $data['username'] = trim($firstname . ' ' . $lastname);
    }
    
    $customEmail = false;
    
    if( array_key_exists('email2', $data) ) {
        $email = $data['email2'];
        $customEmail = true;
    }
    
    if( array_key_exists('email1', $data) ) {
        $email = $data['email1'];
        $customEmail = true;
    }
    
    if( $customEmail ) {
        $data['email'] = $email;
    }
    $data['contact'] = $contact;
    $data['comm'] =& $lead;
    return true;
}

/**
 *  This is a callback from tikisignal event handler. 
 *  It is called after the tiki user is created.
 */
function callback_vtiger_save_registration($raisedBy, &$data) {
    global $userlib;

    // Create the session.  We have to do it separately because we're authenticating
    // as a Lead, but we want to change the username after authenticating.  This is
    // a hack that's not likely to go away any time soon.
    $lead =& $data['comm'];
    $lead->createSession();
    
    $contact =& $data['contact'];

    // Setup vtiger fields
    $contact['lead_source'] = "Web Site";
    $contact['portal_name'] = $data['name'];
    if( !array_key_exists('email1', $contact) ) {
        $contact['email1'] = $data['email'];
    }

    // Get new record's ID
    $data['contactid'] = $userlib->get_user_id($data['name']);

    // Set the new username
    $lead->setPortalUser($data['name']);
    
    // Create the lead
    $newLead = $lead->createNewLead($data['contact']);

    if( $lead->err ) {
        global $smarty;
        $smarty->assign('msg', tra('There was a problem storing your information, but your registration has been successful.') );
        $smarty->display("error.tpl");
        die;
    }
    
    // Close the session to vtiger crm.
    $lead->closeSession();
    
    $data['crmid'] = $newLead;

    return true;
}

} // class

global $dbTiki;
$vtigerregistrationlib = new VtigerRegistrationLib($dbTiki);

?>
