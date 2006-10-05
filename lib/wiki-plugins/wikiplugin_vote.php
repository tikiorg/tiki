<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_vote.php,v 1.1 2006-10-05 14:21:17 sylvieg Exp $
/* A plugin vote based on tracker
 */
function wikiplugin_vote_help() {
	$help = tra("Displays some stat of a tracker content, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{VOTE(trackerId=>1,fields=>2:4:5,show_percent=>y,show_bar=>n,status=>o|c|p|op|oc|pc|opc,float=>right|left)}Title{VOTE}~/np~";
	return $help;
}
function wikiplugin_vote($data, $params) {
	global $smarty;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	extract ($params,EXTR_SKIP);

	if (!isset($trackerId)) {
		$smarty->assign('msg', tra("missing tracker ID for plugin TRACKER"));
		return $smarty->fetch("error_simple.tpl");
	}
	$tracker = $trklib->get_tracker($trackerId);
	$smarty->assign_by_ref('tracker', $tracker);

	if (isset($float)) {
		$smarty->assign('float', $float);
	}
	include_once('lib/wiki-plugins/wikiplugin_tracker.php');
	$vote = wikiplugin_tracker($data, $params);
	$smarty->assign_by_ref('vote', $vote);

	include_once('lib/wiki-plugins/wikiplugin_trackerstat.php');
	$stat = wikiplugin_trackerstat($data, $params);
	$smarty->assign_by_ref('stat', $stat);
	return $smarty->fetch('wiki-plugins/wikiplugin_vote.tpl');
}

?>
