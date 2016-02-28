<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
include_once ('lib/tasks/tasklib.php');
$messulib = TikiLib::lib('message');

$access->check_feature('feature_tasks', '', 'community');
$access->check_user($user);
$access->check_permission('tiki_p_tasks');

if (isset($tiki_p_tasks_admin) && $tiki_p_tasks_admin == 'y') {
	$task_admin = true;
} else {
	$task_admin = false;
}
$smarty->assign('task_admin', $task_admin);
$comp_array = array();
$comp_array_p = array();
for ($i = 0; $i < 101; $i+= 10) {
	$comp_array[] = $i;
	$comp_array_p[] = $i . '%';
}
$smarty->assign('comp_array', $comp_array);
$smarty->assign('comp_array_p', $comp_array_p);
if (!isset($_REQUEST['taskId'])) {
	$_REQUEST['taskId'] = 0;
}
//default values for show options
if (!isset($_SESSION['show_trash'])) $_SESSION['show_trash'] = false;
if (!isset($_SESSION['show_completed'])) $_SESSION['show_completed'] = true;
if (!isset($_SESSION['show_private'])) $_SESSION['show_private'] = true;
if (!isset($_SESSION['show_received'])) $_SESSION['show_received'] = true;
if (!isset($_SESSION['show_submitted'])) $_SESSION['show_submitted'] = true;
if (!isset($_SESSION['show_shared'])) $_SESSION['show_shared'] = true;
if (!isset($_SESSION['show_admin'])) $_SESSION['show_admin'] = false;
if (!isset($_SESSION['admin_mode'])) $_SESSION['admin_mode'] = false;
if (isset($_REQUEST['move_task_into_trash'])) {
	$_REQUEST['update_tasks'] = true;
	$_REQUEST['action'] = 'move_marked_to_trash';
	$_REQUEST['task'] = array($_REQUEST['taskId']);
}
if (isset($_REQUEST['remove_task_from_trash'])) {
	$_REQUEST['update_tasks'] = true;
	$_REQUEST['action'] = 'remove_marked_from_trash';
	$_REQUEST['task'] = array($_REQUEST['taskId']);
}
if (isset($_REQUEST['update_percentage']) && isset($_REQUEST['task_perc'])) {
	check_ticket('user-tasks');
	foreach ($_REQUEST['task_perc'] as $task => $perc) {
		if ($perc == 'w') $perc = NULL;
		$tasklib->update_task_percentage($task, $user, $perc);
	}
}
if (isset($_REQUEST['update_tasks'])) {
	if (($_REQUEST['action'] == 'move_marked_to_trash') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task']) as $task) {
			$tasklib->mark_task_as_trash($task, $user);
			$trashed_task = $tasklib->get_task($user, $task);
			if ($trashed_task['user'] == $user) {
				$msg_from = $user;
				$msg_to = $trashed_task['creator'];
			} else {
				$msg_from = $user;
				$msg_to = $trashed_task['user'];
			}
			$msg_title = tra('Task') . ' "' . $trashed_task['title'] . '" ' . tra('was moved to the trash');
			$msg_body = '__' . tra('Task') . ':__';
			$msg_body.= '^[tiki-user_tasks.php?taskId=' . $trashed_task['taskId'] . "|" . $trashed_task['title'] . "]^\n\n";
			$msg_body.= tra('was moved to the trash by') . ': ' . $user;
			if ($userlib->user_has_permission($msg_from, 'tiki_p_messages') and $userlib->user_has_permission($msg_to, 'tiki_p_messages')) {
				$messulib->post_message($msg_to, $msg_from, $msg_to, '', $msg_title, $msg_body, $trashed_task['priority']);
			}
		}
	}
	if (($_REQUEST['action'] == 'open_marked') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task']) as $task) {
			$tasklib->open_task($task, $user);
			$trashed_task = $tasklib->get_task($user, $task);
			if ($trashed_task['user'] == $user) {
				$msg_from = $user;
				$msg_to = $trashed_task['creator'];
			} else {
				$msg_from = $user;
				$msg_to = $trashed_task['user'];
			}
			$msg_title = tra('Task') . ' "' . $trashed_task['title'] . '" ' . tra('open / in process');
			$msg_body = '__' . tra('Task') . ':__';
			$msg_body.= '^[tiki-user_tasks.php?taskId=' . $trashed_task['taskId'] . "|" . $trashed_task['title'] . "]^\n\n";
			$msg_body.= tra('open / in process') . ': ' . $user;
			if ($userlib->user_has_permission($msg_from, 'tiki_p_messages') and $userlib->user_has_permission($msg_to, 'tiki_p_messages')) {
				$messulib->post_message($msg_to, $msg_from, $msg_to, '', $msg_title, $msg_body, $trashed_task['priority']);
			}
		}
	}
	if (($_REQUEST['action'] == 'complete_marked') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task']) as $task) {
			$tasklib->mark_complete_task($task, $user);
			$trashed_task = $tasklib->get_task($user, $task);
			if ($trashed_task['user'] == $user) {
				$msg_from = $user;
				$msg_to = $trashed_task['creator'];
			} else {
				$msg_from = $user;
				$msg_to = $trashed_task['user'];
			}
			$msg_title = tra('Task') . ' "' . $trashed_task['title'] . '" ' . tra('completed (100%)');
			$msg_body = '__' . tra('Task') . ':__';
			$msg_body.= '^[tiki-user_tasks.php?taskId=' . $trashed_task['taskId'] . "|" . $trashed_task['title'] . "]^\n\n";
			$msg_body.= tra('completed (100%)') . ': ' . $user;
			if ($userlib->user_has_permission($msg_from, 'tiki_p_messages') and $userlib->user_has_permission($msg_to, 'tiki_p_messages')) {
				$messulib->post_message($msg_to, $msg_from, $msg_to, '', $msg_title, $msg_body, $trashed_task['priority']);
			}
		}
	}
	if (($_REQUEST['action'] == 'remove_marked_from_trash') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task']) as $task) {
			$tasklib->unmark_task_as_trash($task, $user);
		}
	}
	if (($_REQUEST['action'] == 'waiting_marked') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task']) as $task) {
			$tasklib->waiting_task($task, $user);
		}
	}
}
if (isset($_REQUEST['reload'])) {
	if (isset($_REQUEST['show_trash'])) {
		$_SESSION['show_trash'] = true;
	} else {
		$_SESSION['show_trash'] = false;
	}
	if (isset($_REQUEST['show_completed'])) {
		$_SESSION['show_completed'] = true;
	} else {
		$_SESSION['show_completed'] = false;
	}
	if (isset($_REQUEST['show_private'])) {
		$_SESSION['show_private'] = true;
	} else {
		$_SESSION['show_private'] = false;
	}
	if (isset($_REQUEST['show_received'])) {
		$_SESSION['show_received'] = true;
	} else {
		$_SESSION['show_received'] = false;
	}
	if (isset($_REQUEST['show_submitted'])) {
		$_SESSION['show_submitted'] = true;
	} else {
		$_SESSION['show_submitted'] = false;
	}
	if (isset($_REQUEST['show_shared'])) {
		$_SESSION['show_shared'] = true;
	} else {
		$_SESSION['show_shared'] = false;
	}
	if (isset($_REQUEST['show_admin'])) {
		$_SESSION['show_admin'] = true;
	} else {
		$_SESSION['show_admin'] = false;
	}
	if (isset($_REQUEST['tasks_maxRecords'])) {
		check_ticket('user-prefs');
		$tikilib->set_user_preference($user, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);
	}
}
if ($task_admin and isset($_REQUEST["admin_mode"]) and $task_admin) {
	$admin_mode = true;
	$_SESSION['admin_mode'] = true;
}
if ($task_admin and isset($_REQUEST["admin_mode_off"])) {
	$admin_mode = false;
	$_SESSION['admin_mode'] = false;
}
if ($_SESSION['admin_mode'] and $task_admin) {
	$admin_mode = true;
	$smarty->assign('admin_mode', $admin_mode);
} else {
	$admin_mode = false;
}
if ($_SESSION['show_trash']) {
	$smarty->assign('show_trash', true);
	$show_trash = true;
} else {
	$show_trash = false;
}
if ($_SESSION['show_completed']) {
	$smarty->assign('show_completed', true);
	$show_completed = true;
} else {
	$show_completed = false;
}
if ($_SESSION['show_private']) {
	$smarty->assign('show_private', true);
	$show_private = true;
} else {
	$show_private = false;
}
if ($_SESSION['show_received']) {
	$smarty->assign('show_received', true);
	$show_received = true;
} else {
	$show_received = false;
}
if ($_SESSION['show_submitted']) {
	$smarty->assign('show_submitted', true);
	$show_submitted = true;
} else {
	$show_submitted = false;
}
if ($_SESSION['show_shared']) {
	$smarty->assign('show_shared', true);
	$show_shared = true;
} else {
	$show_shared = false;
}
if ($_SESSION['show_admin']) {
	$smarty->assign('show_admin', true);
	if ($tiki_p_tasks_admin == 'y') $show_admin = true;
	else $show_admin = false;
} else {
	$show_admin = false;
}
if (isset($_REQUEST['emty_trash'])) {
	$tasklib->emty_trash($user);
}
if (isset($_REQUEST["show_form"]) and $_REQUEST["show_form"] == 'y') {
	$show_form = true;
	$smarty->assign('show_form', $show_form);
} else {
	$show_form = false;
}
$tasks_maxRecords = $tikilib->get_user_preference($user, 'tasks_maxRecords', $maxRecords);
$maxRecords = $tasks_maxRecords;
$user_for_group_list = $user;
$show_history = null;
if (isset($_REQUEST['show_history'])) $show_history = $_REQUEST['show_history'];
if (($_REQUEST['taskId']) && !isset($_REQUEST['preview'])) {
	$info = $tasklib->get_task($user, $_REQUEST['taskId'], $show_history, $task_admin);
	if (!(isset($info['user']))) {
		$smarty->assign('msg', tra("Sorry, this task does not exist or you don't have permission to view this task"));
		$smarty->display("error.tpl");
		die;
	}
	if ($show_admin) $user_for_group_list = $info['creator'];
	$show_form = true;
	$smarty->assign('show_form', $show_form);
	$taskId = $info['taskId'];
} else {
	$info = $tasklib->get_default_new_task($user);
}
if (isset($_REQUEST['tiki_view_mode']) and $_REQUEST['tiki_view_mode'] == 'view') {
	$tiki_view_mode = 'view';
	$smarty->assign('tiki_view_mode', $tiki_view_mode);
	$show_view = true;
	$smarty->assign('show_view', $show_view);
	$show_form = false;
	$smarty->assign('show_form', $show_form);
	$info['parsed'] = $tikilib->parse_data($info['description']);
}
if (isset($_REQUEST['tiki_view_mode']) and $_REQUEST['tiki_view_mode'] == 'edit') {
	$show_form = true;
	$smarty->assign('show_form', $show_form);
	$wikilib = TikiLib::lib('wiki');
	$plugins = $wikilib->list_plugins(true, 'description');
	$smarty->assign_by_ref('plugins', $plugins);
}
if (isset($_REQUEST['preview'])) {
	$tiki_view_mode = 'preview';
	$smarty->assign('tiki_view_mode', $tiki_view_mode);
	$show_form = true;
	$smarty->assign('show_form', $show_form);
	$show_view = true;
	$smarty->assign('show_view', $show_view);
	$info = $tasklib->get_default_new_task($user);
}
if ((isset($_REQUEST['save'])) || (isset($_REQUEST['preview']))) {
	$auto_accepted_status = true;
	$save = array();
	$save_head = array();
	$msg_body = '';
	$msg_title = '';
	if ($info['taskId'] == 0) {
		$msg_changes_head = '__' . tra("Task entries:") . "__\n";
	} else {
		$msg_changes_head = '__' . tra("Changes:") . "__\n";
	}
	$msg_changes = '';
	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['start_Meridian'])) {
		$_REQUEST['start_Hour'] = date('H', strtotime($_REQUEST['start_Hour'] . ':00 ' . $_REQUEST['start_Meridian']));
	}
	if (!empty($_REQUEST['end_Meridian'])) {
		$_REQUEST['end_Hour'] = date('H', strtotime($_REQUEST['end_Hour'] . ':00 ' . $_REQUEST['end_Meridian']));
	}
	if (isset($_REQUEST['use_start_date']) and isset($_REQUEST['start_Hour']) and isset($_REQUEST['start_Minute']) and isset($_REQUEST['start_Month']) and isset($_REQUEST['start_Day']) and isset($_REQUEST['start_Year'])) {
		$start_date = $tikilib->make_time($_REQUEST['start_Hour'], $_REQUEST['start_Minute'], 0, $_REQUEST['start_Month'], $_REQUEST['start_Day'], $_REQUEST['start_Year']);
	} else $start_date = null;
	if (isset($_REQUEST['use_end_date']) and isset($_REQUEST['end_Hour']) and isset($_REQUEST['end_Minute']) and isset($_REQUEST['end_Month']) and isset($_REQUEST['end_Day']) and isset($_REQUEST['end_Year'])) {
		$end_date = $tikilib->make_time($_REQUEST['end_Hour'], $_REQUEST['end_Minute'], 0, $_REQUEST['end_Month'], $_REQUEST['end_Day'], $_REQUEST['end_Year']);
	} else $end_date = null;
	if (isset($_REQUEST['task_user'])) {
		$task_user = $_REQUEST['task_user'];
		if ($info['creator'] == $user and $info['user'] != $_REQUEST['task_user']) {
			$save_head['user'] = $_REQUEST['task_user'];
			$msg_changes.= tra('Task user') . ': ' . $info['user'] . ' --> ' . $save_head['user'] . "\n";
		}
	} else {
		$task_user = $user;
	}
	if (isset($_REQUEST['public_for_group'])) {
		$public_for_group = $_REQUEST['public_for_group'];
		if ($info['creator'] == $user and $info['public_for_group'] != $_REQUEST['public_for_group']) {
			$save_head['public_for_group'] = $_REQUEST['public_for_group'];
			$msg_changes.= tra('Public for group') . ': ' . $info['public_for_group'] . ' --> ' . $save_head['public_for_group'] . "\n";
		}
	} else {
		$public_for_group = null;
		if ($info['creator'] == $user and $info['public_for_group'] != null) {
			$save_head['public_for_group'] = null;
			$msg_changes.= tra('Public for group') . ': ' . $info['public_for_group'] . ' --> ' . $save_head['public_for_group'] . "\n";
		}
	}
	if (isset($_REQUEST['rights_by_creator'])) {
		$rights_by_creator = $_REQUEST['rights_by_creator'] = 'y';
		if ($info['creator'] == $user and $info['rights_by_creator'] != $_REQUEST['rights_by_creator']) {
			$save_head['rights_by_creator'] = $_REQUEST['rights_by_creator'];
			$msg_changes.= tra('Rights by creator') . ': ' . $info['rights_by_creator'] . ' --> ' . $save_head['rights_by_creator'] . "\n";
		}
	} else {
		$rights_by_creator = null;
		if ($info['creator'] == $user and $info['rights_by_creator'] != null) {
			$save_head['rights_by_creator'] = null;
			$msg_changes.= tra('Rights by creator') . ': ' . $info['rights_by_creator'] . ' --> ' . $save_head['rights_by_creator'] . "\n";
		}
	}
	if (isset($_REQUEST['public_for_group'])) {
		$public_for_group = $_REQUEST['public_for_group'];
	} else {
		$public_for_group = null;
	}
	if (isset($_REQUEST['title']) and $info['title'] != $_REQUEST['title']) {
		$save['title'] = $_REQUEST['title'];
		$msg_changes.= tra('Title') . ': ' . $info['title'] . ' --> ' . $save['title'] . "\n";
	}
	if (isset($_REQUEST['description']) and $info['description'] != $_REQUEST['description']) {
		$save['description'] = $_REQUEST['description'];
		$msg_changes.= tra('Description') . ': ' . $info['description'] . ' --> ' . $save['description'] . "\n";
	}
	if (isset($_REQUEST['use_start_date']) and $info['start'] != $start_date) {
		$save['start'] = $start_date;
		$msg_changes.= tra('Start') . ": ";
		if ($info['start'] != null) {
			$msg_changes.= $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['long_time_format'], $info['start']) . ' --> ';
		}
		$msg_changes.= $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['long_time_format'], $save['start']) . "\n";
	}
	if (isset($_REQUEST['use_end_date']) and $info['end'] != $end_date) {
		$save['end'] = $end_date;
		$msg_changes.= tra('END') . ": ";
		if ($info['end'] != null) {
			$msg_changes.= $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['long_time_format'], $info['end']) . ' --> ';
		}
		$msg_changes.= $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['long_time_format'], $save['end']) . "\n";
	}
	if (isset($_REQUEST['priority']) and $info['priority'] != $_REQUEST['priority']) {
		$save['priority'] = $_REQUEST['priority'];
		$msg_changes.= tra('Priority') . ': ' . $info['priority'] . ' --> ' . $save['priority'] . "\n";
	}
	if (isset($_REQUEST['status'])) {
		if ($_REQUEST['status'] == 'w') $save['status'] = null;
		else $save['status'] = $_REQUEST['status'];
		if ($info['status'] != $save['status']) {
			$msg_changes.= tra('Status') . ': ' . $info['status'] . ' --> ' . $save['status'] . "\n";
			if ($save['status'] == 'c') {
				$_REQUEST['percentage'] = 100;
				$save['completed'] = $tikilib->now;
			}
		} else {
			unset($save['status']);
		}
	}
	if (isset($_REQUEST['percentage'])) {
		if ($_REQUEST['percentage'] == 'w') $save['percentage'] = null;
		else $save['percentage'] = $_REQUEST['percentage'];
		if ($info['percentage'] != $save['percentage']) {
			$msg_changes.= tra('Percentage') . ': ' . $info['percentage'] . ' --> ' . $save['percentage'] . "\n";
		} else {
			unset($save['percentage']);
		}
	}
	if ($info['taskId'] > 0 and $info['creator'] != $info['user']) {
		if (isset($_REQUEST['task_accept'])) {
			$auto_accepted_status = false;
			if ($user == $info['creator'] and $info['percentage'] != 'y') {
				$save['accepted_creator'] = 'y';
				$msg_changes.= tra('Task accepted by creator') . "\n";
			}
			if ($user == $info['user'] and $info['percentage'] != 'y') {
				$save['accepted_user'] = 'y';
				$msg_changes.= tra('Task accepted by task user') . "\n";
			}
		}
		if (isset($_REQUEST['task_not_accept'])) {
			$auto_accepted_status = false;
			if ($user == $info['creator'] and $info['percentage'] != 'n') {
				$save['accepted_creator'] = 'n';
				$msg_changes.= tra('Task NOT accepted by creator') . "\n";
			}
			if ($user == $info['user'] and $info['percentage'] != 'n') {
				$save['accepted_user'] = 'n';
				$msg_changes.= tra('Task NOT accepted by task user') . "\n";
			}
		}
	}
	if (isset($_REQUEST['remove_from_trash'])) {
		$tasklib->unmark_task_as_trash($info['taskId'], $user, $admin_mode);
	}
	if (isset($_REQUEST['move_into_trash'])) {
		$tasklib->mark_task_as_trash($info['taskId'], $user, $admin_mode);
	}
	if (isset($_REQUEST['task_send_changes_message'])) {
		$send_message = true;
	} else {
		$send_message = false;
	}
	if (isset($_REQUEST['preview'])) {
		$info = $save;
		$info['taskId'] = $_REQUEST['taskId'];
		$info['task_version'] = $_REQUEST['task_version'];
		$info['last_version'] = $_REQUEST['last_version'];
		$info['user'] = $_REQUEST['task_user'];
		$info['creator'] = $_REQUEST['creator'];;
		$info['public_for_group'] = $_REQUEST['public_for_group'];
		$info['rights_by_creator'] = (isset($_REQUEST['rights_by_creator'])) ? $_REQUEST['rights_by_creator'] : NULL;
		$info['created'] = (isset($_REQUEST['created'])) ? $_REQUEST['created'] : $tikilib->now;
		$info['percentage_null'] = ($_REQUEST['percentage'] == 'w');
		if ((isset($_REQUEST['status'])) && ($_REQUEST['status'] != 'w')) {
			$info['status'] = $_REQUEST['status'];
		} else {
			$info['status'] = null;
		}
		$info['info'] = (isset($_REQUEST['task_info_message'])) ? $_REQUEST['task_info_message'] : '';
		$info['parsed'] = (isset($info['description'])) ? $tikilib->parse_data($info['description']) : '';
	}
}
if (isset($_REQUEST['save'])) {
	if (isset($_REQUEST['task_info_message']) and strlen($_REQUEST['task_info_message']) > 1) {
		$task_info_message = "\n__" . tra("Info message") . ":__\n";
		$task_info_message.= '^' . $_REQUEST['task_info_message'] . "^\n\n";
	} else {
		$task_info_message = '';
	}
	if (isset($save['title']) && strlen($save['title']) < 3) {
		$smarty->assign('msg', tra("The task title must have at least 3 characters"));
		$smarty->display("error.tpl");
		die;
	}
	if ($info['taskId'] == 0) {
		//new task
		if ($_REQUEST['task_user'] != $user) {
			$save['accepted_creator'] = 'y';
			$msg_from = $user;
			$msg_to = $_REQUEST['task_user'];
			$msg_title = tra("NEW Task") . ': "' . $save['title'] . '"';
			$send_message = true;
		} else {
			$send_message = false;
		}
		if (($_REQUEST['task_user'] == $user) or ($userlib->user_has_permission($_REQUEST['task_user'], 'tiki_p_tasks_receive') and $userlib->user_has_permission($user, 'tiki_p_tasks_send'))) {
			$taskId = $tasklib->new_task($_REQUEST['task_user'], $user, $public_for_group, $rights_by_creator, $tikilib->now, $save);
		} else {
			unset($_REQUEST['taskId']);
			$smarty->assign('msg', tra("Either you don't have permission to send tasks to other users, or the user doesn't have permission to receive tasks!"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		if ($auto_accepted_status) {
			if ($info['user'] == $user) {
				$msg_to = $info['creator'];
				$save['accepted_user'] = 'y';
				$save['accepted_creator'] = null;
			} else if ($info['creator'] == $user) {
				$msg_to = $info['user'];
				$save['accepted_user'] = null;
				$save['accepted_creator'] = 'y';
			} else {
				$msg_to = $info['user'];
				$save['accepted_user'] = null;
				$save['accepted_creator'] = null;
			}
		}
		$msg_from = $user;
		$tasklib->update_task($info['taskId'], $user, $save, $save_head, $admin_mode);
		$taskId = $info['taskId'];
		$msg_title = tra("Changes on Task") . ': "' . $info['title'] . '" by ' . $user;
	}
	$info = $tasklib->get_task($user, $taskId, null, $admin_mode);
	//send email to task user
	if ((isset($_REQUEST['send_email_newtask'])) && $send_message && ($_REQUEST['task_user'] != $user)) {
		include_once ('lib/newsletters/nllib.php');
		$email = $userlib->get_user_email($msg_to);
		$mail = new TikiMail($msg_to);
		$mail->setSubject($msg_title);
		$mail_data = tra("You received a new task") . "\n\n" . $info['title'] . "\n" . tra("from") . " :$user\n";
		$mail_data.= tra("The priority is") . ": ";
		switch ($info['priority']) {
			case 1:
				$mail_data.= tra("very low");
    			break;

			case 2:
				$mail_data.= tra("low");
    			break;

			case 3:
				$mail_data.= tra("normal");
    			break;

			case 4:
				$mail_data.= tra("high");
    			break;

			case 5:
				$mail_data.= tra("very high");
    			break;
		}
		$mail_data.= ".\n\n";
		if ($info['start'] !== NULL) {
			$mail_data.= tra("You must start your work at least on") . ": " . $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['short_time_format'], $info['end']) . "\n";
		}
		if ($info['end'] !== NULL) {
			$mail_data.= tra("You must finish your work on") . ": " . $tikilib->date_format($prefs['short_date_format'] . ' ' . $prefs['short_time_format'], $info['end']) . "\n";
		}
		$mail_data.= "\n" . tra("Log in and click the link below") . "\n";
		$mail_data.= "http://" . $_REQUEST['HTTP_HOST'] . $_REQUEST['REQUEST_URI'] . "?tiki_view_mode=view&taskId=" . $taskId . "\n\n";
		$mail_data.= tra("Please read the task and work on it!");
		$mail->setText($mail_data);
		$mail->send(array($email));
	}
	if (!isset($info['user'])) {
		unset($_REQUEST['taskId']);
		$smarty->assign('msg', tra("Sorry, there was an error while trying to write data into the database"));
		$smarty->display("error.tpl");
		die;
	}
	if ($send_message and $userlib->user_has_permission($msg_from, 'tiki_p_messages') and $userlib->user_has_permission($msg_to, 'tiki_p_messages')) {
		$msg_body = "__" . tra('Task') . ":__";
		$msg_body.= '^[tiki-user_tasks.php?taskId=' . $info['taskId'] . "|" . $info['title'] . "]^\n";
		$msg_body.= $task_info_message . $msg_changes_head . '^' . $msg_changes . '^';
		$messulib->post_message(
			$msg_to, //user
			$msg_from, //from
			$msg_to, //to
			'', //cc
			$msg_title, //title
			$msg_body, //body
			$info['priority'] //priority
		);

	}
	if ($show_admin) $user_for_group_list = $info['creator'];
	$show_form = false;
	$smarty->assign('show_form', $show_form);
	$show_view = true;
	$smarty->assign('show_view', $show_view);
}
$smarty->assign('taskId', $_REQUEST['taskId']);
$smarty->assign('info', $info);
$smarty->assign('created_Month', $tikilib->date_format('%m', $info['created']));
$smarty->assign('created_Day', $tikilib->date_format('%d', $info['created']));
$smarty->assign('created_Year', $tikilib->date_format('%Y', $info['created']));
$smarty->assign('created_Hour', $tikilib->date_format('%H', $info['created']));
$smarty->assign('created_Minute', $tikilib->date_format('%M', $info['created']));
if ((!isset($info['start'])) || ($info['start'] == null)) {
	$info['start'] = $tikilib->now;
	$smarty->assign('start_date', $info['start']);
} else {
	$smarty->assign('start_date', $info['start']);
}
if ((!isset($info['end'])) || ($info['end'] == null)) {
	$smarty->assign('end_date', ($info['start'] + 86400));
} else {
	$smarty->assign('end_date', $info['end']);
}
if (!isset($_REQUEST['sort_mode'])) {
	$sort_mode = 'priority_desc';
} else {
	$sort_mode = $_REQUEST['sort_mode'];
}
if (!isset($_REQUEST['offset'])) {
	$offset = 0;
} else {
	$offset = $_REQUEST['offset'];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST['find'])) {
	$find = $_REQUEST['find'];
} else {
	$find = null;
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (!$show_form) {
	$tasklist = $tasklib->list_tasks($user, $offset, $maxRecords, $find, $sort_mode, $show_private, $show_submitted, $show_received, $show_shared, false, null, $show_trash, $show_completed, $show_admin);
	$smarty->assign('cant', $tasklist['cant']);
	$smarty->assign_by_ref('tasklist', $tasklist["data"]);
}
$receive_groups = $tikilib->get_groups_to_user_with_permissions($user_for_group_list, 'tiki_p_tasks_receive');
$smarty->assign('receive_groups', $receive_groups);
$receive_users = $tasklib->get_user_with_permissions('tiki_p_tasks_receive');
if (count($receive_users) < 100) $smarty->assign('receive_users', $receive_users);
include_once ('tiki-mytiki_shared.php');
$percs = array();
for ($i = 0; $i <= 100; $i+= 10) {
	$percs[] = $i;
}
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
$userprefslib = TikiLib::lib('userprefs');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

$smarty->assign_by_ref('percs', $percs);
$smarty->assign('mid', 'tiki-user_tasks.tpl');
$smarty->display("tiki.tpl");
