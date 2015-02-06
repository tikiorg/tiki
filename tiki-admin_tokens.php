<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
require_once('lib/auth/tokens.php');

$access->check_feature('auth_token_access');
$access->check_permission('tiki_p_admin');

$tokenlib = AuthTokens::build($prefs);

$action = '';
$tokenId = 0;
$smarty->assign('tokenCreated', false);

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
	$url = filter_input(INPUT_POST, 'entry', FILTER_SANITIZE_STRING);
	$entry = parse_url($url, PHP_URL_PATH);
	
	$groups = filter_input(INPUT_POST, 'groups', FILTER_SANITIZE_STRING);
	$groups = str_replace(' ', '', $groups);
	$groups = explode(',', $groups);
	
	$parameters = array();
	$query = parse_url($url, PHP_URL_QUERY);

	if (!empty($query)) {
		$query = explode('&', $query);
	
		foreach ($query as $element) {
			list($key, $value) = explode('=', $element);
			$parameters[$key] = $value;
		}
	}

	$arguments = array();
	$arguments['timeout'] = filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT); 
	$arguments['hits'] = filter_input(INPUT_POST, 'maxhits', FILTER_SANITIZE_NUMBER_INT);
	
	if (!empty($entry) && !empty($groups)) {
		$token = $tokenlib->createToken($entry, $parameters, $groups, $arguments);
		
		if (!empty($token)) {
			$smarty->assign('tokenCreated', true);
		}
	}
}

$tokens = $tokenlib->getTokens();

foreach ($tokens as $key => $token) {
	$tokens[$key]['groups'] = join(', ', json_decode($token['groups']));
	$tokens[$key]['parameters'] = (array) json_decode($token['parameters']);
}

$smarty->assign('tokens', $tokens);
$smarty->assign('mid', 'tiki-admin_tokens.tpl');
$smarty->display('tiki.tpl');