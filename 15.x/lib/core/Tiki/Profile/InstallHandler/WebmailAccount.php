<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_WebmailAccount extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'mode' => 'create',		// 'create' or 'update' account with same name (i.e. 'account')
			'account' => '',		// * required
			'pop' => '', 			// * one of pop, imap, mbox or maildir required
			'port' => 110, 			// default for pop3
			'username' => '',
			'pass' => '',
			'msgs' => '', 			// messages per page
			'smtp' => '',
			'useAuth' => 'n', 		// y|n (default null? = n)
			'smtpPort' => 25,
			'flagsPublic' => 'n',	// y|n (default n)
			'autoRefresh' => 0, 	// seconds (default 0)
			'imap' => '',			// *? see pop
			'mbox' => '', 			// *? see pop
			'maildir' => '', 		// *? see pop
			'useSSL' => 'n',			// y|n (default n)
			'fromEmail' => '',
		);

		$data = array_merge($defaults, $this->obj->getData());

		$data['useAuth'] = $data['useAuth'] !== 'n' ? 'y' : 'n';	// should be unecessary surely, but can't find where to stop it (looked for ages!)
		$data['flagsPublic'] = $data['flagsPublic'] !== 'n' ? 'y' : 'n';
		$data['useSSL'] = $data['useSSL'] !== 'n' ? 'y' : 'n';
		$data['overwrite'] = $data['overwrite'] !== 'n' ? 'y' : 'n';

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['account']) || (!isset($data['pop']) && !isset($data['imap']) && !isset($data['mbox']) && !isset($data['maildir'] ))) {
			return false;
		}

		return true;
	}

	function _install()
	{
		global $tikilib, $user;
		$data = $this->getData();

		$this->replaceReferences($data);

		global $webmaillib; require_once 'lib/webmail/webmaillib.php';

		if ($data['mode'] == 'update') {
			$accountId = $webmaillib->get_webmail_account_by_name($user, $data['account']);
		} else {
			$accountId = 0;
		}

		$accountId = $webmaillib->replace_webmail_account(
			$accountId,
			$user,
			$data['account'],
			$data['pop'],
			(int) $data['port'],
			$data['username'],
			$data['pass'],
			(int) $data['msgs'],
			$data['smtp'],
			$data['useAuth'],
			(int) $data['smtpPort'],
			$data['flagsPublic'],
			(int) $data['autoRefresh'],
			$data['imap'],
			$data['mbox'],
			$data['maildir'],
			$data['useSSL'],
			$data['fromEmail']
		);

		return $accountId;
	}
}
