<?php

class Search_Expr_Tokenizer
{
	const QUOTE = '"';
	const OPEN = '(';
	const CLOSE = ')';

	function tokenize($string)
	{
		$tokens = array();
		$open = false;
		$current = '';

		$length = strlen($string);
		for ($i = 0; $length > $i; ++$i ) {
			$char = $string{$i};

			if ($open ) {
				if ($char === self::QUOTE) {
					$this->addToken($tokens, $current);
					$open = false;
				} else {
					$current .= $char;
				}
			} else {
				if ($char === self::QUOTE) {
					$open = true;
				} elseif ($char === self::OPEN || $char === self::CLOSE) {
					$this->addToken($tokens, $current);
					$this->addToken($tokens, $char);
				} elseif (ctype_space($char)) {
					$this->addToken($tokens, $current);
				} else {
					$current .= $char;
				}
			}
		}

		$this->addToken($tokens, $current);

		return $tokens;
	}

	private function addToken(&$tokens, &$current)
	{
		if (! empty($current) ) {
			$tokens[] = $current;
			$current = '';
		}
	}
}

