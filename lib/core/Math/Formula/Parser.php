<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Math_Formula_Parser
{
	function parse( $string ) {
		require_once 'Math/Formula/Tokenizer.php';
		$tokenizer = new Math_Formula_Tokenizer;
		$tokens = $tokenizer->getTokens( $string );

		$element = $this->getElement( $tokens );

		if( ! empty( $tokens ) ) {
			require_once 'Math/Formula/Parser/Exception.php';
			throw new Math_Formula_Parser_Exception( 'Unexpected trailing characters.', $tokens );
		}

		return $element;
	}

	private function getElement( & $tokens ) {

		$first = array_shift( $tokens );

		if( $first != '(' ) {
			array_unshift( $tokens, $first );
			require_once 'Math/Formula/Parser/Exception.php';
			throw new Math_Formula_Parser_Exception( tra('Expecting "("'), $tokens );
		}

		$type = array_shift( $tokens );

		if( $type == '(' || $type == ')' ) {
			array_unshift( $tokens, $type );
			require_once 'Math/Formula/Parser/Exception.php';
			throw new Math_Formula_Parser_Exception( tr('Unexpected "%0"', $type), $tokens );
		}

		require_once 'Math/Formula/Element.php';
		$element = new Math_Formula_Element( $type );

		while( strlen( $token = array_shift( $tokens ) ) != 0 && $token != ')' ) {
			if( $token == '(' ) {
				array_unshift( $tokens, $token );
				$token = $this->getElement( $tokens );

				if( $token->getType() == 'comment' ) {
					continue;
				}
			}

			$element->addChild( $token );
		}

		if( $token != ')' ) {
			array_unshift( $tokens, $token );
			require_once 'Math/Formula/Parser/Exception.php';
			throw new Math_Formula_Parser_Exception( tra('Expecting ")"'), $tokens );
		}

		return $element;
	}

}

