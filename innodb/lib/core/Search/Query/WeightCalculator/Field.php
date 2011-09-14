<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Query_WeightCalculator_Field implements Search_Query_WeightCalculator_Interface
{
	private $map;

	function __construct(array $weightMap)
	{
		$this->map = array_map('floatval', $weightMap);
	}

	function calculate(Search_Expr_Interface $expr)
	{
		if (method_exists($expr, 'getField')) {
			$field = $expr->getField();

			if (isset ($this->map[$field])) {
				$expr->setWeight($this->map[$field]);
				return;
			}
		}

		$expr->setWeight(1.0);
	}
}

