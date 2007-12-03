<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/author/wiki-plugins/wikiplugin_author.php,v 1.1 2007-12-03 23:05:09 sylvieg Exp $
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_author_help() {
	$help = tra("Display wiki text if user is in one of the author or a contributor").":\n";
	$help.= "~np~<br />{AUTHOR(contributor=y|n)}wiki text{ELSE}alternate text{AUTHOR}~/np~";
	return $help;
}
function wikiplugin_author($data, $params) {
	global $user, $page, $tikilib;
	global $wikilib; include_once('lib/wiki/wikilib.php');
	if (empty($page))
		return;
	if (strpos($data, '{ELSE}')) {
		$dataelse = substr($data, strpos($data,'{ELSE}')+6);
		$data = substr($data, 0, strpos($data,'{ELSE}'));
	} else {
		$dataelse = '';
	}
	if (empty($params['contributor']) || $params['contributor'] != 'y') { // looking for the author
		$page_info = $tikilib->get_page_info($page);
		$ok = ($user == $page_info['creator'])? true: false;
	} else { //looking for a contributors
		$contributors = $wikilib->get_contributors($page, '');
		$ok = in_array($user, $contributors);
	}
	return $ok? $data: $dataelse;
}
?>
