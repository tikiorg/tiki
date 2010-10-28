<?php

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
		$tokens = $this->reduceParenthesis($tokens);
		$tokens = $this->applyOperator($tokens, 'NOT', 'buildNot');
		$tokens = $this->applyOperator($tokens, 'OR', 'buildOr');
		$tokens = $this->applyOperator($tokens, 'AND', 'buildAnd');
		$tokens = $this->applyOperator($tokens, '+', 'buildAnd');

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
			} elseif($openCount === 0) {
				$out[] = $token;
			}
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
		$tokens[$key] = new Search_Expr_Or(array($tokens[$key - 1], $tokens[$key + 1]));
		$tokens[$key - 1] = null;
		$tokens[$key + 1] = null;
	}

	private function buildAnd(&$tokens, $key)
	{
		$tokens[$key] = new Search_Expr_And(array($tokens[$key - 1], $tokens[$key + 1]));
		$tokens[$key - 1] = null;
		$tokens[$key + 1] = null;
	}

	private function buildNot(&$tokens, $key)
	{
		$tokens[$key] = new Search_Expr_Not($tokens[$key + 1]);
		$tokens[$key + 1] = null;
	}
}

