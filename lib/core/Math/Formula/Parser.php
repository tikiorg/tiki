<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Parser
{
	function parse( $string )
	{
		$tokenizer = new Math_Formula_Tokenizer;
		$tokens = $tokenizer->getTokens($string);

		$element = $this->getElement($tokens);

		if ( ! empty($tokens) ) {
			throw new Math_Formula_Parser_Exception('Unexpected trailing characters.', $tokens);
		}

		return $element;
	}

	private function getElement( & $tokens )
	{
		$first = array_shift($tokens);

		if ( $first != '(' ) {
			array_unshift($tokens, $first);
			throw new Math_Formula_Parser_Exception(tra('Expecting "("'), $tokens);
		}

		$type = array_shift($tokens);

		if ( $type == '(' || $type == ')' ) {
			array_unshift($tokens, $type);
			throw new Math_Formula_Parser_Exception(tr('Unexpected "%0"', $type), $tokens);
		}

		$element = new Math_Formula_Element($type);

		while ( strlen($token = array_shift($tokens)) != 0 && $token != ')') {
			if ( $token == '(' ) {
				array_unshift($tokens, $token);
				$token = $this->getElement($tokens);

				if ( $token->getType() == 'comment' ) {
					continue;
				}
			}

			if ( $token{0} === '"' ) {
				$element->addChild(new Math_Formula_InternalString($token));
			} else {
				$element->addChild($token);
			}
		}

		if ( $token != ')' ) {
			array_unshift($tokens, $token);
			throw new Math_Formula_Parser_Exception(tra('Expecting ")"'), $tokens);
		}

		return $element;
	}

}
