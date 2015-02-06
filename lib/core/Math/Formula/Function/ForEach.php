<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_ForEach extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$allowed = array('list', 'formula');

		if ($extra = $element->getExtraValues($allowed)) {
			$this->error(tr('Unexpected values: %0', implode(', ', $extra)));
		}

		$list = $element->list;
		if ( ! $list || count($list) != 1 ) {
			$this->error(tra('Field must be provided and contain one argument: list'));
		}
		$list = $this->evaluateChild($list[0]);

		if ( ! $element->formula || count($element->formula) != 1) {
			$this->error(tra('Field must be provided and contain a function.'));
		}
		$formula = $element->formula[0];

		$out = array();

		foreach ($list as $values) {
			$out[] = $this->evaluateChild($formula, $values);
		}

		return $out;
	}
}

