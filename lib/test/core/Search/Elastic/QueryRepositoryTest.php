<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_QueryRepositoryTest extends PHPUnit_Framework_TestCase
{
	function setUp()
	{
		$connection = new Search_Elastic_Connection('http://localhost:9200');

		$status = $connection->getStatus();
		if (! $status->ok) {
			$this->markTestSkipped('ElasticSearch needs to be available on localhost:9200 for the test to run.');
		}

		$this->index = new Search_Elastic_Index($connection, 'test_index');
		$this->index->destroy();
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}

	function testNothingToMatch()
	{
		$tf = $this->index->getTypeFactory();
		$names = $this->index->getMatchingQueries(array(
			'object_type' => $tf->identifier('wiki page'),
			'object_id' => $tf->identifier('HomePage'),
			'contents' => $tf->plaintext('Hello World!'),
		));

		$this->assertEquals(array(), $names);
	}

	function testFilterBasicContent()
	{
		$query = new Search_Query('Hello World');
		$query->store('my_custom_name', $this->index);

		$tf = $this->index->getTypeFactory();
		$names = $this->index->getMatchingQueries(array(
			'object_type' => $tf->identifier('wiki page'),
			'object_id' => $tf->identifier('HomePage'),
			'contents' => $tf->plaintext('Hello World!'),
		));

		$this->assertEquals(array('my_custom_name'), $names);
	}

	function testFilterFailsToFindContent()
	{
		$query = new Search_Query('Foobar');
		$query->store('my_custom_name', $this->index);

		$tf = $this->index->getTypeFactory();
		$names = $this->index->getMatchingQueries(array(
			'object_type' => $tf->identifier('wiki page'),
			'object_id' => $tf->identifier('HomePage'),
			'contents' => $tf->plaintext('Hello World!'),
		));

		$this->assertEquals(array(), $names);
	}
}

