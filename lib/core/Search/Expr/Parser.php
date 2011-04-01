<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Expr_Parser
{
	private $special = array('(', ')', 'AND', 'OR', 'NOT', '+');

	function parse($string)
	{
		$tokenizer = new Search_Expr_Tokenizer;

		$tokens = array();
		foreach ($tokenizer->tokenize($string) as $part) {
			if (in_array(strtoupper($part), $this->special)) {
				$tokens[] = strtoupper($part);
			} else {
				$tokens[] = new Search_Expr_Token($part);
			}
		}

		return $this->reduce($tokens);
	}

	private function reduce($tokens)
	{
		$tokens = $this->filterExcessiveKeywords($tokens);
		$tokens = $this->reduceParenthesis($tokens);
		$tokens = $this->applyOperator($tokens, 'NOT', 'buildNot');
		$tokens = $this->applyOperator($tokens, 'OR', 'buildOr');
		$tokens = $this->applyOperator($tokens, 'AND', 'buildAnd');
		$tokens = $this->applyOperator($tokens, '+', 'buildAnd');
		$tokens = array_values($tokens);

		if (count($tokens) === 1) {
			return reset($tokens);
		} else {
			return new Search_Expr_Or($tokens);
		}
	}

	private function reduceParenthesis($tokens)
	{
		$out = array();
		$firstOpen = null;
		$openCount = 0;

		foreach ($tokens as $key => $token) {
			if ($token === '(') {
				if ($openCount === 0) {
					$firstOpen = $key;
				}

				++$openCount;
			} elseif ($token === ')') {
				--$openCount;

				if($openCount === 0) {
					$inner = array_slice($tokens, $firstOpen + 1, $key - $firstOpen - 1);
					$out[] = $this->reduce($inner);
					$firstOpen = null;
				}

				// Skip extra closing parenthesis and restore state
				$openCount = max(0, $openCount);
			} elseif($openCount === 0) {
				$out[] = $token;
			}
		}

		// Handle a missing final parenthesis by reducing everything until the end
		if ($firstOpen) {
			$inner = array_slice($tokens, $firstOpen + 1);
			$out[] = $this->reduce($inner);
		}

		return $out;
	}

	private function applyOperator($tokens, $lookingFor, $buildMethod)
	{
		$tokens = array_values($tokens);
		$positions = array();
		foreach ($tokens as $key => $token) {
			if ($lookingFor === $token) {
				$positions[] = $key;
			}
		}

		foreach ($positions as $key) {
			$this->$buildMethod($tokens, $key);
		}

		return array_filter($tokens);
	}

	private function buildOr(&$tokens, $key)
	{
		$previous = $key - 1;
		$next = $key + 1;

		while (!$tokens[$previous]) {
			$previous--;
		}

		$tokens[$key] = new Search_Expr_Or(array($tokens[$previous], $tokens[$next]));
		$tokens[$previous] = null;
		$tokens[$next] = null;
	}

	private function buildAnd(&$tokens, $key)
	{
		$previous = $key - 1;
		$next = $key + 1;

		while (!$tokens[$previous]) {
			$previous--;
		}

		$tokens[$key] = new Search_Expr_And(array($tokens[$previous], $tokens[$next]));
		$tokens[$previous] = null;
		$tokens[$next] = null;
	}

	private function buildNot(&$tokens, $key)
	{
		$tokens[$key] = new Search_Expr_Not($tokens[$key + 1]);
		$tokens[$key + 1] = null;
	}

	private function filterExcessiveKeywords($tokens)
	{
		$out = array();
		$skip = true;
		foreach ($tokens as $token) {
			if (is_string($token) && in_array($token, array('AND', 'OR', '+'))) {
				if (! $skip) {
					$out[] = $token;
					$skip = true;
				}
			} else {
				$skip = false;
				$out[] = $token;
			}
		}

		return $out;
	}
}

