<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_UserSource implements Search_ContentSource_Interface
{
	private $db;
	private $user;
	private $tiki;
	private $geo;
	private $trk;
	private $visibility;

	function __construct($visibility)
	{
		$this->db = TikiDb::get();
		$this->user = TikiLib::lib('user');
		$this->tiki = TikiLib::lib('tiki');
		$this->geo = TikiLib::lib('geo');
		$this->trk = TikiLib::lib('trk');
		$this->visibility = $visibility;
	}

	function getDocuments()
	{
		return $this->db->table('users_users')->fetchColumn('login', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $prefs;

		$detail = $this->user->get_user_details($objectId, false);

		$name = $objectId;
		if (! empty($detail['preferences']['realName'])) {
			$name = $detail['preferences']['realName'];
		}

		$content = '';
		if ($prefs['feature_wiki_userpage'] == 'y' && ! empty($prefs['feature_wiki_userpage_prefix'])) {
			$page = $prefs['feature_wiki_userpage_prefix'] . $objectId;
			if ($info = $this->tiki->get_page_info($page, true, true)) {
				$content = $info['data'];
			}
		}

		$loc = $this->geo->build_location_string($detail['preferences']);

		$country = '';
		if (isset($detail['preferences']['country'])) {
			$country = $detail['preferences']['country'];
		}
		$gender = '';
		if (isset($detail['preferences']['gender'])) {
			$gender = $detail['preferences']['gender'];
		}
		$homePage = '';
		if (isset($detail['preferences']['homePage'])) {
			$homePage = $detail['preferences']['homePage'];
		}
		$realName = '';
		if (isset($detail['preferences']['realName'])) {
			$realName = $detail['preferences']['realName'];
		}
		if ($prefs['allowmsg_is_optional'] == 'y' && isset($detail['preferences']['allowMsgs'])) {
			$allowMsgs = $detail['preferences']['allowMsgs'];
		}else{
			$allowMsgs = 'y';
		}
		if (isset($detail['preferences']['user_style'])) {
			$user_style = $detail['preferences']['user_style'];
		} else {
			$user_style = isset($prefs['site_style']) ? $prefs['site_style'] : "" ;
		}

		$user_language = $this->tiki->get_language($objectId);
		$langLib = TikiLib::lib('language');
		$user_language_text = $langLib->format_language_list(array($user_language));

		$userPage = $prefs['feature_wiki_userpage_prefix'] . $objectId;
		if (! $this->tiki->page_exists($userPage)){
			$userPage = "";
		}


		$data = array(
			'title' => $typeFactory->sortable($name),
			'creation_date' => $typeFactory->timestamp($detail['info']['created']),
			'wiki_content' => $typeFactory->wikitext($content),
			'user_country' => $typeFactory->sortable($country),
			'user_gender' => $typeFactory->sortable($gender),
			'user_homepage' => $typeFactory->sortable($homePage),
			'user_realName' => $typeFactory->sortable($realName),
			'user_allowmsgs' => $typeFactory->sortable($allowMsgs),
			'user_language' => $typeFactory->multivalue($user_language),
			'user_style' => $typeFactory->sortable($user_style),
			'user_page' => $typeFactory->sortable($userPage),
			'geo_located' => $typeFactory->identifier(empty($loc) ? 'n' : 'y'),
			'geo_location' => $typeFactory->identifier($loc),
			'searchable' => $typeFactory->identifier($this->userIsIndexed($detail) ? 'y' : 'n'),
			'groups' => $typeFactory->multivalue($detail['groups']),
			'_extra_groups' => array('Registered'), // Add all registered to allowed groups
		);

		$data = array_merge($data, $this->getTrackerFieldsForUser($objectId, $typeFactory));

		return $data;
	}

	private function userIsIndexed($detail)
	{
		if ($this->visibility == 'all') {
			return true;
		} elseif (isset($detail['preferences']['user_information'])) {
			return $detail['preferences']['user_information'] == 'public';
		} else {
			return false;
		}
	}

	function getProvidedFields()
	{
		static $data;

		if (is_array($data)) {
			return $data;
		}

		$data = array(
			'title',
			'wiki_content',

			'geo_located',
			'geo_location',
			'user_country',

			'searchable',
		);

		foreach ($this->getAllIndexableHandlers() as $baseKey => $handler) {
			$data = array_merge($data, $handler->getProvidedFields($baseKey));
		}

		return array_unique($data);
	}

	function getGlobalFields()
	{
		static $data;

		if (is_array($data)) {
			return $data;
		}

		$data = array(
			'title' => true,

			'wiki_content' => false,
			'user_country' => true,
		);

		foreach ($this->getAllIndexableHandlers() as $baseKey => $handler) {
			$data = array_merge($data, $handler->getGlobalFields($baseKey));
		}

		return $data;
	}

	private function getAllIndexableHandlers()
	{
		$result = $this->db->fetchAll("SELECT DISTINCT usersTrackerId FROM users_groups WHERE usersTrackerId IS NOT NULL");

		$handlers = array();
		foreach ($result as $row) {
			if ($definition = Tracker_Definition::get($row['usersTrackerId'])) {
				$handlers = array_merge($handlers, Search_ContentSource_TrackerItemSource::getIndexableHandlers($definition));
			}
		}

		return $handlers;
	}

	private function getTrackerFieldsForUser($user, $typeFactory)
	{
		$result = $this->db->fetchAll(
			"SELECT usersTrackerId trackerId, itemId
			FROM
				users_groups
				INNER JOIN tiki_tracker_item_fields ON usersFieldId = fieldId
			WHERE value = ? AND usersTrackerId IS NOT NULL
			", array($user)
		);

		$data = array();
		foreach ($result as $row) {
			$definition = Tracker_Definition::get($row['trackerId']);

			if (! $definition) {
				continue;
			}

			$item = $this->trk->get_tracker_item($row['itemId']);

			foreach (Search_ContentSource_TrackerItemSource::getIndexableHandlers($definition, $item) as $baseKey => $handler) {
				$data = array_merge($data, $handler->getDocumentPart($typeFactory));
			}
		}

		return $data;
	}
}

