<?php
require_once('../../../tiki-setup.php');

include_once('lib/workspaces/wslib.php');

$ws = new wslib();

	$id = $ws->get_ws_id('WS0','6');
	var_dump($id);
	if (!$id)
		var_dump($ws->add_ws('WS0',6));
	else
		var_dump($ws->add_ws('WS01',6));