<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		$query = $this->builder->build(new Token('Hello', 'plaintext', 'contents', 1.5));

		$this->assertEquals("MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE)", $query);
	}

	function testSimplePhrase()
	{
		$query = $this->builder->build(new Token('Hello World', 'plaintext', 'contents', 1.5));

		$this->assertEquals("MATCH (`contents`) AGAINST ('\\\"Hello World\\\"' IN BOOLEAN MODE)", $query);
	}

	function testQueryWithSinglePart()
	{
		$query = $this->builder->build(
			new AndX(
				array(
					new Token('Hello', 'plaintext', 'contents', 1.5),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('Hello' IN BOOLEAN MODE)", $query);
	}

	function testBuildOrQuery()
	{
		$query = $this->builder->build(
			new OrX(
				array(
					new Token('Hello', 'plaintext', 'contents', 1.5),
					new Token('World', 'plaintext', 'contents', 1.0),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(Hello World)' IN BOOLEAN MODE)", $query);
	}

	function testAndQuery()
	{
		$query = $this->builder->build(
			new AndX(
				array(
					new Token('Hello', 'plaintext', 'contents', 1.5),
					new Token('World', 'plaintext', 'contents', 1.0),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(+Hello +World)' IN BOOLEAN MODE)", $query);
	}

	function testNotBuild()
	{
		$query = $this->builder->build(
			new NotX(
				new Token('Hello', 'plaintext', 'contents', 1.5)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('-Hello' IN BOOLEAN MODE)", $query);
	}

	function testFlattenNot()
	{
		$query = $this->builder->build(
			new AndX(
				array(
					new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
					new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
					new Token('Test', 'plaintext', 'contents', 1.0),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(-Hello -World +Test)' IN BOOLEAN MODE)", $query);
	}

	function testBuildOrQueryDifferentField()
	{
		$query = $this->builder->build(
			new OrX(
				array(
					new Token('Hello', 'plaintext', 'foobar', 1.5),
					new Token('World', 'plaintext', 'baz', 1.0),
				)
			)
		);

		$this->assertEquals("(MATCH (`foobar`) AGAINST ('Hello' IN BOOLEAN MODE) OR MATCH (`baz`) AGAINST ('World' IN BOOLEAN MODE))", $query);
	}

	function testAndQueryDifferentField()
	{
		$query = $this->builder->build(
			new AndX(
				array(
					new Token('Hello', 'plaintext', 'foobar', 1.5),
					new Token('World', 'plaintext', 'baz', 1.0),
				)
			)
		);

		$this->assertEquals("(MATCH (`foobar`) AGAINST ('Hello' IN BOOLEAN MODE) AND MATCH (`baz`) AGAINST ('World' IN BOOLEAN MODE))", $query);
	}

	function testNotBuildDifferentField()
	{
		$query = $this->builder->build(
			new NotX(
				new Token('Hello', 'identifier', 'object_id', 1.5)
			)
		);

		$this->assertEquals("NOT (`object_id` = 'Hello')", $query);
	}

	function testFlattenNotDifferentField()
	{
		$query = $this->builder->build(
			new AndX(
				array(
					new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
					new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
					new Token('Test', 'plaintext', 'contents', 1.0),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(-Hello -World +Test)' IN BOOLEAN MODE)", $query);
	}

	function testFilterWithIdentifier()
	{
		$query = $this->builder->build(new Token('Some entry', 'identifier', 'username', 1.5));

		$this->assertEquals("`username` = 'Some entry'", $query);
	}

	function testRangeFilter()
	{
		$query = $this->builder->build(new Range('Hello', 'World', 'plaintext', 'title', 1.5));

		$this->assertEquals("`title` BETWEEN 'Hello' AND 'World'", $query);
	}

	function testInitialMatchFilter()
	{
		$query = $this->builder->build(new Initial('Hello', 'plaintext', 'title', 1.5));

		$this->assertEquals("`title` LIKE 'Hello%'", $query);
	}

	function testNestedOr()
	{
		$query = $this->builder->build(
			new OrX(
				array(
					new OrX(
						array(
							new Token('Hello', 'plaintext', 'contents', 1.5),
							new Token('World', 'plaintext', 'contents', 1.0),
						)
					),
					new Token('Test', 'plaintext', 'contents', 1.0),
				)
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('((Hello World) Test)' IN BOOLEAN MODE)", $query);
	}

	function testNestedAnd()
	{
		$query = $this->builder->build(
			new AndX(
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
			)
		);

		$this->assertEquals("MATCH (`contents`) AGAINST ('(+(Hello World) +(+Hello +World) +Test)' IN BOOLEAN MODE)", $query);
	}
}

