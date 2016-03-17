<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class FederatedSearchLib
{
	private $unified;
	private $indices = [];
	private $loaded = false;

	function __construct($unifiedsearch)
	{
		$this->unified = $unifiedsearch;
	}

	function addIndex($indexName, Search\Federated\IndexInterface $index)
	{
		$this->indices[$indexName] = $index;
	}

	public function getIndices()
	{
		$this->load();

		return $this->indices;
	}

	function augmentSimpleQuery(Search_Query $query, $content)
	{
		$indices = $this->getIndices();

		foreach ($indices as $indexName => $index) {
			$sub = $this->addForIndex($query, $indexName, $index);
			$index->applyContentConditions($sub, $content);
		}
	}

	function augmentSimilarQuery(Search_Query $query, $type, $object)
	{
		$indices = $this->getIndices();

		foreach ($indices as $indexName => $index) {
			$sub = $this->addForIndex($query, $indexName, $index);
			$index->applySimilarConditions($sub, $type, $object);
		}
	}

	private function load()
	{
		if (! $this->loaded) {
			$this->loaded = true;

			$table = TikiDb::get()->table('tiki_extwiki');
			$tikis = $table->fetchAll($table->all(), ['indexname' => $table->not('')]);

			foreach ($tikis as $tiki) {
				$this->addIndex($tiki['indexname'], new Search\Federated\TikiIndex($this->extractBaseUrl($tiki['extwiki']), json_decode($tiki['groups']) ?: []));
			}
		}
	}

	private function addForIndex($query, $indexName, $index)
	{
		$sub = new Search_Query;
		foreach ($index->getTransformations() as $trans) {
			$sub->applyTransform($trans);
		}

		$query->includeForeign($indexName, $sub);

		return $sub;
	}

	private function extractBaseUrl($url)
	{
		$slash = strrpos($url, '/');
		return substr($url, 0, $slash + 1);
	}

	public function createIndex($location, $index, $type, array $mapping)
	{
		$connection = new Search_Elastic_Connection($location);
		$connection->mapping($index, $type, $mapping);
	}
}

