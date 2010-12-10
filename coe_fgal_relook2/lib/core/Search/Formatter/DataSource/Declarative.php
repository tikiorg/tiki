<?php

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
			$missingFields = $fields;
			
			$entry = array_merge($entry, $this->obtainFromContentSource($type, $object, $missingFields));

			$initial = $entry;
			foreach ($this->globalSources as $globalSource) {
				$entry = array_merge($entry, $this->obtainFromGlobalSource($globalSource, $type, $object, $missingFields, $initial));
			}
		}

		return $list;
	}

	private function obtainFromContentSource($type, $object, & $missingFields)
	{
		$requiresHighlight = in_array('highlight', $missingFields);

		if (isset($this->contentSources[$type])) {
			$contentSource = $this->contentSources[$type];

			if ($this->sourceProvidesValue($contentSource, $missingFields)) {
				$data = $contentSource->getDocument($object, new Search_Type_Factory_Direct);
				
				if (! $requiresHighlight) {
					$data = array_intersect_key($data, array_combine($missingFields, $missingFields));
				}

				$missingFields = array_diff($missingFields, array_keys($data));

				$raw = array();
				foreach ($data as $key => $value) {
					$raw[$key] = $value->getValue();
				}

				return $raw;
			}
		}

		return array();
	}

	private function obtainFromGlobalSource($globalSource, $type, $object, & $missingFields, $data)
	{
		$requiresHighlight = in_array('highlight', $missingFields);

		if ($this->sourceProvidesValue($globalSource, $missingFields)) {
			$data = $globalSource->getData($type, $object, new Search_Type_Factory_Direct, $data);

			if (! $requiresHighlight) {
				$data = array_intersect_key($data, array_combine($missingFields, $missingFields));
			}

			$missingFields = array_diff($missingFields, array_keys($data));

			$raw = array();
			foreach ($data as $key => $value) {
				$raw[$key] = $value->getValue();
			}

			return $raw;
		}

		return array();
	}

	private function sourceProvidesValue($contentSource, $missingFields)
	{
		return in_array('highlight', $missingFields) || count(array_intersect($missingFields, $contentSource->getProvidedFields())) > 0;
	}
}

