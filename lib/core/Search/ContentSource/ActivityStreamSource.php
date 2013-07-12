<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
error_reporting(E_ALL);

class Search_ContentSource_ActivityStreamSource implements Search_ContentSource_Interface
{
	private $lib;
	private $userlib;
	private $source;

	function __construct($source = null)
	{
		$this->lib = TikiLib::lib('activity');
		$this->userlib = TikiLib::lib('user');
		$this->source = $source;
	}

	function getDocuments()
	{
		return $this->lib->getActivityList();
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
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
}

