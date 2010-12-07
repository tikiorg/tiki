<?php

class Search_Index_Lucene implements Search_Index_Interface
{
	private $lucene;

	function __construct($directory, $lang = 'en')
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
	}

	function addDocument(array $data)
	{
		$document = $this->generateDocument($data);

		$this->lucene->addDocument($document);
	}

	function invalidateMultiple(array $objectList)
	{
		$hits = array();

		foreach ($objectList as $object) {
			$expr = new Search_Expr_And(array(
				new Search_Expr_Token($object['object_type'], 'identifier', 'object_type'),
				new Search_Expr_Token($object['object_id'], 'identifier', 'object_id'),
			));

			$query = $this->buildQuery($expr);
			foreach ($this->lucene->find($query) as $hit) {
				$hits[] = $hit;
			}
		}

		foreach ($hits as $hit) {
			$this->lucene->delete($hit->id);
		}
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount)
	{
		$query = $this->buildQuery($query);

		$hits = $this->lucene->find($query, $this->getSortField($sortOrder), $this->getSortType($sortOrder), $this->getSortOrder($sortOrder));
		$result = array();

		foreach ($hits as $key => $hit) {
			if ($key >= $resultStart) {
				$result[] = $this->extractValues($hit->getDocument());

				if (count($result) == $resultCount) {
					break;
				}
			}
		}

		$resultSet = new Search_ResultSet($result, count($hits), $resultStart, $resultCount);

		$resultSet->setHighlightHelper(new Search_Index_Lucene_HighlightHelper($query));

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
			'Search_Type_MultivalueText' => 'UnStored',
			'Search_Type_ShortText' => 'Text',
		);
		$fieldBoost = array(
			'objectId' => 5,
			'title' => 3,
			'description' => 2,
		);
		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$field = Zend_Search_Lucene_Field::$luceneType($key, $value->getValue());
			if (!empty($fieldBoost[$key])) {
				$field->boost = $fieldBoost[$key];
			}
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
		if ($node instanceof Search_Expr_And) {
			return $this->buildCondition($childNodes, true);
		} elseif ($node instanceof Search_Expr_Or) {
			return $this->buildCondition($childNodes, null);
		} elseif ($node instanceof Search_Expr_Not) {
			$result = new Zend_Search_Lucene_Search_Query_Boolean;
			$result->addSubquery($childNodes[0], false);

			return $result;
		} elseif ($node instanceof Search_Expr_Range) {
			$from = $node->getToken('from');
			$to = $node->getToken('to');
			$range = new Zend_Search_Lucene_Search_Query_Range(
				$this->buildTerm($from)->getTerm(),
				$this->buildTerm($to)->getTerm(),
				true // inclusive
			);

			return $range;
		} elseif ($node instanceof Search_Expr_Token) {
			return $this->buildTerm($node);
		}
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

		if ($field === 'global') {
			$field = null;
		}

		switch (get_class($value)) {
		case 'Search_Type_Whole':
		case 'Search_Type_WikiText':
		case 'Search_Type_MultivalueText':
			$parts = explode(' ', $value->getValue());
			if (count($parts) === 1) {
				return new Zend_Search_Lucene_Search_Query_Term(new Zend_Search_Lucene_Index_Term($parts[0], $field), true);
			} else {
				return new Zend_Search_Lucene_Search_Query_Phrase($parts, array_keys($parts), $field);
			}
			break;
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
		return trim(strip_tags($this->query->highlightMatches($content), '<b>'));
	}
}

