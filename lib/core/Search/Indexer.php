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
		$typeFactory = $this->searchIndex->getTypeFactory();

		foreach ($this->contentSources as $objectType => $contentSource) {
			$type = $typeFactory->identifier($objectType);

			foreach ($contentSource->getDocuments() as $objectId) {
				$data = $contentSource->getDocument($objectId, $typeFactory);

				foreach ($this->globalSources as $globalSource) {
					$data = array_merge($data, $globalSource->getData($objectType, $objectId, $typeFactory));
				}

				$base = array('object_type' => $type, 'object_id' => $typeFactory->identifier($objectId));

				$this->searchIndex->addDocument(array_merge($data, $base));
			}
		}
	}
}

