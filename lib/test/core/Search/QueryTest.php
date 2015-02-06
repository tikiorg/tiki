<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_QueryTest extends PHPUnit_Framework_TestCase
{
	function testQueryGlobalText()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Token('hello', 'plaintext', 'contents'),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array('hello'), $query->getTerms());
	}

	function testCompositeQuery()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello world');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_ImplicitPhrase(
					array(
						new Search_Expr_Token('hello', 'plaintext', 'contents'),
						new Search_Expr_Token('world', 'plaintext', 'contents'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array('hello', 'world'), $query->getTerms());
	}

	function testFilterType()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query('hello');
		$query->filterType('wiki page');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Token('hello', 'plaintext', 'contents'),
				new Search_Expr_Token('wiki page', 'identifier', 'object_type'),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array('hello'), $query->getTerms());
	}

	function testFilterCategory()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterCategory('1 and 2');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_And(
					array(
						new Search_Expr_Token('1', 'multivalue', 'categories'),
						new Search_Expr_Token('2', 'multivalue', 'categories'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array(), $query->getTerms());
	}

	function testDeepFilterCategory()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterCategory('1 and 2', true);

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_And(
					array(
						new Search_Expr_Token('1', 'multivalue', 'deep_categories'),
						new Search_Expr_Token('2', 'multivalue', 'deep_categories'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testFilterLanguage()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterLanguage('en or fr');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('en', 'identifier', 'language'),
						new Search_Expr_Token('fr', 'identifier', 'language'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array(), $query->getTerms());
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

	function testFilterBasedOnPermissions()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterPermissions(array('Registered', 'Editor', 'Project Lead ABC'));

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('Registered', 'multivalue', 'allowed_groups'),
						new Search_Expr_Token('Editor', 'multivalue', 'allowed_groups'),
						new Search_Expr_Token('Project Lead ABC', 'multivalue', 'allowed_groups'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testDefaultPagination()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;

		$query->search($index);

		$this->assertEquals(0, $index->getLastStart());
		$this->assertEquals(50, $index->getLastCount());
	}

	function testSpecifiedPaginationRange()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->setRange(60, 30);

		$query->search($index);

		$this->assertEquals(60, $index->getLastStart());
		$this->assertEquals(30, $index->getLastCount());
	}

	function testWithQueryRange()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterRange(1000, 2000);

		$query->search($index);

		$expr = new Search_Expr_And(
			array(new Search_Expr_Range(1000, 2000, 'timestamp', 'modification_date'))
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testFilterTags()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterTags('1 and 2');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_And(
					array(
						new Search_Expr_Token('1', 'multivalue', 'freetags'),
						new Search_Expr_Token('2', 'multivalue', 'freetags'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testFilterContentSpanMultipleFields()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->filterContent('hello world', array('contents', 'title'));

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Or(
					array(
						new Search_Expr_ImplicitPhrase(
							array(
								new Search_Expr_Token('hello', 'plaintext', 'contents'),
								new Search_Expr_Token('world', 'plaintext', 'contents'),
							)
						),
						new Search_Expr_ImplicitPhrase(
							array(
								new Search_Expr_Token('hello', 'plaintext', 'title'),
								new Search_Expr_Token('world', 'plaintext', 'title'),
							)
						),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testApplyWeight()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->setWeightCalculator(
			new Search_Query_WeightCalculator_Field(
				array(
					'title' => 5.5,
					'allowed_groups' => 0.0001,
				)
			)
		);
		$query->filterContent('hello', array('contents', 'title'));
		$query->filterPermissions(array('Anonymous'));

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('hello', 'plaintext', 'contents', 1.0),
						new Search_Expr_Token('hello', 'plaintext', 'title', 5.5),
					)
				),
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('Anonymous', 'multivalue', 'allowed_groups', 0.0001),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
	}

	function testEmptySubQueryIsMainQuery()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->getSubQuery(null)
			->filterContent('hello');

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Token('hello', 'plaintext', 'contents'),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array('hello'), $query->getTerms());
	}

	function testSubQueryCreatesOrStatement()
	{
		$index = new Search_Index_Memory;
		$query = new Search_Query;
		$query->getSubQuery('abc')
			->filterContent('hello');
		$query->getSubQuery('abc')
			->filterCategory('1 and 2');
		$query->filterPermissions(array('Registered'));

		$query->search($index);

		$expr = new Search_Expr_And(
			array(
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('hello', 'plaintext', 'contents'),
						new Search_Expr_And(
							array(
								new Search_Expr_Token('1', 'multivalue', 'categories'),
								new Search_Expr_Token('2', 'multivalue', 'categories'),
							)
						),
					)
				),
				new Search_Expr_Or(
					array(
						new Search_Expr_Token('Registered', 'multivalue', 'allowed_groups'),
					)
				),
			)
		);

		$this->assertEquals($expr, $index->getLastQuery());
		$this->assertEquals(array('hello'), $query->getTerms());
	}

	function testQueryCloning()
	{
		$query = new Search_Query('Hello World');
		$clone = clone $query;

		$query->filterCategory('1 OR 2');

		$this->assertNotEquals($query, $clone);
	}
}

