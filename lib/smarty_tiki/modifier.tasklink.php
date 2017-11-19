<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Martin Hausner
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
function smarty_modifier_tasklink($taskId, $class_name = "link", $offset = "0", $sort_mode = "priority_desc")
{
	global $tasklib, $user, $prefs, $smarty;
	$tikilib = TikiLib::lib('tiki');

	include_once('lib/tasks/tasklib.php');

	$info = $tasklib->get_task($user, $taskId);
	if ($prefs['feature_community_mouseover'] == 'y') {
		$description = "";

		$my_length = strlen($info['description']);
		$my_pos = 0;
		$line = 0;
		if ($my_length > 0) {
			do {
				$line++;
				$my_pos = strpos($info['description'], "\n", ($my_pos + 1));
			} while (($line <= 15) && ($my_pos != ''));
		}

		if (($my_length >= 1300) || ($line >= 16)) {
			if ($line < 15) {
				$my_pos = 1300;
			}
			$description .= substr($info['description'], 0, min(1300, $my_pos + 1));
			$append = "<br /><center><span class=\'highlight\'>" . tra("Text cut here") . "</span></center>";
		} else {
			$description = $info['description'];
			$append = '';
		}

		// FIXME: Truncated Tiki syntax cannot be parsed.
		$description = TikiLib::lib('parser')->parse_data($description) . $append;

		$tooltipContent = tra("Task") . ' ' . tra("from") . ' <b>' . $info['creator'] . '</b> ' . tra("for") .
			' <b>' . $info['user'] . '</b>.<br />' . tra("Priority") . ': <b>' . $info['priority'] . '</b>, (<b>' .
					$info['percentage'] . '%</b>) ' . tra('done') . '.<br />';

		if ($info['start'] != 0) {
			$tooltipContent .= tra("Start date:") . " " . $tikilib->date_format("%H:%M -- %d. %e. %Y", $info['start']) . "<br />";
		} else {
			$tooltipContent .= tra("Start date:") . " -<br />";
		}

		if ($info['end']) {
			$tooltipContent .= tra("End date:") . " " . $tikilib->date_format("%H:%M -- %d. %e. %Y", $info['end']) . "<br />";
		} else {
			$tooltipContent .= tra("End date:") . " -<br />";
		}

		$tooltipContent .= "<hr />" . $description;

		$smarty->loadPlugin('smarty_function_popup');
		$popupAttributes = smarty_function_popup(['text' => $tooltipContent, 'fullhtml' => true], $smarty);
	} else {
		$popupAttributes = '';
	}
	$content = "<a class='" . $class_name . "' " . $popupAttributes . " href='tiki-user_tasks.php?taskId=" . $taskId . "&amp;tiki_view_mode=view&amp;offset=" .
		$offset . "&amp;sort_mode=" . $sort_mode . "' ";
	if ($info['status'] == 'c') {
		$content .= "style=\"text-decoration:line-through;\"";
	}
	$content .= ">" . htmlspecialchars($info['title']) . "</a>";
	return $content;
}
