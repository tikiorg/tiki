<?
//this script may only be included - so its better to die if called directly.
/*if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}*/
require_once('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

function AJAXCheckUserName($name) {
	global $userlib;
	
	$objResponse = new xajaxResponse();
	
	if (empty($name)) {
		$objResponse->addAssign("checkfield", "innerHTML", tra("empty"));
	} else if ($userlib->user_exists($name)) {
		$objResponse->addAssign("checkfield", "innerHTML", tra('User already exists'));
	} else {
		$objResponse->addAssign("checkfield", "innerHTML", tra('Valid').' '.tra('user'));
	}

	return $objResponse;
}


function AJAXCheckMail($mail) {

	$objResponse = new xajaxResponse();

	if (empty($mail)) {
		$objResponse->addAssign("checkmail", "innerHTML", tra("empty"));
	} else if (!eregi("^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$", $mail)) {
		$objResponse->addAssign("checkmail", "innerHTML", tra('This is not a valid mail adress'));
	} else {
		$objResponse->addAssign("checkmail", "innerHTML", tra('Valid').' '.tra('mail adress'));
	}

	return $objResponse;
}

// xajax
$ajaxlib->setRequestURI('tiki-register_ajax.php');
$ajaxlib->registerFunction('AJAXCheckUserName');
$ajaxlib->registerFunction('AJAXCheckMail');
$ajaxlib->processRequests();

?>