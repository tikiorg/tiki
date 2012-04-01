<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$ 

define('SESSION_ADMIN', 2);
define('SESSION_USER', 0);

require_once ('lib/videogals/KalturaClient.php');

class KalturaLib
{
	var $session;
	var $kconfig;
	var $client;
	
	function __construct($session_type)
	{
		global $prefs, $smarty;
		if (!$this->testSetup()) {
			return false;
		}
		$this->kconfig = new KalturaConfiguration($prefs['kaltura_partnerId']);
		$this->kconfig->serviceUrl = $prefs['kaltura_kServiceUrl'];
		$this->client = new KalturaClient($this->kconfig);
		if ($session_type == SESSION_ADMIN) {
			$error = $this->startAdminSession();
		} else {
			$error = $this->startUserSession();
		}
		$smarty->assign('kServiceUrl', $prefs['kaltura_kServiceUrl']);
	}

	function testSetup() {
		global $prefs, $smarty;
		if (empty($prefs['kaltura_partnerId']) || !is_numeric($prefs['kaltura_partnerId']) || empty($prefs['kaltura_secret']) || empty($prefs['kaltura_adminSecret'])) {
			return false;
		} else {
			return true;
		}
	}
	
	private function startAdminSession() {
		global $prefs, $user, $smarty;
		if ($user) {
			$kuser = $user;
		} else {
			$kuser = 'Anonymous';
		}
		try {
			if (!is_object($this->session)) {
				$this->session = $this->client->session->start($prefs['kaltura_adminSecret'], $kuser, SESSION_ADMIN, $prefs['kaltura_partnerId'], 86400, 'edit:*');	
			}
			$this->client->setKs($this->session);
		} catch (Exception $e) {	
			// silent return is important so that it can be handled gracefully above if needed 
		}
		return true;
	}
	
	private function startUserSession() {
		global $prefs, $user, $smarty;
		if ($user) {
			$kuser = $user;
		} else {
			$kuser = 'Anonymous';
		}
		try {
			if (!is_object($this->session)) {		
				$this->session = $this->client->session->start($prefs['kaltura_secret'], $kuser, SESSION_USER, $prefs['kaltura_partnerId'], 86400, 'edit:*');
			}
			$this->client->setKs($this->session);
		} catch (Exception $e) {	
			// silent return is important so that it can be handled gracefully above if needed
		}
		return true;
	}
		
	private function _getPlayersUiConfs()
	{
		if (!$this->session) {
			$this->startAdminSession();
		}
		$filter = new KalturaUiConfFilter();
		$filter->objTypeEqual = 1; // 1 denotes Players
		$filter->orderBy = +createdAt;
		$uiConfs = $this->client->uiConf->listAction($filter);
		
		if (!is_null($this->client->error))
		{
			$uiConfs = new stdClass();
			$uiConfs->objects = array();
		}
		
		return($uiConfs);
	}
	
	function getPlayersUiConfs() {
		$obj = $this->_getPlayersUiConfs()->objects;
		$arr = array();
		foreach ($obj as $o) {
			$arr[] = get_object_vars($o);
		}
		return $arr;
	}

	function getPlayersUiConfsObj() {
		return $this->_getPlayersUiConfs();		
	}
}

global $kalturalib, $kalturaadminlib;
$kalturaadminlib = new KalturaLib(SESSION_ADMIN);
$kalturalib = new KalturaLib(SESSION_USER);
