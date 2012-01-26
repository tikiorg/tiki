<?php

class Search_Expr_TokenizerTest extends PHPUnit_Framework_TestCase
{
	private $tokenizer;

	function setUp()
	{
		$this->tokenizer = new Search_Expr_Tokenizer;
	}

	function testSingleWord()
	{
		$this->assertEquals(array('hello'), $this->tokenizer->tokenize('hello'));
	}

	function testMultipleWords()
	{
		$this->assertEquals(array('hello', 'world', 'who', 'listens'), $this->tokenizer->tokenize('hello world who listens'));
	}

	function testWithQuotedText()
	{
		$this->assertEquals(array('hello world', 'who listens'), $this->tokenizer->tokenize('"hello world" "who listens"'));
	}

	function testWithParenthesis()
	{
		$this->assertEquals(array('hello world (who?)', '(', 'who', ')', '(', 'test', 'listens', ')'), $this->tokenizer->tokenize('"hello world (who?)" (who) (test listens)'));
	}
}

