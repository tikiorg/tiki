<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: SheetSource.php 43067 2012-09-20 20:00:44Z changi67 $

class Search_ContentSource_UserSource implements Search_ContentSource_Interface
{
	private $db;
	private $user;
	private $tiki;
	private $geo;
	private $visibility;

	function __construct($visibility)
	{
		$this->db = TikiDb::get();
		$this->user = TikiLib::lib('user');
		$this->tiki = TikiLib::lib('tiki');
		$this->geo = TikiLib::lib('geo');
		$this->visibility = $visibility;
	}

	function getDocuments()
	{
		if ($this->visibility == 'all') {
			return $this->db->table('users_users')->fetchColumn('login', array());
		} else {
			return array_map(function ($row) {
				return $row['login'];
			}, $this->db->fetchAll('SELECT login FROM users_users u INNER JOIN tiki_user_preferences p ON u.login = p.user WHERE prefName = ? AND value = ?', array('user_information', 'public')));
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
			if ($info = $this->tiki->get_page_info($page)) {
				$content = $info['data'];
			}
		}

		$loc = $this->geo->build_location_string($detail['preferences']);

		return array(
			'title' => $typeFactory->sortable($name),
			'wiki_content' => $typeFactory->wikitext($content),
			'user_country' => $typeFactory->sortable($detail['preferences']['country']),
			'geo_located' => $typeFactory->identifier(empty($loc) ? 'n' : 'y'),
			'geo_location' => $typeFactory->identifier($loc),
		);
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
		return array(
			'title',
			'wiki_content',

			'geo_located',
			'geo_location',
			'user_country',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,

			'wiki_content' => false,
			'user_country' => true,
		);
	}
}

