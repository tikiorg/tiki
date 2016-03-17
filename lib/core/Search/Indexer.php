<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Indexer
{
	private $searchIndex;
	private $contentSources = array();
	private $globalSources = array();
	private $addonSources = array();

	private $cacheGlobals = null;
	private $cacheTypes = array();

	private $contentFilters = array();

	public $log = null;

	public function __construct(Search_Index_Interface $searchIndex, $logWriter = null)
	{
		if (! $logWriter instanceof \Zend\Log\Writer\AbstractWriter) {
			$logWriter = new Zend\Log\Writer\Noop();
		}
		$logWriter->setFormatter(new Zend\Log\Formatter\Simple(Zend\Log\Formatter\Simple::DEFAULT_FORMAT . ' [%memoryUsage% bytes]' . PHP_EOL));
		$this->log = new Zend\Log\Logger();
		$this->log->addWriter($logWriter);

		$this->searchIndex = $searchIndex;

		$api = new TikiAddons_Api_Search();
		$this->addonSources = $api->getAddonSources();
	}

	public function addContentSource($objectType, Search_ContentSource_Interface $contentSource)
	{
		$this->contentSources[$objectType] = $contentSource;
	}

	public function addGlobalSource(Search_GlobalSource_Interface $globalSource)
	{
		$this->globalSources[] = $globalSource;
	}

	public function clearSources()
	{
		$this->contentSources = array();
		$this->globalSources = array();
	}

	public function addContentFilter(Zend\Filter\FilterInterface $filter)
	{
		$this->contentFilters[] = $filter;
	}

	/**
	 * Rebuild the entire index.
	 * @return array
	 */
	public function rebuild()
	{
		$this->log('Starting rebuild');
		$stat = array_fill_keys(array_keys($this->contentSources), 0);

		foreach ($this->contentSources as $objectType => $contentSource) {
			foreach ($contentSource->getDocuments() as $objectId) {
				$stat[$objectType] += $this->addDocument($objectType, $objectId);
			}
		}

		$this->log('Starting optimization');
		$this->searchIndex->optimize();
		$this->log('Finished optimization');
		$this->log('Finished rebuild');
		return $stat;
	}

	public function update(array $objectList)
	{
		$this->searchIndex->invalidateMultiple($objectList);

		foreach ($objectList as $object) {
			$this->addDocument($object['object_type'], $object['object_id']);
		}

		$this->searchIndex->endUpdate();
	}

	private function addDocument($objectType, $objectId)
	{
		$this->log("addDocument $objectType $objectId");

		$data = $this->getDocuments($objectType, $objectId);
		foreach ($data as $entry) {
			try {
				$this->searchIndex->addDocument($entry);
			} catch (Exception $e) {
				$msg = tr('Indexing failed while processing "%0" (type %1) with the error "%2"', $objectId, $objectType, $e->getMessage());
				TikiLib::lib('errorreport')->report($msg);
				$this->log->err($msg);
			}
		}

		return count($data);
	}

	public function getDocuments($objectType, $objectId)
	{
		$out = array();

		$typeFactory = $this->searchIndex->getTypeFactory();

		if (isset($this->contentSources[$objectType])) {
			$globalFields = $this->getGlobalFields($objectType);

			$contentSource = $this->contentSources[$objectType];

			if (false !== $data = $contentSource->getDocument($objectId, $typeFactory)) {
				if ($data === null) {
					TikiLib::lib('errorreport')->report(tr('Object %0 type %1 returned null from getDocument function', $objectId, $objectType));
					$data = array();
				}
				if (! is_int(key($data))) {
					$data = array($data);
				}

				foreach ($data as $entry) {
					$out[] = $this->augmentDocument($objectType, $objectId, $entry, $typeFactory, $globalFields);
				}
			}
		}

		return $out;
	}

	private function augmentDocument($objectType, $objectId, $data, $typeFactory, $globalFields)
	{
		$initialData = $data;

		foreach ($this->globalSources as $globalSource) {
			$local = $globalSource->getData($objectType, $objectId, $typeFactory, $initialData);

			if (false !== $local) {
				$data = array_merge($data, $local);
			}
		}
		foreach ($this->addonSources as $addonSource) {
			if ($addonSource->toIndex($objectType, $objectId, $initialData)) {
				$local = $addonSource->getData($objectType, $objectId, $typeFactory, $initialData);

				if (false !== $local) {
					$data = array_merge($data, $local);
				}
			}
		}

		$base = array(
			'object_type' => $typeFactory->identifier($objectType),
			'object_id' => $typeFactory->identifier($objectId),
			'contents' => $typeFactory->plaintext($this->getGlobalContent($data, $globalFields)),
		);

		$data = array_merge(array_filter($data), $base);
		$data = $this->applyFilters($data);

		$data = $this->removeTemporaryKeys($data);

		return $data;
	}

	private function applyFilters($data)
	{
		$keys = array_keys($data);

		foreach ($keys as $key) {
			$value = $data[$key];

			if (is_callable(array($value, 'filter'))) {
				$data[$key] = $value->filter($this->contentFilters);
			}
		}

		return $data;
	}

	private function removeTemporaryKeys($data)
	{
		$keys = array_keys($data);
		$toRemove = array_filter(
			$keys,
			function ($key) {
				return $key{0} === '_';
			}
		);

		foreach ($keys as $key) {
			if ($key{0} === '_') {
				unset($data[$key]);
			}
		}

		return $data;
	}

	private function getGlobalContent(array & $data, $globalFields)
	{
		$content = '';

		foreach ($globalFields as $name => $preserve) {
			if (isset($data[$name])) {
				$v = $data[$name]->getValue();
				if (is_string($v)) {
					$content .= $v . ' ';

					if (! $preserve) {
						$data[$name] = false;
					}
				}
			}
		}

		return $content;
	}

	private function getGlobalFields($objectType)
	{
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

	private function log($message)
	{
		$this->log->info($message, array('memoryUsage' => memory_get_usage()));
	}
}

