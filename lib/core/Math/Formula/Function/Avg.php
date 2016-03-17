<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_Avg extends Math_Formula_Function
{
	function evaluate($element)
	{
		$list = array();

		foreach ($element as $child) {
			$child = $this->evaluateChild($child);

			if (is_array($child)) {
				$list = array_merge($list, $child);
			} else {
				$list[] = $child;
			}
		}

		if (empty($list)) {
			return 0;
		} else {
			return array_sum($list) / count($list);
		}
	}
}

