<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_vote.php,v 1.7 2006-10-25 18:47:34 sylvieg Exp $
/* A plugin vote based on tracker
 */
/* fields is optionnal - all the fields except the type suer, group, ip will be used
 */
function wikiplugin_vote_help() {
	$help = tra("Displays some stat of a tracker content, fields are indicated with numeric ids.").":\n";
	$help.= "~np~{VOTE(trackerId=>1,fields=>2:4:5,show_percent=>n|y,show_bar=>n|y,status=>o|c|p|op|oc|pc|opc,float=>right|left, show_stat=n|y)}Title{VOTE}~/np~";
	return $help;
}
function wikiplugin_vote($data, $params) {
	global $smarty, $tikilib, $user, $feature_trackers, $tiki_p_admin_trackers, $tiki_p_view_trackers;
	global $trklib; include_once('lib/trackers/trackerlib.php');
	extract ($params,EXTR_SKIP);

	if ($feature_trackers != 'y' || !isset($trackerId) || !($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	$smarty->assign_by_ref('tracker', $tracker);

	if (isset($float)) {
		$smarty->assign('float', $float);
	} else {
		$smarty->assign('float', '');
	}
	if ($trklib->get_user_item($trackerId, array('oneUserItem'=>'y'))) {
		$smarty->assign('has_already_voted', 'y');
	} else {
		$smarty->assign('has_already_voted', 'n');
	}
	if (empty($fields)) {
		$fields = $trklib->list_tracker_fields($trackerId);
		$ff = array();
		foreach ($fields['data'] as $field) {
			if ($field['type'] != 'u' && $field['type'] != 'I' && $field['type'] != 'g') {
				$ff[] = $field['fieldId'];
			}
		}
		if (!empty($ff)) {
			$params['fields'] = implode(':', $ff);
		}
	}
	if ($tikilib->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_create_tracker_items')) {
		$smarty->assign('p_create_tracker_items', 'y');// to have different vote in the same page
		include_once('lib/wiki-plugins/wikiplugin_tracker.php');
		$vote = wikiplugin_tracker($data, $params);
		$smarty->assign_by_ref('vote', $vote);
	} else {
		$smarty->assign('p_create_tracker_items', 'n');
	}
	if (!isset($show_stat) || $show_stat == 'y') {
		include_once('lib/wiki-plugins/wikiplugin_trackerstat.php');
		$stat = wikiplugin_trackerstat($data, $params);
		$smarty->assign_by_ref('stat', $stat);
	} else {
		$smarty->assign('stat', '');
	}
	return $smarty->fetch('wiki-plugins/wikiplugin_vote.tpl');
}

?>
