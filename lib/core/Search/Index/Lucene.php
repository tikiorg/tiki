<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Index_Lucene implements Search_Index_Interface
{
	private $lucene;
	private $highlight = true;

	function __construct($directory, $lang = 'en', $highlight = true)
	{
		switch($lang) {
		case 'en':
		default:
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(new StandardAnalyzer_Analyzer_Standard_English());
		}
		try {
			$this->lucene = Zend_Search_Lucene::open($directory);
		} catch (Zend_Search_Lucene_Exception $e) {
			$this->lucene = Zend_Search_Lucene::create($directory);
		}

		$this->lucene->setMaxBufferedDocs(100);
		$this->lucene->setMaxMergeDocs(200);
		$this->lucene->setMergeFactor(50);

		$this->highlight = (bool) $highlight;
	}

	function addDocument(array $data)
	{
		$document = $this->generateDocument($data);

		$this->lucene->addDocument($document);
	}

	function optimize()
	{
		$this->lucene->optimize();
	}

	function invalidateMultiple(Search_Expr_Interface $expr)
	{
		$documents = array();

		$query = $this->buildQuery($expr);
		foreach ($this->lucene->find($query) as $hit) {
			$document = $hit->getDocument();
			$documents[] = array(
				'object_type' => $document->object_type,
				'object_id' => $document->object_id,
			);
			$this->lucene->delete($hit->id);
		}

		return $documents;
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount)
	{
		$query = $this->buildQuery($query);
		$query = Zend_Search_Lucene_Search_QueryParser::parse($query, 'UTF-8');

		$hits = $this->lucene->find($query, $this->getSortField($sortOrder), $this->getSortType($sortOrder), $this->getSortOrder($sortOrder));
		$result = array();

		foreach ($hits as $key => $hit) {
			if ($key >= $resultStart) {
				$result[] = array_merge($this->extractValues($hit->getDocument()), array('relevance' => round($hit->score, 2)));

				if (count($result) == $resultCount) {
					break;
				}
			}
		}

		$resultSet = new Search_ResultSet($result, count($hits), $resultStart, $resultCount);

		if ($this->highlight) {
			$resultSet->setHighlightHelper(new Search_Index_Lucene_HighlightHelper($query));
		} else {
			$resultSet->setHighlightHelper(new Search_ResultSet_SnippetHelper);
		}

		return $resultSet;
	}

	private function extractValues($document)
	{
		$data = array();
		foreach ($document->getFieldNames() as $field) {
			$data[$field] = $document->$field;
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
		return new Search_Type_Factory_Lucene;
	}

	private function generateDocument($data)
	{
		$document = new Zend_Search_Lucene_Document;
		$typeMap = array(
			'Search_Type_WikiText' => 'UnStored',
			'Search_Type_PlainText' => 'UnStored',
			'Search_Type_Whole' => 'Keyword',
			'Search_Type_Timestamp' => 'Keyword',
			'Search_Type_MultivalueText' => 'UnStored',
			'Search_Type_ShortText' => 'Text',
		);
		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$field = Zend_Search_Lucene_Field::$luceneType($key, $value->getValue(), 'UTF-8');
			$document->addField($field);
		}

		return $document;
	}

	private function buildQuery($expr)
	{
		return (string) $expr->walk(array($this, 'walkCallback'));
	}

	function walkCallback($node, $childNodes)
	{
		$term = null;

		if ($node instanceof Search_Expr_And) {
			$term = $this->buildCondition($childNodes, true);
		} elseif ($node instanceof Search_Expr_Or) {
			$term = $this->buildCondition($childNodes, null);
		} elseif ($node instanceof Search_Expr_Not) {
			$result = new Zend_Search_Lucene_Search_Query_Boolean;
			$result->addSubquery($childNodes[0], false);

			$term = $result;
		} elseif ($node instanceof Search_Expr_Range) {
			$from = $node->getToken('from');
			$to = $node->getToken('to');
			$range = new Zend_Search_Lucene_Search_Query_Range(
				$this->buildTerm($from)->getTerm(),
				$this->buildTerm($to)->getTerm(),
				true // inclusive
			);

			$term = $range;
		} elseif ($node instanceof Search_Expr_Token) {
			$term = $this->buildTerm($node);
		}

		if ($term) {
			$term->setBoost($node->getWeight());
		}

		return $term;
	}

	private function buildCondition($childNodes, $required)
	{
		$result = new Zend_Search_Lucene_Search_Query_Boolean;
		foreach ($childNodes as $child) {

			// Detect if child is a NOT, and reformulate on the fly to support the syntax
			if ($child instanceof Zend_Search_Lucene_Search_Query_Boolean) {
				$signs = $child->getSigns();
				if (count($signs) === 1 && $signs[0] === false) {
					$result->addSubquery(reset($child->getSubqueries()), false);
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
			$whole = str_replace(array('*', '?', '~', '+', '-'), '', $whole);
			$whole = str_replace(array('[', ']', '{', '}', '(', ')', ':'), '', $whole);

			$parts = explode(' ', $whole);
			if (count($parts) === 1) {
				return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($parts[0], $field), true);
			} else {
				return new Zend_Search_Lucene_Search_Query_Phrase($parts, array_keys($parts), $field);
			}
		case 'Search_Type_Timestamp':
			$parts = explode(' ', $value->getValue());
			return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($parts[0], $field), true);
		case 'Search_Type_Whole':
			$parts = explode(' ', $value->getValue());
			return new Zend_Search_Lucene_Search_Query_Phrase($parts, array_keys($parts), $field);
		}
	}
}

class Search_Index_Lucene_HighlightHelper implements Zend_Filter_Interface
{
	private $query;

	function __construct($query)
	{
		$this->query = Zend_Search_Lucene_Search_QueryParser::parse($query);
	}

	function filter($content)
	{
		$snippetHelper = new Search_ResultSet_SnippetHelper;
		$content = $snippetHelper->filter($content);
		return trim(strip_tags($this->query->highlightMatches($content), '<b>'));
	}
}

