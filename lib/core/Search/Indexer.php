<?php

class Search_Indexer
{
	private $searchIndex;
	private $contentSources = array();

	function __construct(Search_Index_Interface $searchIndex)
	{
		$this->searchIndex = $searchIndex;
	}

	function addContentSource($objectType, Search_ContentSource_Interface $contentSource)
	{
		$this->contentSources[$objectType] = $contentSource;
	}

	/**
	 * Rebuild the entire index.
	 */
	function rebuild()
	{
		$typeFactory = $this->searchIndex->getTypeFactory();

		foreach ($this->contentSources as $type => $contentSource) {
			$type = $typeFactory->identifier($type);

			foreach ($contentSource->getDocuments() as $objectId) {
				$data = $contentSource->getDocument($objectId, $typeFactory);
				$base = array('object_type' => $type, 'object_id' => $typeFactory->identifier($objectId));

				$this->searchIndex->addDocument(array_merge($data, $base));
			}
		}
	}
}

