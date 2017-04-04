<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Xmpp_Controller  {
	function setUp() {
		Services_Exception_Disabled::check('xmpp_feature');
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
