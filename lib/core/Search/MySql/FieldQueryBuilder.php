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

class Search_MySql_FieldQueryBuilder
{
	function build(Search_Expr_Interface $expr, Search_Type_Factory_Interface $factory)
	{
		$string = $expr->walk(function ($node, $childNodes) use ($factory) {
			if ($node instanceof Token) {
				$string = $node->getValue($factory)->getValue();
				if (false === strpos($string, ' ')) {
					return $string;
				} else {
					return '"' . $string . '"';
				}
			} elseif ($node instanceof OrX) {
				return (count($childNodes) == 1)
					? reset($childNodes)
					: '(' . implode(' ', $childNodes) . ')';
			} elseif ($node instanceof AndX) {
				return (count($childNodes) == 1)
					? reset($childNodes)
					: '(+' . implode(' +', $childNodes) . ')';
			} elseif ($node instanceof NotX) {
				return '-' . reset($childNodes);
			} else {
				throw new Search_MySql_QueryException('Expression not supported: ' . get_class($node));
			}
		});

		$string = str_replace('+-', '-', $string);
		return $string;
	}
}

