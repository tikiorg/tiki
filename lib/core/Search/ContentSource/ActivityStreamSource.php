<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ActivityStreamSource implements Search_ContentSource_Interface
{
	private $lib;

	function __construct()
	{
		$this->lib = TikiLib::lib('activity');
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
			$type = $mapping[$key];

			if ($type) {
				$document[$key] = $typeFactory->$type($value);
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
}

