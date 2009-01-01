<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

  // Common setup for Galaxia environment
  require_once('tiki-setup.php');
  require_once('lib/Galaxia/ProcessMonitor.php');

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

  // retrieve the instances for which you're the owner
  $where = "gi.owner='$user'";
  if (!empty($args['status'])) {
    $where .= " and gi.status='" . $args['status'] . "'";
  }
  if (!empty($args['actstatus'])) {
    $where .= " and gia.status='" . $args['actstatus'] . "'";
  }
  if (!empty($args['pId'])) {
    $where .= " and gp.pId='" . $args['pId'] . "'";
  }
  $processes = $processMonitor->monitor_list_instances(0, -1, 'started_asc', '', $where);

  $smarty->assign('processes', $processes["data"]);  

    if (!empty($args['layout'])) {
        $tplData['layout'] = $args['layout'];
    }

?>
