<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Elastic_RescoreQueryBuilder as QueryBuilder;
use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;
use Search_Expr_Not as NotX;
use Search_Expr_Range as Range;
use Search_Expr_Initial as Initial;
use Search_Expr_MoreLikeThis as MoreLikeThis;
use Search_Expr_ImplicitPhrase as ImplicitPhrase;
use Search_Expr_ExplicitPhrase as ExplicitPhrase;

class Search_Elastic_RescoreQueryBuilderTest extends PHPUnit_Framework_TestCase
{
	function testSimpleQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new ExplicitPhrase('Hello', 'plaintext', 'contents', 1.5));

		$this->assertEquals(
			['rescore' => [
				'window_size' => 50,
				'query' => [
					'rescore_query' => ['bool' => ['should' => [
						["match_phrase" => [
							"contents" => ["query" => "hello", "boost" => 1.5, 'slop' => 50],
						]],
					]]],
				]
			]], $query
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
				["match_phrase" => [
						"contents" => ["query" => "hello", "boost" => 1.5, 'slop' => 50],
				]],
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testBuildImplicitQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new ImplicitPhrase(
				[
					new Token('Hello', 'plaintext', 'contents', 1.0),
					new Token('World', 'plaintext', 'contents', 1.0),
				]
			)
		);

		$this->assertEquals(
			[
				["match_phrase" => [
						"contents" => ["query" => "hello world", "boost" => 1.0, 'slop' => 50],
				]],
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testBuildOrQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new OrX(
				[
					new Token('Hello', 'plaintext', 'contents', 1.0),
					new Token('World', 'plaintext', 'contents', 1.5),
				]
			)
		);

		$this->assertEquals(
			[
				["match_phrase" => [
						"contents" => ["query" => "hello", "boost" => 1.0, 'slop' => 50],
				]],
				["match_phrase" => [
						"contents" => ["query" => "world", "boost" => 1.5, 'slop' => 50],
				]],
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testBuildAndQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new AndX(
				[
					new Token('Hello', 'plaintext', 'contents', 1.0),
					new Token('World', 'plaintext', 'contents', 1.5),
				]
			)
		);

		// Notice how AND does not matter when ranking
		$this->assertEquals(
			[
				["match_phrase" => [
						"contents" => ["query" => "hello", "boost" => 1.0, 'slop' => 50],
				]],
				["match_phrase" => [
						"contents" => ["query" => "world", "boost" => 1.5, 'slop' => 50],
				]],
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}


	function testBuildNotQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(
			new NotX(
				new Token('Hello', 'plaintext', 'contents', 1.5)
			)
		);

		$this->assertEquals(
			[
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testFilterWithIdentifier()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Token('Some entry', 'identifier', 'username', 1.5));

		$this->assertEquals(
			[
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testRangeFilter()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Range('Hello', 'World', 'plaintext', 'title', 1.5));

		$this->assertEquals(
			[
			], $query['rescore']['query']['rescore_query']['bool']['should']
		);
	}

	function testInitialMatchFilter()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Initial('Hello', 'plaintext', 'title', 1.5));

		$this->assertEquals(
			[
			], $query['rescore']['query']['rescore_query']['bool']['should']
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
			],
			$query['rescore']['query']['rescore_query']['bool']['should']
		);
	}
}

