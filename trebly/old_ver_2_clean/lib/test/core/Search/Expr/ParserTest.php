<?php

class Search_Expr_ParserTest extends PHPUnit_Framework_TestCase
{
	private $parser;

	function setUp()
	{
		$this->parser = new Search_Expr_Parser;
	}

	function testSimpleWord()
	{
		$result = $this->parser->parse('hello');

		$this->assertEquals($result, new Search_Expr_Token('hello'));
	}

	function testMultipleWords()
	{
		$result = $this->parser->parse('"hello world" test again');
		$this->assertEquals(new Search_Expr_Or(array(
			new Search_Expr_Token('hello world'),
			new Search_Expr_Token('test'),
			new Search_Expr_Token('again'),
		)), $result);
	}

	function testSimpleParenthesis()
	{
		$result = $this->parser->parse('(test again)');
		$this->assertEquals(new Search_Expr_Or(array(
			new Search_Expr_Token('test'),
			new Search_Expr_Token('again'),
		)), $result);
	}

	function testMatchParenthesis()
	{
		$result = $this->parser->parse('(hello (bob roger)) (test again)');
		$this->assertEquals(new Search_Expr_Or(array(
			new Search_Expr_Or(array(
				new Search_Expr_Token('hello'),
				new Search_Expr_Or(array(
					new Search_Expr_Token('bob'),
					new Search_Expr_Token('roger'),
				)),
			)),
			new Search_Expr_Or(array(
				new Search_Expr_Token('test'),
				new Search_Expr_Token('again'),
			)),
		)), $result);
	}

	function testStripOr()
	{
		$result = $this->parser->parse('(bob roger) or (test again)');

		$this->assertEquals(new Search_Expr_Or(array(
			$this->parser->parse('bob roger'),
			$this->parser->parse('test again'),
		)), $result);
	}

	function testRecongnizeAnd()
	{
		$result = $this->parser->parse('(bob roger) and (test again)');

		$this->assertEquals(new Search_Expr_And(array(
			$this->parser->parse('bob roger'),
			$this->parser->parse('test again'),
		)), $result);
	}

	function testSequence()
	{
		$result = $this->parser->parse('1 and 2 and 3');

		$this->assertEquals(new Search_Expr_And(array(
			new Search_Expr_And(array(
				$this->parser->parse('1'),
				$this->parser->parse('2'),
			)),
			$this->parser->parse('3'),
		)), $result);
	}

	function testSequenceWithOr()
	{
		$result = $this->parser->parse('1 or 2 or 3');

		$this->assertEquals(new Search_Expr_Or(array(
			new Search_Expr_Or(array(
				$this->parser->parse('1'),
				$this->parser->parse('2'),
			)),
			$this->parser->parse('3'),
		)), $result);
	}

	function testRecongnizePlus()
	{
		$result = $this->parser->parse('(bob roger) + (test again)');

		$this->assertEquals(new Search_Expr_And(array(
			$this->parser->parse('bob roger'),
			$this->parser->parse('test again'),
		)), $result);
	}

	function testCheckPriority()
	{
		$result = $this->parser->parse('bob AND test OR again');

		$this->assertEquals(new Search_Expr_And(array(
			$this->parser->parse('bob'),
			$this->parser->parse('test again'),
		)), $result);
	}

	function testCheckLowerSpacePriority()
	{
		$result = $this->parser->parse('bob AND test again');

		$this->assertEquals(new Search_Expr_Or(array(
			$this->parser->parse('bob AND test'),
			$this->parser->parse('again'),
		)), $result);
	}

	function testNotOperator()
	{
		$result = $this->parser->parse('bob AND NOT (roger alphonse)');

		$this->assertEquals(new Search_Expr_And(array(
			$this->parser->parse('bob'),
			new Search_Expr_Not($this->parser->parse('roger OR alphonse')),
		)), $result);
	}

	function testDoubleParenthesisClose()
	{
		$result = $this->parser->parse('hello (test) foo) bar');

		$this->assertEquals($this->parser->parse('hello (test) foo bar'), $result);
	}

	function testMissingClose()
	{
		$result = $this->parser->parse('hello (test foo bar');

		$this->assertEquals($this->parser->parse('hello (test foo bar)'), $result);
	}

	function testConsecutiveKeywords()
	{
		$result = $this->parser->parse('hello and and or + or world');

		$this->assertEquals($this->parser->parse('hello and world'), $result);
	}
}

