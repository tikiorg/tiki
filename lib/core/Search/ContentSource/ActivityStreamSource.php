<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ActivityStreamSource implements Search_ContentSource_Interface
{
	private $lib;
	private $userlib;
	private $sociallib;
	private $source;

	function __construct($source = null)
	{
		global $prefs;
		$this->lib = TikiLib::lib('activity');
		$this->userlib = TikiLib::lib('user');
		$this->source = $source;

		if ($prefs['feature_friends'] == 'y') {
			$this->sociallib = TikiLib::lib('social');
		}
	}

	function getDocuments()
	{
		return $this->lib->getActivityList();
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $prefs;

		if (! $info = $this->lib->getActivity($objectId, $typeFactory)) {
			return false;
		}

		$mapping = $this->lib->getMapping();

		$document = array(
			'event_type' => $typeFactory->identifier($info['eventType']),
			'modification_date' => $typeFactory->timestamp($info['eventDate']),
		);

		foreach ($info['arguments'] as $key => $value) {
			$type = isset($mapping[$key]) ? $mapping[$key] : '';

			if ($type) {
				$document[$key] = $typeFactory->$type($value);
			}
		}

		if ($this->source && isset($document['type'], $document['object'])) {
			$related = $this->source->getDocuments($info['arguments']['type'], $info['arguments']['object']);

			if (count($related)) {
				$first = reset($related);
				$collectedFields = array('allowed_groups', 'categories', 'deep_categories', 'freetags', 'freetags_text', 'geo_located', 'geo_location');

				foreach ($collectedFields as $field) {
					if (isset($first[$field])) {
						$document[$field] = $first[$field];
					}
				}
			}
		}

		if (! empty($info['arguments']['user'])) {
			$groups = $this->userlib->get_user_groups_inclusion($info['arguments']['user']);
			unset($groups['Anonymous'], $groups['Registered']);
			$document['user_groups'] = $typeFactory->multivalue(array_keys($groups));

			if ($this->sociallib) {
				$followers = $this->getFollowers($info['arguments']['user']);
				$document['user_followers'] = $typeFactory->multivalue($followers);
			}
		}

		return $document;
	}

	function getProvidedFields()
	{
		$mapping = $this->lib->getMapping();
		return array_merge(array('event_type', 'modification_date'), array_keys($mapping));
	}

	function getGlobalFields()
	{
		return array(
		);
	}

	private function getFollowers($user)
	{
		static $localCache = array();

		if (! isset($localCache[$user])) {
			$list = $this->sociallib->listFollowers($user);
			$localCache[$user] = array_map(function ($entry) {
				return $entry['user'];
			}, $list);
		}

		return $localCache[$user];
	}
}

