<?php
/**
 * @version $Id: solvebugs.php,v 1.9 2005-10-11 23:23:36 michael_davey Exp $
 * @package TikiWiki
 * @subpackage Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once ( 'lib/solve/db/vtiger_portal_configuration.php' );
require_once ( "lib/solve/db/vtiger_portal_bug_fields.php" );

define('_MYNAMEIS', 'bugs');

if ((!@include( 'lib/vtiger/vtiger.php' ))) {
    $smarty->assign('msg', 'vtiger ' . tra("modular extension (mod) not present: please contact the administrator."));
    $smarty->display("error.tpl");
    die;
}

require_once('lib/tikiaccesslib.php');
$access->check_user($user);

$presentation = new TikiPresentation();
// we already have $option from solve.php global section
$task = solve_get_param( $_REQUEST, 'task' );
$sortBy = solve_get_param( $_REQUEST, 'order_by' );
$dbtable = new VtigerBugFields();
$config = new SolveConfiguration( 'TikiWiki', $presentation, $option, $task, $sortBy, $dbtable );
$presentation->setConfig( $config );

$bugApp = new VtigerAppBug($config, $user);

$caseID = solve_get_param( $_REQUEST, 'caseID' );
$caseID = $caseID == null ? 0 : $caseID;

$bugApp->login();

if(isset($SoapError) && isset( $bugApp->$soapError) &&  $bugApp->$soapError )
    $task = "error";

$bugApp->setSessionStartCallback('startCrmSession');
$bugApp->setSessionStopCallback('stopCrmSession');

// Session management, logs into the soap server and gets the session ID
$bugApp->startSession();

switch( $task ) {
    case "search":
        $access->check_page($user, array('feature_crm'), array('vtiger_p_search_bugs'));
        $columnData = $dbtable->getColumnData($bugApp);
        $columns = $columnData['selected'];
        $searchcolumns = array();
        foreach($columns as $column) {
            if($column['searchable'] == 1) {
                if( isset( $_REQUEST[$column['field']] ) ) {
                    $searchcolumns[$column['field']] = $_REQUEST[$column['field']];
                } else {
                    $searchcolumns[$column['field']] = '';
                }
            }
        }

        $bugs = $bugApp->search($searchcolumns);
        
        $presentation->RenderList($bugs, $searchcolumns, $columnData);
        break;
    case "new":
        $caseID = false;
        $task = "edit";
        // fall-through
    case "view":
    case "edit":
        if( !isset($caseID) || ! $caseID) {
            $caseID = false;
            $access->check_page($user, array('feature_crm'), array('vtiger_p_create_bugs'));
        } else {
            $access->check_page($user, array('feature_crm'), array("vtiger_p_".$task."_bugs"));
        }
        $columns = $dbtable->getColumnData($bugApp);
        
        $msg = solve_get_param( $_REQUEST, 'msg' );
        $smarty->assign( 'msg', $msg );

        list($thisbug,$notes) = $bugApp->get($caseID);
        $presentation->Render($columns, $thisbug, $notes, 'bugs', $task);
        break;
    case "newnote":
        $access->check_page($user, array('feature_crm'), array('vtiger_p_edit_bugs'));
        // First add the new note
        $cases = $bugApp->createNote($_POST['caseID'], $_POST, $_FILES);
        
        //echo $cases . "<br />";

        // broke error checking
        if($bugApp->soapError) {
            //echo $Comm->getErrorText();
            $access->redirect($feature_server_name . "/solve/" . _MYNAMEIS . "/edit/" . $_POST['caseID'], 'There was an error processing your request.');
        }
        $access->redirect($feature_server_name . "/solve/" . _MYNAMEIS . "/edit/" . $_POST['caseID'], 'Note saved!');
        break;
    case "error":
        echo $config->getBrokeMessage();
        break;
    case "saveedit":
        $access->check_page($user, array('feature_crm'), array('vtiger_p_edit_bugs'));
        if( $_POST['button']=='Save' ) {
			$_POST['release'] = $_POST['release_name'];
            $cases = $bugApp->modify($_POST);
            $access->redirect($feature_server_name . "/solve/" . _MYNAMEIS . "/edit/" . $cases['id'], 'Bug saved!');
        } else {
            $access->redirect($feature_server_name . "/solve/$option", "Edit bug is cancelled.");
        }
        break;
    case "savenew":
        $access->check_page($user, array('feature_crm'), array('vtiger_p_create_bugs'));
        if( $_POST['button']=='Save' ) {
            $_POST['release'] = $_POST['release_name'];
            $cases = $bugApp->create($_POST);
            $access->redirect($feature_server_name . "/solve/" . _MYNAMEIS . "/edit/" . $cases['id'], 'Bug saved!');
        } else {
            $access->redirect($feature_server_name . "/solve/$option", "New bug is cancelled.");
        }
        break;
    case "download":
        $access->check_page($user, array('feature_crm'), array('vtiger_p_view_bugs'));
        if(!empty($_REQUEST['noteid'])) {
			$theFile = $bugApp->getNoteAttachment($_REQUEST['moduleid'],$_REQUEST['noteid']);
            $file = base64_decode($theFile['file']);

            $discard = ob_end_clean();
            $content_dispo_header = "Content-Disposition: attachment; filename=\"".$theFile['filename']."\"";

            header($content_dispo_header);
            header("Content-Type: application/force-download");
            header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
            header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
            header("Content-Length: ".strlen($file));
			echo $file;
            die();
		} else {
            $access->redirect($feature_server_name . "/solve/$option", "No File To Download.");
        }
        break;
    case "refresh":
		$bugApp->stopSession();
        $bugApp->startSession();
        break;
	case "home":
    default:
        $access->check_page($user, array('feature_crm'), array('vtiger_p_list_bugs'));
        $bugs = $bugApp->getAll();
        
        $msg = solve_get_param( $_REQUEST, 'msg' );
        $smarty->assign( 'msg', $msg );
        $columns = $dbtable->getColumnData($bugApp);
        
        $presentation->RenderList($bugs, array(), $columns, 'home');
        break;
}

$bugApp->logout();

// Session management function
function startCrmSession(&$bugApp) {
    if(!isset($_SESSION)){
        session_start();
    }
    // Check to see if we already have a crm session
    if(isset($_SESSION['crm_session'])){
        $bugApp->setCrmSessionID($_SESSION['crm_session']);
    } else {
        // If not, create one and get going
        $bugApp->createSession();
        $_SESSION['crm_session'] = $bugApp->getCrmSessionID();
    }
}

function stopCrmSession(&$bugApp) {
    if(!isset($_SESSION)){
        session_start();
    }
    if(empty($bugApp->sessionID) && isset($_SESSION['crm_session'])){
        $bugApp->setCrmSessionID($_SESSION['crm_session']);
    }
    $bugApp->closeSession();
    unset($_SESSION['crm_session']);
}

?>
