<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_GroupSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('users_groups')->fetchColumn('groupName', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$row = $this->db->table('users_groups')->fetchRow(['groupDesc'], array('groupName' => $objectId));

		if (! $row) {
			return false;
		}

		$api = new TikiAddons_Api_Group;
		$groupName = $objectId;
		$addongroup = false;
		$addonpendinggroup = false;
		$addonleadergroup = false;
	
		if ($ret = $api->getOrganicGroupName($objectId)) {
			$groupName = $ret;
			$addongroup = $api->isOrganicGroup($objectId);
			if ($addongroup == true && $api->getOrganicGroupPendingToken($objectId) == $objectId) {
				$addonpendinggroup = true;
			}
			if ($addongroup == true && $api->getOrganicGroupLeaderToken($objectId) == $objectId) {
				$addonleadergroup = true;
			}
		}

		$data = array(
			'title' => $typeFactory->sortable($groupName),
			'description' => $typeFactory->plaintext($row['groupDesc']),

			'searchable' => $typeFactory->identifier('n'),

			'view_permission' => $typeFactory->identifier('tiki_p_group_view'),

			'addongroup' => $addongroup ? $typeFactory->identifier('y') : $typeFactory->identifier('n'),
			'addonleadergroup' => $addonleadergroup ? $typeFactory->identifier('y') : $typeFactory->identifier('n'),
			'addonpendinggroup' => $addonpendinggroup ? $typeFactory->identifier('y') : $typeFactory->identifier('n'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'description',

			'searchable',

			'view_permission',

			'addongroup',
			'addonleadergroup',
			'addonpendinggroup',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,
		);
	}
}

