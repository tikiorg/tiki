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

	function testFilterType()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello');
		$query->filterType('wiki page');

		$query->search($index);
		
		$expr = new Search_Expr_And(array(
			new Search_Expr_Token('hello', 'wikitext', 'global'),
			new Search_Expr_Token('wiki page', 'identifier', 'object_type'),
		));

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testFilterCategory()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterCategory('1 and 2');

		$query->search($index);

		$expr = new Search_Expr_And(array(
			$expr = new Search_Expr_And(array(
				new Search_Expr_Token('1', 'multivalue', 'categories'),
				new Search_Expr_Token('2', 'multivalue', 'categories'),
			)),
		));

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testFilterLanguage()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterLanguage('en or fr');

		$query->search($index);

		$expr = new Search_Expr_And(array(
			$expr = new Search_Expr_Or(array(
				new Search_Expr_Token('en', 'identifier', 'language'),
				new Search_Expr_Token('fr', 'identifier', 'language'),
			)),
		));

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testDefaultSearchOrder()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;

		$query->search($index);

		$this->assertEquals(Search_Query_Order::searchResult(), $index->getLastOrder());
	}

	function testSpecifiedOrder()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;

		$query->setOrder(Search_Query_Order::recentChanges());

		$query->search($index);

		$this->assertEquals(Search_Query_Order::recentChanges(), $index->getLastOrder());
	}

	function testOrderFromString()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;

		$query->setOrder('title_asc');

		$query->search($index);

		$this->assertEquals(new Search_Query_Order('title', 'text', 'asc'), $index->getLastOrder());
	}
}

