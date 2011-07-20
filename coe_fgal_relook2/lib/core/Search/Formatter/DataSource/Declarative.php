<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_DataSource_Declarative implements Search_Formatter_DataSource_Interface
{
	private $contentSources = array();
	private $globalSources = array();

	function addContentSource($type, Search_ContentSource_Interface $contentSource)
	{
		$this->contentSources[$type] = $contentSource;
	}

	function addGlobalSource(Search_GlobalSource_Interface $globalSource)
	{
		$this->globalSources[] = $globalSource;
	}

	function getInformation($list, array $fields)
	{
		foreach ($list as & $entry) {
			$type = $entry['object_type'];
			$object = $entry['object_id'];
			$hash = isset($entry['hash']) ? $entry['hash'] : null;
			$missingFields = $fields;
			
			$entry = array_merge($entry, $this->obtainFromContentSource($type, $object, $hash, $missingFields));

			$initial = $entry;
			foreach ($this->globalSources as $globalSource) {
				$entry = array_merge($entry, $this->obtainFromGlobalSource($globalSource, $type, $object, $missingFields, $initial));
			}
		}

		return $list;
	}

	private function obtainFromContentSource($type, $object, $hash, & $missingFields)
	{
		if (isset($this->contentSources[$type])) {
			$contentSource = $this->contentSources[$type];

			if (in_array('highlight', $missingFields)) {
				$missingFields = array_merge($missingFields, array_keys($contentSource->getGlobalFields()));
			}

			if ($this->sourceProvidesValue($contentSource, $missingFields)) {
				$data = $contentSource->getDocument($object, new Search_Type_Factory_Direct);
				$used = $data;

				if (is_int(key($data)) && ! is_null($hash)) {
					$used = reset($data);

					foreach ($data as $entry) {
						if (isset($entry['hash']) && $entry['hash']->getValue() == $hash) {
							$used = $entry;
							break;
						}
					}
				} elseif (is_int(key($data))) {
					$used = reset($data);
				}

				return $this->getRaw($used, $missingFields);
			}
		}

		return array();
	}

	private function obtainFromGlobalSource($globalSource, $type, $object, & $missingFields, $data)
	{
		if (in_array('highlight', $missingFields)) {
			$missingFields = array_merge($missingFields, array_keys($globalSource->getGlobalFields()));
		}

		if ($this->sourceProvidesValue($globalSource, $missingFields)) {
			$data = $globalSource->getData($type, $object, new Search_Type_Factory_Direct, $data);

			return $this->getRaw($data, $missingFields);
		}

		return array();
	}

	private function sourceProvidesValue($contentSource, $missingFields)
	{
		return count(array_intersect($missingFields, $contentSource->getProvidedFields())) > 0;
	}

	private function getRaw($data, & $missingFields)
	{
		$data = array_intersect_key($data, array_combine($missingFields, $missingFields));

		$missingFields = array_diff($missingFields, array_keys($data));

		$raw = array();
		foreach ($data as $key => $value) {
			$value = $value->getValue();
			if (! empty($value)) {
				$raw[$key] = $value;
			}
		}

		return $raw;
	}
}

