<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Sub.php 45445 2013-04-11 20:54:04Z lphuberdeau $

class Math_Formula_Function_Sub extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$elements = array();

		foreach ( $element as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		$out = array_shift($elements);

		foreach ($elements as $element) {
			$out -= $element;
		}

		return $out;
	}
}

