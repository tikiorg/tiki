<?
include_once("lib/init/initlib.php");
require_once("tiki-setup_base.php");


require_once('lib/cpaint/cpaint2.inc.php');
require_once ('lib/chat/chatlib.php');

$cp = new cpaint();

$cp->register('list_new_messages');
$cp->register('list_online_friends');
$cp->register('send_msg');

$cp->start();
$cp->return_data();

function _send_ajax_list(&$cp, $list, $name) {
    for ($i=0; $i < sizeof($list); $i++) {
	$item = $list[$i];

	$ajaxObj =& $cp->add_node($name);

	foreach ($item as $field => $value) {
	    $r =& $ajaxObj->add_node($field);
	    $r->set_id($field . '_' . $i);
	    $r->set_data($value);
	}
    }
}

function list_new_messages() {
    global $cp, $user, $chatlib;
    require_once("lib/chat/chatlib.php");

    $messages = $chatlib->get_private_messages($user);

    _send_ajax_list($cp, $messages, 'message');
}

function list_online_friends() {
    global $cp, $user, $tikilib;

    $friends = $tikilib->list_online_friends($user);

    _send_ajax_list($cp, $friends, 'friend');
}

function send_msg($to, $msg) {
    global $user, $chatlib;

    require_once("lib/chat/chatlib.php");

    $chatlib->send_private_message($user, $to, $msg);
}

?>
