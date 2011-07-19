<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Tokenizer
{
	function getTokens( $string ) {
		$tokens = array();

		$len = strlen( $string );
		$current = '';
		for( $i = 0; $len > $i; ++$i ) {
			$chr = $string{$i};

			$end = false;
			$extra = null;

			if( ctype_space( $chr ) ) {
				$end = true;
			} elseif( $chr == '(' || $chr == ')' ) {
				$extra = $chr;
				$end = true;
			} else {
				$current .= $chr;
			}

			if( $end && 0 != strlen( $current ) ) {
				$tokens[] = $current;
				$current = '';
			}

			if( $extra ) {
				$tokens[] = $extra;
			}
		}

		if( strlen( $current ) != 0 ) {
			$tokens[] = $current;
		}

		return $tokens;
	}
}

