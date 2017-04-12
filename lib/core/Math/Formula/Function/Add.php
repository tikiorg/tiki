<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_Add extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$out = 0;

		foreach ( $element as $child ) {
			$out += $this->evaluateChild($child);
		}

		return $out;
	}
}

