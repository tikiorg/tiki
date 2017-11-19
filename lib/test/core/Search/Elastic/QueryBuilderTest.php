<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Elastic_QueryBuilder as QueryBuilder;
use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;
use Search_Expr_Not as NotX;
use Search_Expr_Range as Range;
use Search_Expr_Initial as Initial;
use Search_Expr_MoreLikeThis as MoreLikeThis;

class Search_Elastic_QueryBuilderTest extends PHPUnit_Framework_TestCase
{
	protected $prefErrorMissingField;

	public function setUp()
	{
		global $prefs;
		$this->prefErrorMissingField = $prefs['search_error_missing_field'];
		$prefs['search_error_missing_field'] = 'n';
	}

	public function tearDown()
	{
		global $prefs;
		$prefs['search_error_missing_field'] = $this->prefErrorMissingField;
	}

	function testSimpleQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Token('Hello', 'plaintext', 'contents', 1.5));

		$this->assertEquals(
			[
				"match" => [
					"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
				],
			],
			$query['query']
		);
	}

	function testQueryWithSinglePart()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new Token('Hello', 'plaintext', 'contents', 1.5),
				]
			)
		);

		$this->assertEquals(
			[
				"match" => [
						"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
				],
			],
			$query['query']
		);
	}

	function testBuildOrQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new OrX(
				[
					new Token('Hello', 'plaintext', 'contents', 1.5),
					new Token('World', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"should" => [
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "world", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					"minimum_should_match" => 1,
				],
			],
			$query['query']
		);
	}

	function testAndQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new Token('Hello', 'plaintext', 'contents', 1.5),
					new Token('World', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "world", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testNotBuild()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new NotX(
				new Token('Hello', 'plaintext', 'contents', 1.5)
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must_not" => [
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testFlattenNot()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new NotX(new Token('Hello', 'plaintext', 'contents', 1.5)),
					new NotX(new Token('World', 'plaintext', 'contents', 1.5)),
					new Token('Test', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"match" => [
								"contents" => ["query" => "test", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					"must_not" => [
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "world", "boost" => 1.5, 'operator' => 'and'],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testFilterWithIdentifier()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Token('Some entry', 'identifier', 'username', 1.5));

		$this->assertEquals(
			[
				"match" => [
					"username" => [
						"query" => "Some entry",
						'operator' => 'and'
					],
				],
			],
			$query['query']
		);
	}

	function testRangeFilter()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Range('Hello', 'World', 'plaintext', 'title', 1.5));

		$this->assertEquals(
			[
				"range" => [
					"title" => [
						"from" => "hello",
						"to" => "world",
						"boost" => 1.5,
						"include_upper" => false,
					],
				],
			],
			$query['query']
		);
	}

	function testInitialMatchFilter()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Initial('Hello', 'plaintext', 'title', 1.5));

		$this->assertEquals(
			[
				"match_phrase_prefix" => [
					"title.sort" => [
						"query" => "hello",
						"boost" => 1.5,
					],
				],
			],
			$query['query']
		);
	}

	function testFlattenOr()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new OrX(
				[
					new OrX(
						[
							new Token('Hello', 'plaintext', 'contents', 1.5),
							new Token('World', 'plaintext', 'contents', 1.0),
						]
					),
					new Token('Test', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"should" => [
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "world", "boost" => 1.0, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "test", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					"minimum_should_match" => 1,
				],
			],
			$query['query']
		);
	}

	function testFlattenAnd()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new OrX(
						[
							new Token('Hello', 'plaintext', 'contents', 1.5),
							new Token('World', 'plaintext', 'contents', 1.0),
						]
					),
					new AndX(
						[
							new Token('Hello', 'plaintext', 'contents', 1.5),
							new Token('World', 'plaintext', 'contents', 1.0),
						]
					),
					new Token('Test', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"bool" => [
								"should" => [
									[
										"match" => [
											"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
										],
									],
									[
										"match" => [
											"contents" => ["query" => "world", "boost" => 1.0, 'operator' => 'and'],
										],
									],
								],
								"minimum_should_match" => 1,
							],
						],
						[
							"match" => [
								"contents" => ["query" => "hello", "boost" => 1.5, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "world", "boost" => 1.0, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "test", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testFlattenSingledOutOr()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new OrX(
						[
							new Token('Foo', 'plaintext', 'contents', 1.0),
							new Token('Baz', 'plaintext', 'contents', 1.0),
						]
					),
					new NotX(new Token('Bar', 'plaintext', 'contents', 1.0)),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"should" => [
						[
							"match" => [
								"contents" => ["query" => "foo", "boost" => 1.0, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "baz", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					'must_not' => [
						[
							"match" => [
								"contents" => ["query" => "bar", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					"minimum_should_match" => 1,
				],
			],
			$query['query']
		);
	}

	function testFlattenSingledOutAnd()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new AndX(
						[
							new Token('Foo', 'plaintext', 'contents', 1.0),
							new Token('Baz', 'plaintext', 'contents', 1.0),
						]
					),
					new NotX(new Token('Bar', 'plaintext', 'contents', 1.0)),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"match" => [
								"contents" => ["query" => "foo", "boost" => 1.0, 'operator' => 'and'],
							],
						],
						[
							"match" => [
								"contents" => ["query" => "baz", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
					'must_not' => [
						[
							"match" => [
								"contents" => ["query" => "bar", "boost" => 1.0, 'operator' => 'and'],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testMoreLikeThisQuery()
	{
		$builder = new QueryBuilder;
		$builder->setDocumentReader(
			function ($type, $object) {
				return [
					'object_type' => $type,
					'object_id' => $object,
					'contents' => 'hello world',
				];
			}
		);

		$query = $builder->build(
			new AndX(
				[
					new MoreLikeThis('wiki page', 'A'),
				]
			)
		);

		$this->assertEquals(
			[
				'more_like_this' => [
					'fields' => ['contents'],
					'like' => 'hello world',
					'boost' => 1.0,
				],
			],
			$query['query']
		);
	}

	function testMoreLikeThisThroughAbstraction()
	{
		$builder = new QueryBuilder;
		$builder->setDocumentReader(
			function ($type, $object) {
				return [
					'object_type' => $type,
					'object_id' => $object,
					'contents' => 'hello world',
				];
			}
		);

		$q = new Search_Query;
		$q->filterSimilar('wiki page', 'A');

		$query = $builder->build($q->getExpr());

		$this->assertEquals(
			[
				'bool' => [
					'must' => [
						[
							'more_like_this' => [
								'fields' => ['contents'],
								'like' => 'hello world',
								'boost' => 1.0,
							],
						],
					],
					'must_not' => [
						[
							'bool' => [
								'must' => [
									[
										"match" => [
											"object_type" => ["query" => "wiki page", 'operator' => 'and'],
										],
									],
									[
										"match" => [
											"object_id" => ["query" => "A", 'operator' => 'and'],
										],
									],
								],
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testEmptyString()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Token('', 'identifier', 'contents'));

		$this->assertEquals(
			[
				"bool" => [
					"must_not" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testEmptyStringWithAnd()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new Token('', 'identifier', 'contents'),
					new Token('Hello', 'plaintext', 'field', 1.5)
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"match" => [
								"field" => ["query" => "hello", "operator" => "and", "boost" => 1.5]
							],
						],
					],
					"must_not" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testEmptyStringWithOr()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new OrX(
				[
					new Token('', 'identifier', 'contents'),
					new Token('Hello', 'plaintext', 'field', 1.5)
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"should" => [
						[
							"bool" => [
								"must_not" => [
									[
										"wildcard" => [
											"contents" => '*',
										],
									],
								],
							],
						],
						[
							"match" => [
								"field" => ["query" => "hello", "operator" => "and", "boost" => 1.5]
							],
						],
					],
					"minimum_should_match" => 1
				],
			],
			$query['query']
		);
	}

	function testEmptyStringWithNot()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new Token('', 'identifier', 'contents'),
					new NotX(
						new Token('Hello', 'plaintext', 'field', 1.5)
					),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must_not" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
						[
							"match" => [
								"field" => ["query" => "hello", "operator" => "and", "boost" => 1.5]
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testNonEmptyString()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new NotX(
				new Token('', 'identifier', 'contents')
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testNonEmptyStringWithAnd()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new NotX(
						new Token('', 'identifier', 'contents')
					),
					new Token('Hello', 'plaintext', 'field', 1.5)
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
						[
							"match" => [
								"field" => ["query" => "hello", "operator" => "and", "boost" => 1.5]
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testNonEmptyStringWithNot()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new NotX(
						new Token('', 'identifier', 'contents')
					),
					new NotX(
						new Token('Hello', 'plaintext', 'field', 1.5)
					),
				]
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"wildcard" => [
								"contents" => '*',
							],
						],
					],
					"must_not" => [
						[
							"match" => [
								"field" => ["query" => "hello", "operator" => "and", "boost" => 1.5]
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testEmptyDate()
	{
		$builder = new QueryBuilder($this->dateFieldMappingIndexMock());

		$query = $builder->build(new Token('', 'identifier', 'field_date'));

		$this->assertEquals(
			[
				"bool" => [
					"must_not" => [
						[
							"exists" => [
								"field" => 'field_date',
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	function testNonEmptyDate()
	{
		$builder = new QueryBuilder($this->dateFieldMappingIndexMock());

		$query = $builder->build(
			new NotX(
				new Token('', 'identifier', 'field_date')
			)
		);

		$this->assertEquals(
			[
				"bool" => [
					"must" => [
						[
							"exists" => [
								"field" => 'field_date',
							],
						],
					],
				],
			],
			$query['query']
		);
	}

	private function dateFieldMappingIndexMock()
	{
		$mockIndex = $this->createMock('Search_Elastic_Index');
		$mockIndex->expects($this->any())
			->method('getFieldMapping')
			->with('field_date')
			->will($this->returnValue((object)['type' => 'date']));
		return $mockIndex;
	}
}
