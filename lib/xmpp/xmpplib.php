<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
require_once 'lib/auth/tokens.php';
require_once dirname(__FILE__) . '/TikiXmppPrebind.php';

class XMPPLib extends TikiLib
{

	function __construct()
	{
		global $prefs;

		$this->server_host = $prefs['xmpp_server_host'];
		$this->server_http_bind = $prefs['xmpp_server_http_bind'];

		if (! class_exists('XmppPrebind')) {
			throw new Exception(
				"class 'XmppPrebind' does not exists."
				. " Install with `composer require candy-chat/xmpp-prebind-php:dev-master`.",
				1
			);
		}
	}

	function get_user_password($user)
	{
		$query  = "SELECT value FROM `tiki_user_preferences` WHERE `user`=?";
		$query .= " AND `prefName`='xmpp_password';";

		$result = $this->query($query, [$user]);
		$ret = $result->fetchRow();

		if (count($ret) === 1) {
			return $ret['value'];
		}
	}

	function get_user_jid($user)
	{
		return sprintf('%s@%s', $user, $this->server_host);
	}

	function check_token($givenUser, $givenToken)
	{
		$tokenlib = AuthTokens::build($prefs);
		$token = $tokenlib->getToken($givenToken);

		if (! $token || $token['entry'] !== 'openfireauthtoken') {
			return false;
		}
		// TODO: figure out how to delete token after n usages
		$tokenlib->deleteToken($token['tokenId']);

		$param = json_decode($token['parameters'], A_ARRAY);
		return is_array($param)
			&& ! empty($param['user'])
			&& $param['user'] === $givenUser;
	}

	function prebind($user)
	{
		global $prefs;

		$tikilib = TikiLib::lib('tiki');
		$tokenlib = AuthTokens::build($prefs);

		if (empty($this->server_host) ||  empty($this->server_http_bind)) {
			return [];
		}

		$xmpp_username = $user;

		if (! empty($prefs['xmpp_openfire_use_token']) && $prefs['xmpp_openfire_use_token'] === 'y') {
			$token = $tokenlib->createToken(
				'openfireauthtoken',
				['user' => $user], // parameters
				[], // groups
				[
					'timeout' => 300,
					'createUser' => 'n',
				]
			);
			$xmpp_password = "$token";
		} else {
			$xmpp_password = $this->get_user_password($user);
		}

		$xmppPrebind = new TikiXmppPrebind(
			$this->server_host,
			$this->server_http_bind,
			'tikiwiki',
			false,
			false
		);

		$xmppPrebind->connect($xmpp_username, $xmpp_password);

		try {
			$xmppPrebind->auth();
			$result = $xmppPrebind->getSessionInfo();
		} catch (XmppPrebindException $e) {
			throw new Exception($e->getMessage(), 401);
		}

		return $result;
	}
}
