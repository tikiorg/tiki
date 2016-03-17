<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;
use Search_Expr_Not as NotX;
use Search_Expr_Range as Range;
use Search_Expr_Initial as Initial;
use Search_Expr_MoreLikeThis as MoreLikeThis;
use Search_Expr_ImplicitPhrase as ImplicitPhrase;

class Search_MySql_QueryBuilder
{
	private $db;
	private $factory;
	private $fieldBuilder;
	private $indexes = array();

	function __construct($db)
	{
		$this->db = $db;
		$this->factory = new Search_MySql_TypeFactory;
		$this->fieldBuilder = new Search_MySql_FieldQueryBuilder;
	}

	function build(Search_Expr_Interface $expr)
	{
		$this->indexes = array();
		$query = $expr->walk($this);

		return $query;
	}

	function getRequiredIndexes()
	{
		return array_values($this->indexes);
	}

	function __invoke($node, $childNodes)
	{
		$exception = null;

		if ($node instanceof ImplicitPhrase) {
			$node = $node->getBasicOperator();
		}

		$fields = $this->getFields($node);

		try {
			if (! $node instanceof NotX && count($fields) == 1 && $this->isFullText($node)) {
				// $query contains the token string to compare against $fields[0] in the unified search table
				// $fields[0] can be i.e  'allowed_users', 'allowed_groups'
				$query = $this->fieldBuilder->build($node, $this->factory);
				$str = $this->db->qstr($query);
				$this->requireIndex($fields[0], 'fulltext', $node->getWeight());
				$type = $this->fieldBuilder->isInverted()
					? 'NOT MATCH'
					: 'MATCH';
				return "$type (`{$fields[0]}`) AGAINST ($str IN BOOLEAN MODE)";
			}
		} catch (Search_MySql_QueryException $e) {
			// Try to build the query with the SQL logic when fulltext is not an option
			$exception = $e;
		}

		if (count($childNodes) === 0 && ($node instanceof AndX || $node instanceof OrX)) {
			return '';
		} elseif (count($childNodes) === 1 && ($node instanceof AndX || $node instanceof OrX)) {
			return reset($childNodes);
		} elseif ($node instanceof OrX) {
			return '(' . implode(' OR ', array_filter($childNodes)) . ')';
		} elseif ($node instanceof AndX) {
			return '(' . implode(' AND ', array_filter($childNodes)) . ')';
		} elseif ($node instanceof NotX) {
			return 'NOT (' . reset($childNodes) . ')';
		} elseif ($node instanceof Token) {
			$value = $this->getQuoted($node);
			$this->requireIndex($node->getField(), 'index', $node->getWeight());
			return "`{$node->getField()}` = $value";
		} elseif ($node instanceof Initial) {
			$value = $this->getQuoted($node, '%');
			$this->requireIndex($node->getField(), 'index', $node->getWeight());
			return "`{$node->getField()}` LIKE $value";
		} elseif ($node instanceof Range) {
			$from = $this->getQuoted($node->getToken('from'));
			$to = $this->getQuoted($node->getToken('to'));
			$this->requireIndex($node->getField(), 'index', $node->getWeight());
			return "`{$node->getField()}` BETWEEN $from AND $to";
		} else {
			// Throw initial exception if fallback fails
			throw $exception ?: new Exception(tr('Feature not supported: %0', get_class($node)));
		}
	}

	private function requireIndex($field, $type, $weight = 1.0)
	{
		$this->indexes[$field . $type] = array('field' => $field, 'type' => $type, 'weight' => $weight);
	}

	private function getFields($node)
	{
		$fields = array();
		$node->walk(
			function ($node) use (& $fields) {
				if (method_exists($node, 'getField')) {
					$fields[$node->getField()] = true;
				}
			}
		);

		return array_keys($fields);
	}

	private function isFullText($node)
	{
		$fullText = true;
		$node->walk(
			function ($node) use (& $fullText) {
				if ($fullText && method_exists($node, 'getType')) {
					$type = $node->getType();
					if ($type != 'sortable' && $type != 'wikitext' && $type != 'plaintext' && $type != 'multivalue') {
						$fullText = false;
					}
				}
			}
		);

		return $fullText;
	}

	private function getQuoted($node, $suffix = '')
	{
		$string = $node->getValue($this->factory)->getValue();
		return $this->db->qstr($string . $suffix);
	}
}

