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
	var $initialized = false;
	
	function __construct($session_type)
	{
		global $prefs, $smarty;
		$this->initialized = true;

		if (!$this->testSetup()) {
			return false;
		}
		$this->kconfig = new KalturaConfiguration($prefs['kaltura_partnerId']);
		$this->kconfig->serviceUrl = $prefs['kaltura_kServiceUrl'];
		$this->client = new KalturaClient($this->kconfig);

		// test that the service is still available (for this partner id etc)
		try {
			if ($session_type == SESSION_ADMIN) {
				$this->startAdminSession();
			} else {
				$this->startUserSession();
			}
			$smarty->assign('kServiceUrl', $prefs['kaltura_kServiceUrl']);

		} catch (Exception $e) {

			$this->initialized = false;
			TikiLib::lib('errorreport')->report($e->getMessage());
		}
	}

	function testSetup() {
		global $prefs;
		if (!$this->initialized || empty($prefs['kaltura_partnerId']) || !is_numeric($prefs['kaltura_partnerId']) || empty($prefs['kaltura_secret']) || empty($prefs['kaltura_adminSecret'])) {
			return false;
		} else {
			return true;
		}
	}
	
	private function startAdminSession() {
		global $prefs, $user;
		if (!$this->testSetup()) {
			return;
		}
		if ($user) {
			$kuser = $user;
		} else {
			$kuser = 'Anonymous';
		}
		if (!is_object($this->session)) {
			$this->session = $this->client->session->start($prefs['kaltura_adminSecret'], $kuser, SESSION_ADMIN, $prefs['kaltura_partnerId'], 86400, 'edit:*');
		}
		$this->client->setKs($this->session);
	}
	
	private function startUserSession() {
		global $prefs, $user;
		if (!$this->testSetup()) {
			return;
		}
		if ($user) {
			$kuser = $user;
		} else {
			$kuser = 'Anonymous';
		}
		if (!is_object($this->session)) {
			$this->session = $this->client->session->start($prefs['kaltura_secret'], $kuser, SESSION_USER, $prefs['kaltura_partnerId'], 86400, 'edit:*');
		}
		$this->client->setKs($this->session);
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
		if (!$this->testSetup()) {
			return null;
		}
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
	
	function getPlayersUiConf($playerId) {
		$obj = $this->_getPlayersUiConf($playerId)->objects;
		$arr = array();
		if (count($obj) === 1) {
			$arr = get_object_vars($obj[0]);
		}
		return $arr;
	}

	private function _getPlayersUiConf($playerId)
	{
		if (!$this->session) {
			$this->startAdminSession();
		}
		$filter = new KalturaUiConfFilter();
		$filter->objTypeEqual = 1; // 1 denotes Players
		$filter->idEqual = $playerId;
		$uiConf = $this->client->uiConf->listAction($filter);

		if (!is_null($this->client->error))
		{
			$uiConf = new stdClass();
			$uiConf->objects = array();
		}

		return($uiConf);
	}

	function updateStandardTikiKcw() {
		if (!$this->testSetup()) {
			return '';
		}
		// first check if there is an existing one
		$pager = null;
		$filter = new KalturaUiConfFilter();
		$filter->nameLike = 'Tiki.org Standard';
		$filter->objTypeEqual = KalturaUiConfObjType::CONTRIBUTION_WIZARD;
		$existing = $this->client->uiConf->listAction($filter, $pager);
		if (count($existing->objects) > 0) {
			$current_obj = array_pop($existing->objects);
			$current = $current_obj->id;
		} else {
			$current = '';
		}
		
		global $tikipath;
		$uiConf = new KalturaUiConf();
		$uiConf->name = 'Tiki.org Standard';
		$uiConf->objType = KalturaUiConfObjType::CONTRIBUTION_WIZARD;
		$filename = $tikipath . "lib/videogals/standardTikiKcw.xml";
		$fh = fopen($filename, 'r');
		$confXML = fread($fh, filesize($filename));
		$uiConf->confFile = $confXML;
		$uiConf->useCdn = 1;
		$uiConf->swfUrl = '/flash/kcw/v2.1.4/ContributionWizard.swf';
		$uiConf->tags = 'autodeploy, content_v3.2.5, content_upload';
		
		// first try to update
	 	if ($current) {
			 try {
				 $results = $this->client->uiConf->update($current, $uiConf);
				 if (isset($results->id)) {
					 return $results->id;
				 }
			 } catch (Exception $e) {
				 TikiLib::lib('errorreport')->report($e->getMessage());
			 }
			 try {
				 // create if updating failed or not updating
				 $uiConf->creationMode = KalturaUiConfCreationMode::ADVANCED;
				 $results = $this->client->uiConf->add($uiConf);
				 if (isset($results->id)) {
					 return $results->id;
				 } else {
					 return '';
				 }
			 } catch (Exception $e) {
				 TikiLib::lib('errorreport')->report($e->getMessage());
			 }
		}
		return '';
	}
	
}

global $kalturalib, $kalturaadminlib;
$kalturaadminlib = new KalturaLib(SESSION_ADMIN);
if ($kalturaadminlib->session) {	// only make user session if admin one worked
	$kalturalib = new KalturaLib(SESSION_USER);
}
