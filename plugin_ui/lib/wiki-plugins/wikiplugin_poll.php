<?php
// Includes a poll
// Usage:
// {POLL(pollId=>1)}Title{POLL}

function wikiplugin_poll_help() {
	$help = tra("Displays the output of a poll, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{POLL(pollId=>1)}Good Poll{POLL}~/np~";
	return $help;
}
function wikiplugin_poll($data, $params) {
	global $smarty, $polllib, $trklib, $tikilib, $dbTiki, $userlib, $tiki_p_admin, $prefs, $_REQUEST, $user;

	extract ($params,EXTR_SKIP);

	if (!isset($pollId)) {
	    $smarty->assign('msg', tra("missing poll ID for plugin POLL"));
	    return $smarty->fetch("error_simple.tpl");
	} else {
	    include_once ('lib/polls/polllib.php');


	    $poll_info = $polllib->get_poll($pollId);
	    $options = $polllib->list_poll_options($pollId);

	    $smarty->assign_by_ref('menu_info', $poll_info);
	    $smarty->assign_by_ref('channels', $options);
	    $smarty->assign_by_ref('poll_title', $data);
	    $smarty->assign('ownurl', $tikilib->httpPrefix(). $_SERVER["REQUEST_URI"]);

	    ask_ticket('poll-form');

	    // Display the template
	    return $smarty->fetch("tiki-plugin_poll.tpl");
	}
}

?>
