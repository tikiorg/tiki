<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Mul.php 25325 2010-02-17 21:55:51Z lphuberdeau $

require_once 'Math/Formula/Function.php';

class Math_Formula_Function_Str extends Math_Formula_Function
{
	function evaluate( $element ) {
		$out = array();

		foreach( $element as $child ) {
			$out[] = $child;
		}

		return implode( ' ', $out );
	}
}

