<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_IsEmpty extends Math_Formula_Function
{
	function evaluate( $element )
	{
		// Multiple components will all need to be equal.
		$out = array();

		foreach ( $element as $child ) {
			$component = $this->evaluateChild($child);
			if (!empty($component)) {
				return false;
			}
		}

		return true;
	}
}

