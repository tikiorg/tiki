<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ActivityStreamSource implements Search_ContentSource_Interface
{
	private $lib;
	private $sociallib;
	private $relationlib;
	private $tikilib;
	private $source;

	function __construct($source = null)
	{
		global $prefs;
		$this->lib = TikiLib::lib('activity');
		$this->source = $source;
		$this->sociallib = TikiLib::lib('social');
		$this->relationlib = TikiLib::lib('relation');
		$this->tikilib = TikiLib::lib('tiki');
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

			'searchable' => $typeFactory->identifier('n'),
		);

		foreach ($info['arguments'] as $key => $value) {
			$type = isset($mapping[$key]) ? $mapping[$key] : '';

			if ($type) {
				$document[$key] = $typeFactory->$type($value);
			}
		}

		if (! isset($document['stream'])) {
			$document['stream'] = $typeFactory->multivalue(['custom']);
		}

		if ($this->source && isset($document['type'], $document['object'])) {
			$related = $this->source->getDocuments($info['arguments']['type'], $info['arguments']['object']);

			if (count($related)) {
				$first = reset($related);
				$collectedFields = array('allowed_groups', 'categories', 'deep_categories', 'freetags', 'freetags_text', 'geo_located', 'geo_location', 'relations', 'relation_types');

				foreach ($collectedFields as $field) {
					if (isset($first[$field])) {
						$document[$field] = $first[$field];
					}
				}
			}
		}

		$list = $this->sociallib->getLikes('activity', $objectId);
		$document['like_list'] = $typeFactory->multivalue($list);

		if ($prefs['monitor_individual_clear'] == 'y') {
			$clearList = $this->getClearList($objectId);
			$document['clear_list'] = $typeFactory->multivalue($clearList);
		} else {
			$document['clear_list'] = $typeFactory->multivalue([]);
		}

		return $document;
	}

	function getProvidedFields()
	{
		$mapping = $this->lib->getMapping();
		return array_merge(['event_type', 'modification_date', 'like_list', 'clear_list'], array_keys($mapping));
	}

	function getGlobalFields()
	{
		return array(
		);
	}

	private function getClearList($activityId)
	{
		$list = $this->relationlib->get_relations_to('activity', $activityId, 'tiki.monitor.cleared');
		$out = [];
		foreach ($list as $rel) {
			if ($rel['type'] == 'user') {
				$out[] = $rel['itemId'];
			}
		}

		return $out;
	}
}

