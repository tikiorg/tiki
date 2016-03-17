<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
use Search_MySql_QueryBuilder as QueryBuilder;
use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;
use Search_Expr_Not as NotX;
use Search_Expr_Range as Range;
use Search_Expr_Initial as Initial;
use Search_Expr_MoreLikeThis as MoreLikeThis;

class Search_MySql_QueryBuilderTest extends PHPUnit_Framework_TestCase
{
	private $builder;

	function setUp()
	{
		$this->builder = new QueryBuilder(TikiDb::get());
	}

	function testSimpleQuery()
	{
		$expr = new Token('Hello', 'plaintext', 'contents', 1.5);

		$this->assertEquals("MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testSimplePhrase()
	{
		$expr = new Token('Hello World', 'plaintext', 'contents', 1.5);

		$this->assertEquals("MATCH (`contents`) AGAINST ('\\\"Hello World\\\"' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testQueryWithSinglePart()
	{
		$expr = new AndX(
			array(
				new Token('Hello', 'plaintext', 'contents', 1.5),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testBuildOrQuery()
	{
		$expr = new OrX(
			array(
				new Token('Hello', 'plaintext', 'contents', 1.5),
				new Token('World', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(Hello World)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testAndQuery()
	{
		$expr = new AndX(
			array(
				new Token('Hello', 'plaintext', 'contents', 1.5),
				new Token('World', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(+Hello +World)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testNotBuild()
	{
		$expr = new NotX(
			new Token('Hello', 'plaintext', 'contents', 1.5)
		);

		$this->assertEquals("NOT (MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE))", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testFlattenNot()
	{
		$expr = new AndX(
			array(
				new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
				new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
				new Token('Test', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(-Hello -World +Test)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testBuildOrQueryDifferentField()
	{
		$expr = new OrX(
			array(
				new Token('Hello', 'plaintext', 'foobar', 1.5),
				new Token('World', 'plaintext', 'baz', 1.0),
			)
		);

		$this->assertEquals("(MATCH (`foobar`) AGAINST ('Hello' IN BOOLEAN MODE) OR MATCH (`baz`) AGAINST ('World' IN BOOLEAN MODE))", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'foobar', 'type' => 'fulltext'),
				array('field' => 'baz', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testAndQueryDifferentField()
	{
		$expr = new AndX(
			array(
				new Token('Hello', 'plaintext', 'foobar', 1.5),
				new Token('World', 'plaintext', 'baz', 1.0),
			)
		);

		$this->assertEquals("(MATCH (`foobar`) AGAINST ('Hello' IN BOOLEAN MODE) AND MATCH (`baz`) AGAINST ('World' IN BOOLEAN MODE))", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'foobar', 'type' => 'fulltext'),
				array('field' => 'baz', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testNotBuildNotIdentifier()
	{
		$expr = new NotX(
			new Token('Hello', 'identifier', 'object_id', 1.5)
		);

		$this->assertEquals("NOT (`object_id` = 'Hello')", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'object_id', 'type' => 'index'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testFlattenNotDifferentField()
	{
		$expr = new AndX(
			array(
				new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
				new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
				new Token('Test', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(-Hello -World +Test)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testFilterWithIdentifier()
	{
		$expr = new Token('Some entry', 'identifier', 'username', 1.5);

		$this->assertEquals("`username` = 'Some entry'", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'username', 'type' => 'index'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testRangeFilter()
	{
		$expr = new Range('Hello', 'World', 'plaintext', 'title', 1.5);

		$this->assertEquals("`title` BETWEEN 'Hello' AND 'World'", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'title', 'type' => 'index'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testInitialMatchFilter()
	{
		$expr = new Initial('Hello', 'plaintext', 'title', 1.5);

		$this->assertEquals("`title` LIKE 'Hello%'", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'title', 'type' => 'index'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testNestedOr()
	{
		$expr = new OrX(
			array(
				new OrX(
					array(
						new Token('Hello', 'plaintext', 'contents', 1.5),
						new Token('World', 'plaintext', 'contents', 1.0),
					)
				),
				new Token('Test', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('((Hello World) Test)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testNestedAnd()
	{
		$expr = new AndX(
			array(
				new OrX(
					array(
						new Token('Hello', 'plaintext', 'contents', 1.5),
						new Token('World', 'plaintext', 'contents', 1.0),
					)
				),
				new AndX(
					array(
						new Token('Hello', 'plaintext', 'contents', 1.5),
						new Token('World', 'plaintext', 'contents', 1.0),
					)
				),
				new Token('Test', 'plaintext', 'contents', 1.0),
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(+(Hello World) +(+Hello +World) +Test)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
					array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testInvertNotOnlyMatchStatements()
	{
		$expr = new AndX(
			array(
				new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
				new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
			)
		);

		$this->assertEquals("NOT MATCH (`contents`) AGAINST ('(Hello World)' IN BOOLEAN MODE)", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testOrNot()
	{
		$expr = new OrX(
			array(
				new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
				new Token('World', 'plaintext', 'contents', 1.5),
			)
		);

		$this->assertEquals("(NOT (MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE)) OR MATCH (`contents`) AGAINST ('World' IN BOOLEAN MODE))", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'contents', 'type' => 'fulltext'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testEmptyAnd()
	{
		$expr = new AndX(
			array()
		);

		$this->assertEquals("", $this->builder->build($expr));
		$this->assertEquals(
			array(
			), $this->builder->getRequiredIndexes($expr)
		);
	}

	function testEmptyAndPart()
	{
		$expr = new AndX(
			array(
				new Token('Hello', 'identifier', 'object_id', 1.5),
				new AndX(array()),
			)
		);

		$this->assertEquals("(`object_id` = 'Hello')", $this->builder->build($expr));
		$this->assertEquals(
			array(
				array('field' => 'object_id', 'type' => 'index'),
			), $this->builder->getRequiredIndexes($expr)
		);
	}
}

