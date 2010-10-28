<?php

class Search_QueryTest extends PHPUnit_Framework_TestCase
{
	function testQueryGlobalText()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello');

		$query->search($index);
		
		$expr = new Search_Expr_And(array(
			new Search_Expr_Token('hello', 'wikitext', 'global'),
		));

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testCompositeQuery()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello world');

		$query->search($index);
		
		$expr = new Search_Expr_And(array(
			new Search_Expr_Or(array(
				new Search_Expr_Token('hello', 'wikitext', 'global'),
				new Search_Expr_Token('world', 'wikitext', 'global'),
			)),
		));

		$this->assertEquals($expr, $index->getLastQuery());
	}
}

