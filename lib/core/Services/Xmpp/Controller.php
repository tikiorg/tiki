<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once 'lib/auth/tokens.php';

class Services_Xmpp_Controller  {
	function setUp() {
		Services_Exception_Disabled::check('xmpp_feature');
		Services_Exception_Disabled::check('auth_token_access');
	}

	function action_check_token($input) {
		$xmpplib = TikiLib::lib('xmpp');
		$query = $input->stored;

		$user = $input->offsetGet('user');
		$token = $input->offsetGet('token');

		if( empty($user) || empty($token) ) {
			return array('valid' => false);
		}

		$valid = (bool) $xmpplib->check_token($user, $token);
		return array('valid' => $valid);
	}

	function action_get_user_info($input) {
		$xmpplib = TikiLib::lib('xmpp');
		$userlib = TikiLib::lib('user');

		$authHeader = '';
		$givenKey = null;
		$user = $input->offsetGet('user');

		// check if authorization is sent
		if(! empty($_SERVER['Authorization'])) {
			$authHeader = $_SERVER['Authorization'];
		} else if (! empty($_SERVER['HTTP_AUTHORIZATION']) ) {
			$authHeader = $_SERVER['HTTP_AUTHORIZATION'];
		} else {
			header("HTTP/1.0 403 Forbidden", true, 403);
			die(tr("Empty authorization"));
		}

		// check if authorization looks like we expect
		$match = null;
		if (preg_match('/^Bearer  *([a-zA-Z0-9]{32})$/', $authHeader, $match)) {
			$givenKey = $match[1];
		} else {
			header("HTTP/1.0 403 Forbidden", true, 403);
			die(tr("Wrong authorization format"));
		}

		if(!$userlib->user_exists($user)) {
			header("HTTP/1.0 404 Not Found", true, 404);
			die(tr('Invalid user'));
		}

		// TODO: Check with jonnybradley if this is a good idea
		$tokenlib = AuthTokens::build();
		$tokens = $tokenlib->getTokens(array('entry' => 'openfireaccesskey'));
		$key = !empty($tokens) ? md5("{$user}{$tokens[0]['token']}") : null;

		$validity = $key !== null
			&& $givenKey !== null
			&& strtoupper($key) === strtoupper($givenKey);

		// final check, if givenKey is really valid
		if($validity) {
			$details = $userlib->get_user_details($user);
			return isset($details['info']) ? $details['info'] : null;
		}

		header("HTTP/1.0 403 Forbidden", true, 403);
		die(tr('Invalid token'));
	}

	function action_prebind($input) {
		global $user;
		$xmpplib = TikiLib::lib('xmpp');

		if (! $user) {
			throw new Services_Exception(tr('Must be authenticated'), 403);
		}

		try{
			$result = $xmpplib->prebind($user);
		} catch (Exception $e) {
			$code = $e->getCode() ?: 500;
			$msg = $e->getMessage();
			throw new Services_Exception($msg, $code);
		}

		return $result;
	}
}
