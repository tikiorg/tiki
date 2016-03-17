<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Function_Pad extends Math_Formula_Function
{
	function evaluate( $element )
	{
		$elements = array();
		$help = ' ' . tra('string $input , int $pad_length [, string $pad_string = " " [, string $pad_type = right|left|both ]]');
		// see http://php.net/manual/en/function.str-pad.php for more info

		if (count($element) > 4) {
			$this->error(tr('Too many arguments for pad.') . $help);
		}

		if (count($element) < 2) {
			$this->error(tr('Too few arguments for pad.') . $help);
		}

		foreach ( $element as $child ) {
			$elements[] = $this->evaluateChild($child);
		}

		$input = array_shift($elements);
		$pad_length = intval(array_shift($elements));
		$pad_string = array_shift($elements);
		if ($pad_string === null) {
			$pad_string = ' ';
		}
		$pad_type = array_shift($elements);
		switch ($pad_type) {
			case 'left':
				$pad_type = STR_PAD_LEFT;
				break;
			case 'both':
				$pad_type = STR_PAD_BOTH;
				break;
			default:
				$pad_type = STR_PAD_RIGHT;
				break;
		}

		return str_pad($input, $pad_length, $pad_string, $pad_type);

	}
}

