<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;

class Search_Elastic_QueryBuilder
{
	function build(Search_Expr_Interface $expr) {
		$factory = new Search_Type_Factory_Direct;
		$getTerm = function ($node) use ($factory) {
			$value = $node->getValue($factory);
			return strtolower($value->getValue());
		};

		$query = $expr->traverse(function ($callback, $node, $childNodes) use ($getTerm) {
			if ($node instanceof Token) {
				return array("term" => array(
					$node->getField() => array("value" => $getTerm($node), "boost" => $node->getWeight()),
				));
			} elseif (count($childNodes) === 1 && ($node instanceof AndX || $node instanceof OrX)) {
				return reset($childNodes)->traverse($callback);
			} elseif ($node instanceof OrX) {
				return array(
					'bool' => array(
						'should' => array_map(function ($expr) use ($callback) {
							return $expr->traverse($callback);
						}, $childNodes),
						"minimum_number_should_match" => 1,
					),
				);
			}
		});

		$query = array("query" => $query);

		return $query;
	}
}

