<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-user_tasks.php,v 1.17 2005-01-22 22:54:57 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/tasks/tasklib.php');
include_once ('lib/messu/messulib.php');

if ($feature_tasks != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tasks");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_tasks != 'y') {
	$smarty->assign('msg', tra("Permission denied to use this feature"));

	$smarty->display("error.tpl");
	die;
}


if ($tiki_p_tasks_admin == 'y') $task_admin = true;
else $task_admin = false;
$smarty->assign('task_admin', $task_admin);

$comp_array = array();
$comp_array_p = array();

for ($i = 0; $i < 101; $i += 10) {
	$comp_array[] = $i;

	$comp_array_p[] = $i . '%';
}

$smarty->assign('comp_array', $comp_array);
$smarty->assign('comp_array_p', $comp_array_p);

if (!isset($_REQUEST['taskId'])){
	$_REQUEST['taskId'] = 0;
}



//default values for show options
if(!isset($_SESSION['show_trash'])) $_SESSION['show_trash'] = false;
if(!isset($_SESSION['show_completed'])) $_SESSION['show_completed'] = true;
if(!isset($_SESSION['show_private'])) $_SESSION['show_private'] = true;
if(!isset($_SESSION['show_received'])) $_SESSION['show_received'] = true;
if(!isset($_SESSION['show_submitted'])) $_SESSION['show_submitted'] = true;
if(!isset($_SESSION['show_shared'])) $_SESSION['show_shared'] = true;
if(!isset($_SESSION['show_admin'])) $_SESSION['show_admin'] = false;

if (isset($_REQUEST['update']) && isset($_REQUEST['action'])) {

	if (($_REQUEST['action'] == 'update_percentage') && isset($_REQUEST['task_perc'])) {
		check_ticket('user-tasks');
		foreach ($_REQUEST['task_perc'] as $task => $perc) {
			$tasklib->update_task_percentage($user, $task, $perc);
		}
	}

	if (($_REQUEST['action'] == 'move_marked_to_trash') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task'])as $task) {
			$tasklib->remove_task($user, $task);
		}
	}
	
	if (($_REQUEST['action'] == 'complete_marked') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task'])as $task) {
			$tasklib->complete_task($user, $task);
		}
	}
	if (($_REQUEST['action'] == 'remove_marked_from_trash') && isset($_REQUEST['task'])) {
		check_ticket('user-tasks');
		foreach (array_keys($_REQUEST['task'])as $task) {
			$tasklib->remove_task_from_trash($user, $task);
		}
	}
	if (isset($_REQUEST['show_trash'])) {
		$_SESSION['show_trash'] = true;
 	}else {
		$_SESSION['show_trash'] = false;
	}
	if (isset($_REQUEST['show_completed'])) {
		$_SESSION['show_completed'] = true;
 	}else {
		$_SESSION['show_completed'] = false;
	}
	if (isset($_REQUEST['show_private'])) {
		$_SESSION['show_private'] = true;
 	}else {
		$_SESSION['show_private'] = false;
	}
	if (isset($_REQUEST['show_received'])) {
		$_SESSION['show_received'] = true;
 	}else {
		$_SESSION['show_received'] = false;
	}
	if (isset($_REQUEST['show_submitted'])) {
		$_SESSION['show_submitted'] = true;
 	}else {
		$_SESSION['show_submitted'] = false;
	}
	if (isset($_REQUEST['show_shared'])) {
		$_SESSION['show_shared'] = true;
 	}else {
		$_SESSION['show_shared'] = false;
	}
	if (isset($_REQUEST['show_admin'])) {
		$_SESSION['show_admin'] = true;
 	}else {
		$_SESSION['show_admin'] = false;
	}
	
	if (isset($_REQUEST['tasks_maxRecords'])) {
		check_ticket('user-prefs');
		$tikilib->set_user_preference($user, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);
	}
}
	



if ($_SESSION['show_trash']){
	$smarty->assign('show_trash', true);
	$show_trash = true;
} else {
	$show_trash = false;
}

if ($_SESSION['show_completed']){
	$smarty->assign('show_completed', true);
	$show_completed = true;
} else {
	$show_completed = false;
}
if ($_SESSION['show_private']){
	$smarty->assign('show_private', true);
	$show_private = true;
} else {
	$show_private = false;
}
if ($_SESSION['show_received']){
	$smarty->assign('show_received', true);
	$show_received = true;
} else {
	$show_received = false;
}
if ($_SESSION['show_submitted']){
	$smarty->assign('show_submitted', true);
	$show_submitted = true;
} else {
	$show_submitted = false;
}
if ($_SESSION['show_shared']){
	$smarty->assign('show_shared', true);
	$show_shared = true;
} else {
	$show_shared = false;
}
if ($_SESSION['show_admin']){
	$smarty->assign('show_admin', true);
	if ($tiki_p_tasks_admin == 'y') $show_admin = true;
	else $show_admin = false;
} else {
	$show_admin = false;
}

if (isset($_REQUEST['emty_trash'])) {
			$tasklib->emty_trash($user);
}

if (isset($_REQUEST['accept'])){
	$tasklib->accept_task($user, $_REQUEST['taskId'], 'y');
} 

if (isset($_REQUEST['reject'])){
	$tasklib->accept_task($user, $_REQUEST['taskId'], 'n');
} 

if (isset($_REQUEST['move_task_into_trash'])){
	$tasklib->move_task_into_trash($user, $_REQUEST['taskId']);
} 

if (isset($_REQUEST['remove_task_from_trash'])){
	$tasklib->remove_task_from_trash($user, $_REQUEST['taskId']);
} 

if(isset($_REQUEST["show_form"]) and $_REQUEST["show_form"] == 'y'){
	$smarty->assign('show_form', true);
}

$tasks_maxRecords = $tikilib->get_user_preference($user, 'tasks_maxRecords', $maxRecords);
$maxRecords = $tasks_maxRecords;

$smarty->assign('tasks_maxRecords', $tasks_maxRecords);

$history = null;

if ($_REQUEST['taskId']) {
	$info = $tasklib->get_task($user, $_REQUEST['taskId'], !$show_admin);
	if(!(isset($info['user']))){
		$smarty->assign('msg', tra("Sorry this task does not exist or you have no rights to view this task"));
		$smarty->display("error.tpl");
		die;
	}
	$smarty->assign('show_form', true);
	
	$history = $tasklib->list_taskId_form_history($info['belongs_to']);
	if($info['newest_version'] != 0) $smarty->assign('history', $history);

} else {
	$info = $tasklib->get_default_new_task($user);
}

if($show_admin){
	$right = 'creator';
}else{
	$right = $tasklib->check_right_on_task($user, $info);
}

if(isset($right)){
	$editable['user'] = true;
	$editable['taskId'] = true;
	$editable['belongs_to'] = true;
	$editable['task_version'] = true;
	$editable['title'] = true;
	$editable['description'] = true;
	$editable['date'] = true;
	$editable['start'] = true;
	$editable['end'] = true;
	$editable['status']  = true;
	$editable['priority']  = true;
	$editable['completed']  = true;
	$editable['percentage']  = true;
	$editable['lasteditor'] = true;
	$editable['changes'] = true;
	$editable['deleted'] = true;
	$editable['creator'] = true;
	$editable['accepted_creator'] = true;
	$editable['accepted_user'] = true;
	$editable['public_for_group'] = true;
	$editable['rights_by_creator'] = true;
	$editable['info'] = true;
	
	if($right == 'new'){
		$editable['taskId'] = false;
		$editable['belongs_to'] = false;
		$editable['task_version'] = false;
		$editable['date'] = false;
		$editable['lasteditor'] = false;
		$editable['changes'] = false;
		$editable['deleted'] = false;
		$editable['creator'] = false;
		$editable['accepted_creator'] = false;
		$editable['accepted_user'] = false;
	} else
	if($right == 'private'){
		$editable['user'] = false;
		$editable['taskId'] = false;
		$editable['belongs_to'] = false;
		$editable['task_version'] = false;
		$editable['date'] = false;
		$editable['lasteditor'] = false;
		$editable['changes'] = false;
		$editable['creator'] = false;
		$editable['accepted_creator'] = false;
		$editable['accepted_user'] = false;
		$editable['rights_by_creator'] = false;
		$editable['info'] = false;
		$editable['rights_by_creator'] = false;
	} else
	if($right == 'creator'){
		$editable['user'] = false;
		$editable['taskId'] = false;
		$editable['belongs_to'] = false;
		$editable['task_version'] = false;
		$editable['date'] = false;
		$editable['lasteditor'] = false;
		$editable['changes'] = false;
		$editable['creator'] = false;
		if($info['user'] != $info['creator'] and $user != $info['creator']) $editable['rights_by_creator'] = false;
	} else
	if($right == 'user'){
		$editable['user'] = false;
		$editable['taskId'] = false;
		$editable['belongs_to'] = false;
		$editable['task_version'] = false;
		$editable['date'] = false;
		$editable['lasteditor'] = false;
		$editable['changes'] = false;
		$editable['deleted'] = false;
		$editable['creator'] = false;
		$editable['rights_by_creator'] = false;
	} 
	if($info['user'] == $user) $editable['accepted_user'] = true; else $editable['accepted_user'] = false;
	if($info['creator'] == $user) $editable['accepted_creator'] = true; else $editable['accepted_creator'] = false;
	
	if ($tiki_p_tasks_send != 'y') {
		$editable['user'] = false;
	}
}


if (isset($_REQUEST['save']) and isset($right) and $right != 'view') {
	$dc = $tikilib->get_date_converter($user);
	$save = $info;
	
	
	if(	isset($_REQUEST['use_start_date']) and
		isset($_REQUEST['start_Hour']) and 
		isset($_REQUEST['start_Minute']) and 
		isset($_REQUEST['start_Month']) and 
		isset($_REQUEST['start_Day']) and 
		isset($_REQUEST['start_Year'])){
			$start_date = $dc->getServerDateFromDisplayDate(mktime(	$_REQUEST['start_Hour'], 
																	$_REQUEST['start_Minute'], 
																	0, 
																	$_REQUEST['start_Month'], 
																	$_REQUEST['start_Day'], 
																	$_REQUEST['start_Year']));
	} else $start_date = null;
	
	if(	isset($_REQUEST['use_end_date']) and
		isset($_REQUEST['end_Hour']) and 
		isset($_REQUEST['end_Minute']) and 
		isset($_REQUEST['end_Month']) and 
		isset($_REQUEST['end_Day']) and 
		isset($_REQUEST['end_Year'])){
			$end_date = $dc->getServerDateFromDisplayDate(mktime(	$_REQUEST['end_Hour'], 
																	$_REQUEST['end_Minute'], 
																	0, 
																	$_REQUEST['end_Month'], 
																	$_REQUEST['end_Day'], 
																	$_REQUEST['end_Year']));
	} else $end_date = null;

	if($editable['user'] and isset($_REQUEST['task_user'])) $save['user'] = $_REQUEST['task_user'];
	$save['belongs_to'] = $save['belongs_to'];
	if($right != 'new')	$save['task_version'] = $save['task_version'] + 1;
	if($editable['title'] and isset($_REQUEST['title'])) $save['title'] = $_REQUEST['title'];
	if($editable['description'] and isset($_REQUEST['description'])){
		$save['description'] = $_REQUEST['description'];
	}
	if($editable['start']) $save['start'] = $start_date;
	if($editable['end']) $save['end'] = $end_date;
	
	if($editable['status']){
		if(isset($_REQUEST["status"]) and $_REQUEST["status"] == 'w'){
		 	$save['status'] = null;
			$save['completed'] = null;
		}
		else if(isset($_REQUEST["status"]) and $_REQUEST["status"] == 'o'){
			$save['status'] = 'o';
			$save['completed'] = null;
		}
		else if(isset($_REQUEST["status"]) and $_REQUEST["status"] == 'c'){
			$save['status'] = 'c';
			$save['completed'] = date("U");
			$_REQUEST["percentage"] = 100;
		}
		 $save['description'] = $_REQUEST["description"];
	}
	
	if($editable['priority']){
		if(!isset($_REQUEST["priority"])) $save['priority'] = 3;
		else 
		$save['priority'] = $_REQUEST["priority"];
	}
	
	if($editable['percentage'] and isset($_REQUEST["percentage"])) $save['percentage'] = $_REQUEST["percentage"];
	
	$editable['lasteditor'] = $user;
	$editable['changes'] = date("U");
	
	if($editable['creator'] and isset($_REQUEST["creator"])) $save['creator'] = $_REQUEST["creator"];
	
	
	if($right != 'new' and $right != 'private'){
		if($editable['accepted_creator'] and $info['creator'] == $user){
			$save['accepted_creator'] = 'y';	
			$save['accepted_user'] = null;
		}
		if($editable['accepted_user'] and $info['user'] == $user){
			$save['accepted_creator'] = 'y';
			$save['accepted_user'] = null;
		}
	}else if($right == 'new') $save['accepted_creator'] = 'y';

	if($editable['public_for_group']){
		if (isset($_REQUEST['public_for_group'])) $save['public_for_group'] = $_REQUEST['public_for_group'];
		else $save['public_for_group'] = null;
	}
	
	if($editable['rights_by_creator']){
		if(isset($_REQUEST['rights_by_creator'])) $save['rights_by_creator'] = 'y';
		if(!isset($_REQUEST['rights_by_creator'])) $save['rights_by_creator'] = null;
	}
	
	if($editable['info'] and isset($_REQUEST["info"])) $save['info'] = $_REQUEST["info"];	
	
	$save['deleted'] = null;

	
	if(!isset($save['title']) or strlen($save['title']) < 3){
		$smarty->assign('msg', tra("The tile must have at lease three characters!"));
		$smarty->display("error.tpl");
		die;
	}
	if(isset($save['end']) and  isset($save['start']) and  $save['start'] >= $save['end']){
		$smarty->assign('msg', tra("The end date must be after the start date!"));
		$smarty->display("error.tpl");
		die;
	}
	
	$msg_change = "\n". tra('changes at:') . "\n";
	if($info['title'] != $save['title']) $msg_change .=  tra('title') . "\n";
	if($info['description'] != $save['description']) $msg_change .=  tra('description') . "\n";
	if($info['start'] != $save['start']) $msg_change .=  tra('start date') . "\n";
	if($info['end'] != $save['end']) $msg_change .=  tra('end date') . "\n";
	if($info['priority'] != $save['priority']) $msg_change .=  tra('priority') . "\n";
	if($info['status'] != $save['status']) $msg_change .=  tra('status') . "\n";
	if($info['completed'] != $save['completed']) $msg_change .=  tra('completed') . "\n";
	
	
	if($save['creator'] != $save['user']){
		if($userlib->user_has_permission($save['user'],'tiki_p_tasks_receive') != 1){
				$smarty->assign('msg', tra("Sorry the task user has no right to recive tasks"));
				$smarty->display("error.tpl");
				die;
		}
		if($userlib->user_has_permission($save['creator'],'tiki_p_tasks_send') != 1){
				$smarty->assign('msg', tra("Sorry the creator has no right to send tasks"));
				$smarty->display("error.tpl");
				die;
		}
	}
	
	
	$new_taskId = $tasklib->write_task_in_db(	$save['user'],
												$save['taskId'],
												$save['belongs_to'],
												$save['task_version'],
												$save['title'],
												$save['description'],
												$save['date'],
												$save['start'],
												$save['end'],
												$save['status'],
												$save['priority'],
												$save['completed'],
												$save['percentage'],
												$save['lasteditor'],
												$save['changes'],
												$save['deleted'],
												$save['creator'],
												$save['accepted_creator'],
												$save['accepted_user'],
												$save['public_for_group'],
												$save['rights_by_creator'],
												$save['info']
	);
	$info = $tasklib->get_task($user, $new_taskId);
	
	if(!isset($info['user'])){
		unset($_REQUEST['taskId']);
		$smarty->assign('msg', tra("Sorry this problems by writing into the database"));
		$smarty->display("error.tpl");
		die;
	}
	
	if($info['creator'] != $info['user']){
		if($info['user'] == $user) {
			$msg_from = $user;
			$msg_to = $info['creator'];
		}
		if($info['creator'] == $user) {
			$msg_from = $user;
			$msg_to = $info['user'];
		}
		if($info['task_version'] == 0) {
			$msg_title = 'New task for you';
			$msg_body  = 'New task for you created by ' . $msg_from . "\n\n"; 
			$msg_body .= '<a href="tiki-user_tasks.php?taskId='.$info['taskId'].'">'.$info['title']."</a>\n\n";
		} else {
			$msg_title = 'Task changed!';
			$msg_body = "Task changed by " . $info['lasteditor'] . "\n\n"; 
			$msg_body .= '<a href="tiki-user_tasks.php?taskId='.$info['taskId'].'">'.$info['title']."</a>\n\n";
			$msg_body .= $msg_change . "\n";
		}
		$msg_body .= "Info:\n" . $info['info'];
		if(	$userlib->user_has_permission($msg_from,'tiki_p_messages') and 
			$userlib->user_has_permission($msg_to,'tiki_p_messages'))
		{
			$messulib->post_message($msg_to,	//user
						$msg_from,	//from
						$msg_to,	//to
						'',		//cc
						$msg_title,	//title
						$msg_body,	//body
						$info['priority']);//priority
		}
	}
	$_REQUEST['taskId'] = $new_taskId;
	$smarty->assign('saved',  true);
	$smarty->assign('show_form', true);
	if($show_admin){
		$right = 'creator';
	} else {
		$right = $tasklib->check_right_on_task($user, $info);
	}
}

$smarty->assign('taskId', $_REQUEST['taskId']);
$smarty->assign('info', $info);
$smarty->assign('created_Month',  date('m', $info['date']));
$smarty->assign('created_Day',  date('d', $info['date']));
$smarty->assign('created_Year',  date('Y', $info['date']));
$smarty->assign('created_Hour',  date('H', $info['date']));
$smarty->assign('created_Minute',  date('M', $info['date']));


if($info['start'] == null) $smarty->assign('start_date',  date("U"));
 else $smarty->assign('start_date',  $info['start']);

if($info['end'] == null){
	if($info['start'] == null) $smarty->assign('end_date',  date("U"));
	else $smarty->assign('end_date',  $info['start']);
} else $smarty->assign('end_date',  $info['end']);


$smarty->assign('right',  $right);

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
						
$tasklist = $tasklib->list_tasks(	$user,$offset ,$maxRecords, $find, $sort_mode, 
									$show_private, $show_submitted, $show_received, $show_shared,
									/*$use_show_shared_for_group*/ false, /*$show_shared_for_group*/ null, 
									$show_trash, $show_completed, $show_admin);

$cant_pages = ceil($tasklist['cant'] / $maxRecords);
if($maxRecords == -1) $cant_pages = 1;
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));
$receive_groups = $tikilib->get_groups_to_user_with_permissions($user, 'tiki_p_tasks_receive');
$smarty->assign('receive_groups', $receive_groups);
$smarty->assign('editable', $editable);
$smarty->assign('cant', $tasklist['cant']);


$receive_users = $tasklib->get_user_with_permissions('tiki_p_tasks_receive');
if(count($receive_users) < 100) $smarty->assign('receive_users', $receive_users);

if ($tasklist["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('tasklist', $tasklist["data"]);


include_once ('tiki-mytiki_shared.php');

$percs = array();

for ($i = 0; $i <= 100; $i += 10) {
	$percs[] = $i;
}

$smarty->assign_by_ref('percs', $percs);

ask_ticket('user-tasks');

$smarty->assign('mid', 'tiki-user_tasks.tpl');
$smarty->display("tiki.tpl");

?>
