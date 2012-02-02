<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
require_once('lib/auth/tokens.php');

$access->check_permission('tiki_p_admin');

$tokenlib = AuthTokens::build($prefs);

$action = '';
$tokenId = 0;

if (isset($_REQUEST['action'])) {
	$action = $_REQUEST['action'];
}

if (isset($_REQUEST['tokenId']) && is_numeric($_REQUEST['tokenId'])) {
	$tokenId = $_REQUEST['tokenId'];
}

if ($action == 'delete'	&& $tokenId > 0) {
	$tokenlib->deleteToken($_REQUEST['tokenId']);
}

if ($action == 'add') {
	$entry = filter_input(INPUT_POST, 'entry', FILTER_SANITIZE_STRING);
	
	$groups = filter_input(INPUT_POST, 'groups', FILTER_SANITIZE_STRING);
	$groups = str_replace(' ', '', $groups);
	$groups = explode(',', $groups);
	
	$arguments = array();
	$arguments['timeout'] = filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT); 
	$arguments['hits'] = filter_input(INPUT_POST, 'maxhits', FILTER_SANITIZE_NUMBER_INT);
	
	if (!empty($entry) && !empty($groups)) {
		$tokenlib->createToken($entry, array(), $groups, $arguments);
	}
}

$tokens = $tokenlib->getTokens();

$smarty->assign('tokens', $tokens);
$smarty->assign('mid', 'tiki-admin_tokens.tpl');
$smarty->display('tiki.tpl');