<?php

class Search_Indexer
{
	private $searchIndex;
	private $contentSources = array();
	private $globalSources = array();

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
		return $stat;
	}

	function update(array $objectList)
	{
		$this->searchIndex->invalidateMultiple($objectList);

		foreach ($objectList as $object) {
			$this->addDocument($object['object_type'], $object['object_id']);
		}
	}

	private function addDocument($objectType, $objectId)
	{
		$typeFactory = $this->searchIndex->getTypeFactory();
		if (isset($this->contentSources[$objectType])) {
			$contentSource = $this->contentSources[$objectType];

			if (false !== $data = $contentSource->getDocument($objectId, $typeFactory)) {
				$initialData = $data;

				foreach ($this->globalSources as $globalSource) {
					$data = array_merge($data, $globalSource->getData($objectType, $objectId, $typeFactory, $initialData));
				}

				$base = array(
					'object_type' => $typeFactory->identifier($objectType),
					'object_id' => $typeFactory->identifier($objectId),
				);

				$this->searchIndex->addDocument(array_merge($data, $base));
			}
		}
	}
}

