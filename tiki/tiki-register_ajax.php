<?
//this script may only be included - so its better to die if called directly.
/*if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}*/
require_once('tiki-setup.php');

function AJAXCheckUserName($name, $nameSequence) {
	global $cpAJAX;
	global $userlib;
	
	$nameMessage_result_node =& $cpAJAX->add_node('nameMessage');
	$nameSequence_result_node =& $cpAJAX->add_node('nameSequence');
	
	if (empty($name)) {
		$nameMessage_result_node->set_data(tra('empty'));
		$nameSequence_result_node->set_data($nameSequence);
	} else if ($userlib->user_exists($name)) {
		$nameMessage_result_node->set_data(tra('User already exists'));
		$nameSequence_result_node->set_data($nameSequence);
	} else {
		$nameMessage_result_node->set_data(tra('Valid').' '.tra('user'));
		$nameSequence_result_node->set_data($nameSequence);
	}
}


function AJAXCheckMail($mail, $mailSequence) {
	global $cpAJAX;
	$mailMessage_result_node =& $cpAJAX->add_node('mailMessage');
	$mailSequence_result_node =& $cpAJAX->add_node('mailSequence');

	if (empty($mail)) {
		$mailMessage_result_node->set_data(tra('empty'));
		$mailSequence_result_node->set_data($mailSequence);		
	} else if (!eregi("^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$", $mail)) {
		$mailMessage_result_node->set_data(tra('This is not a valid mail adress'));
		$mailSequence_result_node->set_data($mailSequence);		
	} else {
		$mailMessage_result_node->set_data(tra('Valid').' '.tra('mail adress'));
		$mailSequence_result_node->set_data($mailSequence);		
	}
}

require_once('lib/cpaint/cpaint2.inc.php');
$cpAJAX = new cpaint();
$cpAJAX->register('AJAXCheckUserName');
$cpAJAX->register('AJAXCheckMail');
$cpAJAX->start();
$cpAJAX->return_data();

?>