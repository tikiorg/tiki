<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$ 


require_once ('lib/videogals/KalturaClient.php');

class KalturaLib
{
	const CONFIGURATION_LIST = 'kaltura_configuration_list';
	const SESSION_ADMIN = 2;
	const SESSION_USER = 0;

	private $kconfig;
	private $client;
	private $sessionType;
	private $initialized = false;
	
	function __construct($session_type)
	{
		$this->sessionType = $session_type;
	}

	public function getSessionKey()
	{
		$tikilib = TikiLib::lib('tiki');

		if ($session = $this->storedKey()) {
			return $session;
		}

		if ($this->getClient()) {
			return $this->storedKey();
		}
	}

	private function storedKey($key = null)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');
		$session = "kaltura_session_{$this->sessionType}_$user";

		if (is_null($key)) {
			if (isset($_SESSION[$session]) && $_SESSION[$session]['expiry'] > $tikilib->now) {
				return $_SESSION[$session]['key'];
			}
		} else {
			$_SESSION[$session] = array(
				'key' => $key,
				'expiry' => $tikilib->now + 1800, // Keep for half an hour
			);
		}
	}

	private function getConfig()
	{
		if (! $this->kconfig) {
			global $prefs;
			$this->kconfig = new KalturaConfiguration($prefs['kaltura_partnerId']);
			$this->kconfig->serviceUrl = $prefs['kaltura_kServiceUrl'];
		}

		return $this->kconfig;
	}

	private function getClient()
	{
		if (! $this->initialized && ! $this->client) {
			$this->initialized = true;
			try {
				$client = new KalturaClient($this->getConfig());
				if ($session = $this->storedKey()) {
					$client->setKs($session);
					$this->client = $client;
				} elseif ($session = $this->initializeClient($client)) {
					$client->setKs($session);
					$this->client = $client;
					$this->storedKey($session);
				}
			} catch (Exception $e) {
				TikiLib::lib('errorreport')->report($e->getMessage());
			}
		}

		return $this->client;
	}

	function getMediaUrl($entryId, $playerId)
	{
		global $prefs;
		$config = $this->getConfig();
		return $config->serviceUrl . "kwidget/wid/_{$prefs['kaltura_partnerId']}/uiconf_id/$playerId/entry_id/$entryId";
	}

	function getPlaylist($entryId) {
		return $this->getClient()->playlist->get($entryId);
	}

	function testSetup() {
		global $prefs;
		if (empty($prefs['kaltura_partnerId']) || !is_numeric($prefs['kaltura_partnerId']) || empty($prefs['kaltura_secret']) || empty($prefs['kaltura_adminSecret'])) {
			return false;
		} else {
			return true;
		}
	}
	
	private function initializeClient($client) {
		global $prefs, $user;

		if (! $this->testSetup()) {
			return false;
		}

		if ($user) {
			$kuser = $user;
		} else {
			$kuser = 'Anonymous';
		}

		if ($this->sessionType == self::SESSION_ADMIN) {
			$session = $client->session->start($prefs['kaltura_adminSecret'], $kuser, self::SESSION_ADMIN, $prefs['kaltura_partnerId'], 86400, 'edit:*');
		} else {
			$session = $client->session->start($prefs['kaltura_secret'], $kuser, self::SESSION_USER, $prefs['kaltura_partnerId'], 86400, 'edit:*');
		}

		return $session;
	}
	
	private function _getPlayersUiConfs()
	{
		if ($client = $this->getClient()) {
			$filter = new KalturaUiConfFilter();
			$filter->objTypeEqual = 1; // 1 denotes Players
			$filter->orderBy = '-createdAt';
			$uiConfs = $client->uiConf->listAction($filter);
			
			if (is_null($client->error)) {
				return $uiConfs;
			}
		}

		$uiConfs = new stdClass();
		$uiConfs->objects = array();

		return $uiConfs;
	}
	
	function getPlayersUiConfs() {
		$cachelib = TikiLib::lib('cache');

		if (! $configurations = $cachelib->getSerialized(self::CONFIGURATION_LIST)) {
			try {
				$obj = $this->_getPlayersUiConfs()->objects;
			} catch (Exception $e) {
				TikiLib::lib('errorreport')->report($e->getMessage());
				return array();
			}
			$configurations = array();
			foreach ($obj as $o) {
				$configurations[] = get_object_vars($o);
			}

			$cachelib->cacheItem(self::CONFIGURATION_LIST, serialize($configurations));
		}

		return $configurations;
	}

	function getPlayersUiConf($playerId) {
		// Ontaining full list, because it is cached
		$confs = $this->getPlayersUiConfs();

		foreach ($confs as $config) {
			if ($config['id'] == $playerId) {
				return $config;
			}
		}
	}

	function updateStandardTikiKcw() {
		if ($client = $this->getClient()) {
			// first check if there is an existing one
			$pager = null;
			$filter = new KalturaUiConfFilter();
			$filter->nameLike = 'Tiki.org Standard 2013';
			$filter->objTypeEqual = KalturaUiConfObjType::CONTRIBUTION_WIZARD;
			$existing = $client->uiConf->listAction($filter, $pager);
			if (count($existing->objects) > 0) {
				$current_obj = array_pop($existing->objects);
				$current = $current_obj->id;
			} else {
				$current = '';
			}

			global $tikipath;
			$uiConf = new KalturaUiConf();
			$uiConf->name = 'Tiki.org Standard 2013';
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
					 $results = $client->uiConf->update($current, $uiConf);
					 if (isset($results->id)) {
						 return $results->id;
					 }
				 } catch (Exception $e) {
					 TikiLib::lib('errorreport')->report($e->getMessage());
				 }
			 } else {
				 try {
					 // create if updating failed or not updating
					 $uiConf->creationMode = KalturaUiConfCreationMode::ADVANCED;
					 $results = $client->uiConf->add($uiConf);
					 if (isset($results->id)) {
						 return $results->id;
					 } else {
						 return '';
					 }
				 } catch (Exception $e) {
					 TikiLib::lib('errorreport')->report($e->getMessage());
				 }
			}
		}

		return '';
	}

	public function cloneMix($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->mixing->cloneAction($entryId);
		}
	}

	public function deleteMedia($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->media->delete($entryId);
		}
	}

	public function deleteMix($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->mixing->delete($entryId);
		}
	}

	public function flattenVideo($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->mixing->requestFlattening($entryId, 'flv');
		}
	}

	public function getMix($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->mixing->get($entryId);
		}
	}

	public function updateMix($entryId, array $data)
	{
		if ($client = $this->getClient()) {
			$kentry = new KalturaPlayableEntry();
			$kentry->name = $data['name'];
			$kentry->description = $data['description'];
			$kentry->tags = $data['tags'];
			$kentry->editorType = $data['editorType'];
			$kentry->adminTags = $data['adminTags'];

			return $client->mixing->update($entryId, $kentry);
		}
	}

	public function getMedia($entryId)
	{
		if ($client = $this->getClient()) {
			return $client->media->get($entryId);
		}
	}

	public function updateMedia($entryId, array $data)
	{
		if ($client = $this->getClient()) {
			$kentry = new KalturaPlayableEntry();
			$kentry->name = $data['name'];
			$kentry->description = $data['description'];
			$kentry->tags = $data['tags'];
			$kentry->adminTags = $data['adminTags'];

			return $client->media->update($entryId, $kentry);
		}
	}
	
	public function listMix($sort_mode, $page, $page_size, $find)
	{
		if ($client = $this->getClient()) {
			$kpager = new KalturaFilterPager();
			$kpager->pageIndex = $page;
			$kpager->pageSize = $page_size;

			$kfilter = new KalturaMixEntryFilter();
			$kfilter->orderBy = $sort_mode;
			$kfilter->nameMultiLikeOr = $find;
			
			return $client->mixing->listAction($kfilter, $kpager);
		}
	}
	
	public function listMedia($sort_mode, $page, $page_size, $find)
	{
		if ($client = $this->getClient()) {
			$kpager = new KalturaFilterPager();
			$kpager->pageIndex = $page;
			$kpager->pageSize = $page_size;

			$kfilter = new KalturaMediaEntryFilter();
			$kfilter->orderBy = $sort_mode;
			$kfilter->nameMultiLikeOr = $find;
			$kfilter->statusIn = '-1,-2,0,1,2';
			
			return $client->media->listAction($kfilter, $kpager);
		}
	}

	public function getMovieList(array $movies)
	{
		if (count($movies) && $client = $this->getClient()) {
			$kpager = new KalturaFilterPager();
			$kpager->pageIndex = 0;
			$kpager->pageSize = count($movies);

			$kfilter = new KalturaMediaEntryFilter();
			$kfilter->idIn = implode(',', $movies);
			
			$mediaList = array();
			foreach ($client->media->listAction($kfilter, $kpager)->objects as $media) {
				$mediaList[] = array(
					'id' => $media->id,
					'name' => $media->name,
				);
			}

			return $mediaList;
		}

		return array();
	}
}

