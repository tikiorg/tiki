<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_Coalesce extends Math_Formula_Function
{
	function evaluate( $element )
	{
		foreach ( $element as $child ) {
			$value = $this->evaluateChild($child);

			if (! empty($value)) {
				return $value;
			}
		}

		return 0;
	}
}

