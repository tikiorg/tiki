<?php

class Search_Indexer
{
	private $searchIndex;
	private $contentSources = array();
	private $globalSources = array();

	private $cacheGlobals = null;
	private $cacheTypes = array();

	function __construct(Search_Index_Interface $searchIndex)
	{
		$this->searchIndex = $searchIndex;
	}

	function addContentSource($objectType, Search_ContentSource_Interface $contentSource)
	{
		$this->contentSources[$objectType] = $contentSource;
	}

	function addGlobalSource(Search_GlobalSource_Interface $globalSource)
	{
		$this->globalSources[] = $globalSource;
	}

	/**
	 * Rebuild the entire index.
	 */
	function rebuild()
	{
		$stat = array();
		foreach ($this->contentSources as $objectType => $contentSource) {
			foreach ($contentSource->getDocuments() as $objectId) {
				$this->addDocument($objectType, $objectId);
				$stat[$objectType] = empty($stat[$objectType])? 1: $stat[$objectType] + 1;
			}
		}
		
		$this->searchIndex->optimize();
		return $stat;
	}

	function update($searchArgument)
	{
		if (is_array($searchArgument)) {
			$query = new Search_Query;
			foreach ($searchArgument as $object) {
				$query->addObject($object['object_type'], $object['object_id']);
			}

			$result = $query->invalidate($this->searchIndex);
			$objectList = $searchArgument;
		} elseif ($searchArgument instanceof Search_Query) {
			$objectList = $searchArgument->invalidate($this->searchIndex);
		}

		foreach ($objectList as $object) {
			$this->addDocument($object['object_type'], $object['object_id']);
		}
	}

	private function addDocument($objectType, $objectId)
	{
		$typeFactory = $this->searchIndex->getTypeFactory();

		if (isset($this->contentSources[$objectType])) {
			$globalFields = $this->getGlobalFields($objectType);

			$contentSource = $this->contentSources[$objectType];

			if (false !== $data = $contentSource->getDocument($objectId, $typeFactory)) {
				$initialData = $data;

				foreach ($this->globalSources as $globalSource) {
					$data = array_merge($data, $globalSource->getData($objectType, $objectId, $typeFactory, $initialData));
				}

				$base = array(
					'object_type' => $typeFactory->identifier($objectType),
					'object_id' => $typeFactory->identifier($objectId),
					'global' => $typeFactory->plaintext($this->getGlobalContent($data, $globalFields)),
				);

				$this->searchIndex->addDocument(array_merge($data, $base));
			}
		}
	}

	private function getGlobalContent(array $data, $globalFields) {
		$content = '';

		foreach ($globalFields as $name) {
			if (isset($data[$name])) {
				$v = $data[$name]->getValue();
				if (is_string($v)) {
					$content .= $v . ' ';
				}
			}
		}

		return $content;
	}

	private function getGlobalFields($objectType) {
		if (is_null($this->cacheGlobals)) {
			$this->cacheGlobals = array();
			foreach ($this->globalSources as $source) {
				$this->cacheGlobals = array_merge($this->cacheGlobals, $source->getGlobalFields());
			}
		}

		if (! isset($this->cacheTypes[$objectType])) {
			$this->cacheTypes[$objectType] = array_merge($this->cacheGlobals, $this->contentSources[$objectType]->getGlobalFields());
		}

		return $this->cacheTypes[$objectType];
	}
}

