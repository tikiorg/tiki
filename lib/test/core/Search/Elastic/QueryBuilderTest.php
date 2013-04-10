<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Search_Elastic_QueryBuilder as QueryBuilder;
use Search_Expr_Token as Token;
use Search_Expr_And as AndX;
use Search_Expr_Or as OrX;

class Search_Elastic_QueryBuilderTest extends PHPUnit_Framework_TestCase
{
	function testSimpleQuery()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new Token('Hello', 'plaintext', 'contents', 1.5));

		$this->assertEquals(array("query" => array(
			"term" => array(
				"contents" => array("value" => "hello", "boost" => 1.5),
			),
		)), $query);
	}

	function testQueryWithSinglePart()
	{
		$builder = new QueryBuilder;

		$query = $builder->build(new AndX(array(
			new Token('Hello', 'plaintext', 'contents', 1.5),
		)));

		$this->assertEquals(array("query" => array(
			"term" => array(
				"contents" => array("value" => "hello", "boost" => 1.5),
			),
		)), $query);
	}
}

