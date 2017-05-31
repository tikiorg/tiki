<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_StrReplace extends Math_Formula_Function
{
	function evaluate( $args )
	{
		$elements = array();

		if (count($args) != 3) {
			$this->error(tr('Str-replace needs exactly 3 arguments: search, replace and subject.'));
		}

		foreach ( $args as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		return str_replace($elements[0], $elements[1], $elements[2]);
	}
}
