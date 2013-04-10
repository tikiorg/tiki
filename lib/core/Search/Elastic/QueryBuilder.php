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
		$query = $expr->walk(function ($node, $childNodes) use ($factory) {
			if ($node instanceof Token) {
				$value = $node->getValue($factory);
				return array("term" => array(
					$node->getField() => array("value" => strtolower($value->getValue()), "boost" => $node->getWeight()),
				));
			} elseif (count($childNodes) === 1 && ($node instanceof AndX || $node instanceof OrX)) {
				return reset($childNodes);
			}
		});

		$query = array("query" => $query);

		return $query;
	}
}

