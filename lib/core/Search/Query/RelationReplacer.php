<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_RelationReplacer
{
	private $invertable;

	function __construct(array $invertable)
	{
		$this->invertable = $invertable;
	}

	function visit(Search_Expr_Interface $expr, $results)
	{
		if ($expr instanceof Search_Expr_Token) {
			$relation = Search_Query_Relation::fromToken($expr);

			if (in_array($relation->getQualifier(), $this->invertable)) {
				$invert = $relation->getInvert();

				return new Search_Expr_Or(
					array(
						$expr,
						new Search_Expr_Token($invert->getToken()),
					)
				);
			}
		}

		if ($expr instanceof Search_Expr_Or || $expr instanceof Search_Expr_And) {
			$class = get_class($expr);
			return new $class($results);
		} elseif ($expr instanceof Search_Expr_Not) {
			return new Search_Expr_Not($results[0]);
		}

		return $expr;
	}
}

