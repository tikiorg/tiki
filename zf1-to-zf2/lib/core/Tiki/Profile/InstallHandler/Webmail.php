<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Webmail extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'accountId' => null,	// use current account if null or empty
			'accountName' => '',	// as above
			'to' => '',
			'cc' => '',
			'bcc' => '',
			'subject' => '',
			'body' => '',
			'fattId' => null,         // add a File Gallery file as an attachment
			'pageaftersend' => null,  // defines wiki page to go to after webmail is sent
			'html' => 'y',
		);

		$data = array_merge($defaults, $this->obj->getData());
				
		return $this->data = $data;
	}

	function canInstall()
	{
		global $user, $webmaillib;
		require_once 'lib/webmail/webmaillib.php';

		$data = $this->getData();
		
		if ( !isset( $data['accountId']) && !isset( $data['accountName']) && !$webmaillib->get_current_webmail_accountId($user)) {
			return false;	// webmail account not specified
		}
		
		if ( !isset( $data['to']) && !isset( $data['cc']) && !isset( $data['bcc']) && !isset( $data['subject']) && !isset( $data['body'])) {
			return false;	// nothing specified?
		}
				
		return true;
	}

	function _install()
	{
		global $tikilib, $user;
		$data = $this->getData();
		
		$this->replaceReferences($data);

		global $webmaillib; require_once 'lib/webmail/webmaillib.php';
		
		if (!empty($data['accountId']) && $data['accountId'] != $webmaillib->get_current_webmail_accountId($user)) {
			$webmaillib->current_webmail_account($user, $data['accountId']);
		} else if (!empty($data['accountName'])) {
			$data['accountId'] = $webmaillib->get_webmail_account_by_name($user, $data['accountName']);
			if ($data['accountId'] > 0 && $data['accountId'] != $webmaillib->get_current_webmail_accountId($user)) {
				$webmaillib->current_webmail_account($user, $data['accountId']);
			}
		}	

		if ( strpos($data['body'], 'wikidirect:') === 0 ) {
			$pageName = substr($this->content, strlen('wikidirect:'));
			$data['body'] = $this->obj->getProfile()->getPageContent($pageName);
		}
		
		if (!$data['html']) {
			$data['body'] = strip_tags($data['body']);
		}
		$data['to']      = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['to']))), ' ,');
		$data['cc']      = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['cc']))), ' ,');
		$data['bcc']     = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['bcc']))), ' ,');
		$data['subject'] = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['subject']))));
		
		$webmailUrl = $tikilib->tikiUrl(
					'tiki-webmail.php',
					array(
						'locSection' => 'compose',
						'to' => $data['to'],
						'cc' => $data['cc'],
						'bcc' => $data['bcc'],
						'subject' => $data['subject'],
						'body' => $data['body'],
						'fattId' => $data['fattId'],
						'pageaftersend' => $data['pageaftersend'],
						'useHTML' => $data['html'] ? 'y' : 'n'
					)
		);

		header('Location: ' . $webmailUrl);
		exit;	// means this profile never gets "remembered" - a good thing?
	}
}