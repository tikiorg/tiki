<?php

class Search_Index_Lucene implements Search_Index_Interface
{
	private $lucene;

	function __construct($directory)
	{
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

	function find(Search_Expr_Interface $query)
	{
		$query = $this->buildQuery($query);

		$hits = $this->lucene->find((string)$query);
		$result = array();

		foreach ($hits as $hit) {
			$result[] = array(
				'object_type' => $hit->object_type,
				'object_id' => $hit->object_id,
			);
		}

		return $result;
	}

	function getTypeFactory()
	{
		return new Search_Type_Factory_Lucene;
	}

	private function generateDocument($data)
	{
		$document = new Zend_Search_Lucene_Document;
		$typeMap = array(
			'Search_Type_WikiText' => 'Text',
			'Search_Type_Whole' => 'Keyword',
			'Search_Type_MultivalueText' => 'UnStored',
		);

		foreach ($data as $key => $value) {
			$luceneType = $typeMap[get_class($value)];
			$document->addField(Zend_Search_Lucene_Field::$luceneType($key, $value->getValue()));
		}

		return $document;
	}

	private function buildQuery($expr)
	{
		return $expr->walk(array($this, 'walkCallback'));
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

