<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		if ($this->visibility == 'all') {
			return $this->db->table('users_users')->fetchColumn('login', array());
		} else {
			return array_map(
				function ($row) {
					return $row['login'];
				},
				$this->db->fetchAll(
					'SELECT login FROM users_users u INNER JOIN tiki_user_preferences p ON u.login = p.user WHERE prefName = ? AND value = ?', array('user_information', 'public')
				)
			);
		}
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $prefs;

		$detail = $this->user->get_user_details($objectId, false);

		if (! $this->userIsIndexed($detail)) {
			return false;
		}

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

		$data = array(
			'title' => $typeFactory->sortable($name),
			'wiki_content' => $typeFactory->wikitext($content),
			'user_country' => $typeFactory->sortable($detail['preferences']['country']),
			'geo_located' => $typeFactory->identifier(empty($loc) ? 'n' : 'y'),
			'geo_location' => $typeFactory->identifier($loc),
		);

		$data = array_merge($data, $this->getTrackerFieldsForUser($objectId, $typeFactory));

		return $data;
	}

	private function userIsIndexed($detail)
	{
		if ($this->visibility == 'all') {
			return true;
		} else {
			return $detail['preferences']['user_information'] == 'public';
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
				$data = array_merge($data, $handler->getDocumentPart($baseKey, $typeFactory));
			}
		}

		return $data;
	}
}

