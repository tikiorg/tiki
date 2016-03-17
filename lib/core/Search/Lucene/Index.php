<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Lucene_Index implements Search_Index_Interface
{
	private $lucene;
	private $highlight = true;
	private $cache;
	private $lastModif;
	private $directory;
	private $maxResults = 0;
	private $resultSetLimit = 0;

	function __construct($directory, $lang = 'en', $highlight = true)
	{
		switch($lang) {
		case 'en':
			default:
				ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
				ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('UTF-8');
		}

		ZendSearch\Lucene\Storage\Directory\Filesystem::setDefaultFilePermissions(0660);
		$this->directory = $directory;
		$this->lastModif = file_exists($directory) ? filemtime($directory) : 0;

		$this->highlight = (bool) $highlight;
	}

	private function getLucene()
	{
		if ($this->lucene) {
			return $this->lucene;
		}

		try {
			$this->lucene = ZendSearch\Lucene\Lucene::open($this->directory);
		} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
			$this->lucene = ZendSearch\Lucene\Lucene::create($this->directory);
		}
		global $prefs;
		if (!empty($prefs['unified_lucene_max_buffered_docs'])) {							// these break indexing if set empty
			$this->lucene->setMaxBufferedDocs($prefs['unified_lucene_max_buffered_docs']);	// default is 10
		}
		if (!empty($prefs['unified_lucene_max_merge_docs'])) {
			$this->lucene->setMaxMergeDocs($prefs['unified_lucene_max_merge_docs']);		// default is PHP_INT_MAX (effectively "infinite")
		}
		if (!empty($prefs['unified_lucene_merge_factor'])) {
			$this->lucene->setMergeFactor($prefs['unified_lucene_merge_factor']);			// default is 10
		}
		ZendSearch\Lucene\Lucene::setResultSetLimit($this->resultSetLimit);

		return $this->lucene;
	}

	function addDocument(array $data)
	{
		$document = $this->generateDocument($data);

		$this->getLucene()->addDocument($document);
	}

	function endUpdate()
	{
	}

	function optimize()
	{
		$this->getLucene()->optimize();
	}

	function destroy()
	{
		unset($this->lucene);

		return (bool) $this->destroyDirectory($this->directory);
	}

	function exists()
	{
		return file_exists($this->directory);
	}

    /**
	 * Private. Used by a callback, so made public until PHP 5.4.
	 *
     * @param $path
     * @return int
	 * @private
     */
	private function destroyDirectory($path)
	{
		if (!$path or !is_dir($path)) return false;

		if ($dir = opendir($path)) {
			while (false !== ($file = readdir($dir))) {
				if ($file == '.' || $file == '..') {
					continue;
				}

				if (is_dir($path . '/' . $file)) {
					$this->destroyDirectory($path . '/' . $file);
				} else {
					unlink($path . '/' . $file);
				}
			}
			closedir($dir);
		}

		rmdir($path);

		return ! file_exists($path);
	}


	function invalidateMultiple(array $objectList)
	{
		$expr = $this->buildExpr($objectList);

		$lucene = $this->getLucene();
		$query = $this->buildQuery($expr);
		foreach ($lucene->find($query) as $hit) {
			$document = $hit->getDocument();
			$lucene->delete($hit->id);
		}
	}

	private function buildExpr(array $objectList)
	{
		$query = new Search_Query;
		foreach ($objectList as $object) {
			$object = (array) $object;
			$query->addObject($object['object_type'], $object['object_id']);
		}

		return $query->getExpr();
	}

	function find(Search_Query_Interface $query, $resultStart, $resultCount)
	{
		$expr = $query->getExpr();
		$data = $this->internalFind($expr, $query->getSortOrder());

		$result = array_slice($data['result'], $resultStart, $resultCount);

		$resultSet = new Search_ResultSet($result, count($data['result']), $resultStart, $resultCount);
		$resultSet->setEstimate($data['count']);

		if ($this->highlight) {
			$resultSet->setHighlightHelper(new Search_Lucene_HighlightHelper($expr));
		} else {
			$resultSet->setHighlightHelper(new Search_ResultSet_SnippetHelper);
		}

		return $resultSet;
	}

	function scroll(Search_Query_Interface $query)
	{
		$expr = $query->getExpr();
		$data = $this->internalFind($expr, $query->getSortOrder());
		$resultCount = count($data['result']);
		$resultSet = new Search_ResultSet($data['result'], $resultCount, 0, $resultCount);

		return $resultSet;
	}

	function setCache($cache)
	{
		$this->cache = $cache;
	}

	function setMaxResults($max)
	{
		$this->maxResults = (int) $max;
	}

	public function setResultSetLimit($resultSetLimit)
	{
		$this->resultSetLimit = $resultSetLimit;
	}

	public function getResultSetLimit()
	{
		return $this->resultSetLimit;
	}

	private function internalFind(& $query, $sortOrder)
	{
		if ($this->cache) {
			$args = func_get_args();
			$cacheKey = serialize($args);

			$entry = $this->cache->getSerialized($cacheKey, 'searchresult', $this->lastModif);

			if ($entry) {
				$query = $entry['query'];
				return $entry['hits'];
			}
		}

		$query = $this->buildQuery($query);
		try {
			$hits = $this->getLucene()->find($query, $this->getSortField($sortOrder), $this->getSortType($sortOrder), $this->getSortOrder($sortOrder));
		} catch (Exception $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
		}

		$result = array();
		foreach ($hits as $key => $hit) {
			$res = array_merge($this->extractValues($hit->getDocument()), array('score' => round($hit->score * 100)));

			$found = false;
			if (!empty($res['object_id']) && !empty($res['object_type'])) {	// filter out duplicates here
				foreach ($result as $r) {
					if ($r['object_id'] === $res['object_id'] && $r['object_type'] === $res['object_type']) {
						$found = true;
						break;
					}
				}
			}
			if (!$found) {
				$result[] = $res;
			}

			if ($this->maxResults && count($result) >= $this->maxResults) {
				break;
			}
		}

		$return = array(
			'result' => $result,
			'count' => count($hits),
		);

		if ($this->cache) {
			$this->cache->cacheItem(
				$cacheKey,
				serialize(
					array(
						'query' => $query,
						'hits' => $return,
					)
				),
				'searchresult'
			);
		}

		return $return;
	}

	private function extractValues($document)
	{
		$data = array();
		foreach ($document->getFieldNames() as $field) {
			if (! $document->getField($field)->isTokenized) {
				$data[$field] = $document->$field;
			}
		}

		return $data;
	}

	private function getSortField($sortOrder)
	{
		return $sortOrder->getField();
	}

	private function getSortType($sortOrder)
	{
		switch ($sortOrder->getMode()) {
		case Search_Query_Order::MODE_NUMERIC:
			return SORT_NUMERIC;
		case Search_Query_Order::MODE_TEXT:
			return SORT_STRING;
		}
	}

	private function getSortOrder($sortOrder)
	{
		switch ($sortOrder->getOrder()) {
		case Search_Query_Order::ORDER_ASC:
			return SORT_ASC;
		case Search_Query_Order::ORDER_DESC:
			return SORT_DESC;
		}
	}

	function getTypeFactory()
	{
		return new Search_Lucene_TypeFactory;
	}

	private function generateDocument($data)
	{
		$document = new ZendSearch\Lucene\Document;
		$typeMap = array(
			'Search_Type_WikiText' => 'UnStored',
			'Search_Type_PlainText' => 'UnStored',
			'Search_Type_Whole' => 'Keyword',
			'Search_Type_Numeric' => 'Keyword',
			'Search_Type_Timestamp' => 'Keyword',
			'Search_Type_MultivalueText' => 'UnStored',
			'Search_Type_ShortText' => 'Text',
		);
		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$field = ZendSearch\Lucene\Document\Field::$luceneType($key, $value->getValue(), 'UTF-8');
			$document->addField($field);
		}

		return $document;
	}

	private function buildQuery($expr)
	{
		$query = (string) $expr->walk(array($this, 'walkCallback'));

		// FIX : Depending on the locale, decimals may be rendered as 1,2 instead of 1.2, causing lucene to go crazy
		$query = preg_replace('/\^(\d+),(\d+)/', '^$1.$2', $query);
		return ZendSearch\Lucene\Search\QueryParser::parse($query, 'UTF-8');
	}

	function walkCallback($node, $childNodes)
	{
		$term = null;

		if ($node instanceof Search_Expr_ImplicitPhrase) {
			$node = $node->getBasicOperator();
		}

		if ($node instanceof Search_Expr_Initial) {
			$initial = $node->getContent();
			$node = new Search_Expr_Range($initial, substr($initial, 0, -1) . chr(ord(substr($initial, -1)) + 1), $node->getType(), $node->getField());
		}

		if ($node instanceof Search_Expr_And) {
			$term = $this->buildCondition($childNodes, true);
		} elseif ($node instanceof Search_Expr_Or) {
			$term = $this->buildCondition($childNodes, null);
		} elseif ($node instanceof Search_Expr_Not) {
			$result = new ZendSearch\Lucene\Search\Query\Boolean;
			$result->addSubquery($childNodes[0], false);

			$term = $result;
		} elseif ($node instanceof Search_Expr_Range) {
			$from = $node->getToken('from');
			$to = $node->getToken('to');

			$from = $this->buildTerm($from);
			$to = $this->buildTerm($to);

			// Range search not supported for phrases, so revert to normal token matching
			if (method_exists($from, 'getTerm')) {
				$range = new ZendSearch\Lucene\Search\Query\Range(
					$from->getTerm(),
					$to->getTerm(),
					true // inclusive
				);

				$term = $range;
			} else {
				$term = $from;
			}
		} elseif ($node instanceof Search_Expr_Token) {
			$term = $this->buildTerm($node);
		} else {
			throw new Exception(tr('Feature not supported.'));
		}

		if ($term && method_exists($term, 'getBoost')) {
			$term->setBoost($node->getWeight());
		}

		return $term;
	}

	private function buildCondition($childNodes, $required)
	{
		$result = new ZendSearch\Lucene\Search\Query\Boolean;
		foreach ($childNodes as $child) {

			// Detect if child is a NOT, and reformulate on the fly to support the syntax
			if ($child instanceof ZendSearch\Lucene\Search\Query\Boolean) {
				$signs = $child->getSigns();
				if (count($signs) === 1 && $signs[0] === false) {
					$subs = $child->getSubqueries();
					$result->addSubquery(reset($subs), false);
					continue;
				}
			}

			$result->addSubquery($child, $required);
		}

		return $result;
	}

	private function buildTerm($node)
	{
		$value = $node->getValue($this->getTypeFactory());
		$field = $node->getField();

		switch (get_class($value)) {
		case 'Search_Type_WikiText':
		case 'Search_Type_PlainText':
		case 'Search_Type_MultivalueText':
			$whole = $value->getValue();
			$whole = str_replace(array('*', '?', '~', '+'), '', $whole);
			$whole = str_replace(array('[', ']', '{', '}', '(', ')', ':', '-'), ' ', $whole);

			$parts = explode(' ', $this->leftToRight($whole));
			if (count($parts) === 1) {
				return new ZendSearch\Lucene\Search\Query\Term(new ZendSearch\Lucene\Index\Term($parts[0], $field), true);
			} else {
				return new ZendSearch\Lucene\Search\Query\Phrase($parts, array_keys($parts), $field);
			}
		case 'Search_Type_Timestamp':
			$parts = explode(' ', $value->getValue());
			return new ZendSearch\Lucene\Search\Query\Term(new ZendSearch\Lucene\Index\Term($parts[0], $field), true);
		case 'Search_Type_Whole':
		case 'Search_Type_Numeric':
			$parts = explode(' ', $value->getValue());
			return new ZendSearch\Lucene\Search\Query\Phrase($parts, array_keys($parts), $field);
		}
	}

	private function leftToRight($string)
	{
		return $string . "\xE2\x80\x8E";
	}
}

