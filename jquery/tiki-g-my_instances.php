<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

  // Common setup for Galaxia environment
  require_once('tiki-setup.php');
  require_once('lib/Galaxia/GUI.php');

  // Check if feature is enabled and permissions
  if ($prefs['feature_workflow'] != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

    $smarty->display("error.tpl");
    die;
  }

  if ($tiki_p_use_workflow != 'y') {
	  $smarty->assign('errortype', 401);
    $smarty->assign('msg', tra("Permission denied"));

    $smarty->display("error.tpl");
    die;
  }

  $smarty->assign('tiki_p_abort_instance',$tiki_p_abort_instance);
  $smarty->assign('tiki_p_exception',$tiki_p_exception_instance);

  $where = '';
  $wheres = array();

  if (isset($_REQUEST['status']) && $_REQUEST['status'])
    $wheres[] = "gi.status='" . $args['status'] . "'";

  if (isset($_REQUEST['actstatus']) && $_REQUEST['actstatus'])
    $wheres[] = "gia.status='" . $args['actstatus'] . "'";

  if (isset($_REQUEST['pId']) && $_REQUEST['pId'])
    $wheres[] = "gp.pId='" . $args['pId'] . "'";
 
  $where = implode(' and ', $wheres);

  if (isset($_REQUEST['numitems']) && $_REQUEST['numitems']) {
    $numitems = $args['numitems'];
  } else {
    $numitems = -1;
  }
  if (isset($_REQUEST['startnum']) && $_REQUEST['startnum']) {
    $startnum = $args['startnum'];
  } else {
    $startnum = 1;
  }
  if (isset($_REQUEST['sort_mode']) && $_REQUEST['sort_mode']) {
    $sort_mode = $args['sort_mode'];
  } else {
    $sort_mode = 'started_asc';
  }
  $instances = $GUI->gui_list_user_instances($user, $startnum - 1, $numitems, $sort_mode, '', $where);
  if (empty($instances) || count($instances['data']) < 1) {
      return '';
  }

  $smarty->assign('instances', $instances["data"]);

?>
