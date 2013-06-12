<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

class Search_Elastic_QueryBuilder
{
	private $factory;

	function __construct()
	{
		$this->factory = new Search_Elastic_TypeFactory;
	}

	function build(Search_Expr_Interface $expr)
	{
		$query = $expr->traverse($this);
		$query = array("query" => $query);

		return $query;
	}

	function __invoke($callback, $node, $childNodes)
	{
		if ($node instanceof Token) {
			return $this->handleToken($node);
		} elseif (count($childNodes) === 1 && ($node instanceof AndX || $node instanceof OrX)) {
			return reset($childNodes)->traverse($callback);
		} elseif ($node instanceof OrX) {
			$inner = array_map(
				function ($expr) use ($callback) {
					return $expr->traverse($callback);
				}, $childNodes
			);

			return array(
				'bool' => array(
					'should' => $this->flatten($inner, 'should'),
					"minimum_number_should_match" => 1,
				),
			);
		} elseif ($node instanceof AndX) {
			$inner = array_map(
				function ($expr) use ($callback) {
					return $expr->traverse($callback);
				}, $childNodes
			);
			return array(
				'bool' => array(
					'must' => $this->flatten($inner, 'must'),
				),
			);
		} elseif ($node instanceof NotX) {
			return array(
				'bool' => array(
					'must_not' => array(
						reset($childNodes)->traverse($callback),
					),
				),
			);
		} elseif ($node instanceof Initial) {
			return array(
				'prefix' => array(
					$node->getField() => array(
						"value" => $this->getTerm($node),
						"boost" => $node->getWeight(),
					),
				),
			);
		} elseif ($node instanceof Range) {
			return array(
				'range' => array(
					$node->getField() => array(
						"from" => $this->getTerm($node->getToken('from')),
						"to" => $this->getTerm($node->getToken('to')),
						"boost" => $node->getWeight(),
						"include_upper" => false,
					),
				),
			);
		}
	}

	private function flatten($list, $type)
	{
		$out = array();
		foreach ($list as $entry) {
			if (isset($entry['bool'][$type])) {
				$out = array_merge($out, $entry['bool'][$type]);
			} else {
				$out[] = $entry;
			}
		}

		return $out;
	}

	private function getTerm($node)
	{
		$value = $node->getValue($this->factory);
		return strtolower($value->getValue());
	}

	private function handleToken($node)
	{
		if ($node->getType() == 'identifier') {
			$value = $node->getValue($this->factory)->getValue();
			return array("match" => array(
				$node->getField() => array("query" => $value),
			));
		} elseif ($node->getType() == 'multivalue') {
			$value = $node->getValue($this->factory)->getValue();
			return array("match" => array(
				$node->getField() => array("query" => reset($value)),
			));
		} else {
			return array("match" => array(
				$node->getField() => array("query" => $this->getTerm($node), "boost" => $node->getWeight()),
			));
		}
	}
}

