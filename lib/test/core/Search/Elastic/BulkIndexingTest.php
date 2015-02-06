<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_BulkIndexingTest extends PHPUnit_Framework_TestCase
{
	function testBasicBulk()
	{
		$parts = array();
		$bulk = new Search_Elastic_BulkOperation(
			10,
			function ($data) use (& $parts) {
				$parts[] = $data;
			}
		);

		$bulk->index('test', 'foo', 1, array('a' => 1));
		$bulk->index('test', 'foo', 2, array('a' => 2));
		$bulk->index('test', 'foo', 3, array('a' => 3));
		$bulk->unindex('test', 'bar', 4);
		$bulk->flush();

		$this->assertCount(1, $parts);

		$this->assertContains(json_encode(array('a' => 3)) . "\n", $parts[0]);
		$this->assertContains(json_encode(array('index' => array('_index' => 'test', '_type' => 'foo', '_id' => 2))) . "\n", $parts[0]);
		$this->assertContains(json_encode(array('delete' => array('_index' => 'test', '_type' => 'bar', '_id' => 4))) . "\n", $parts[0]);
	}

	function testDoubleFlushHasNoImpact()
	{
		$parts = array();
		$bulk = new Search_Elastic_BulkOperation(
			10,
			function ($data) use (& $parts) {
				$parts[] = $data;
			}
		);

		$bulk->index('test', 'foo', 1, array('a' => 1));
		$bulk->index('test', 'foo', 2, array('a' => 2));
		$bulk->index('test', 'foo', 3, array('a' => 3));
		$bulk->unindex('test', 'bar', 4);
		$bulk->flush();
		$bulk->flush();

		$this->assertCount(1, $parts);
	}

	function testAutomaticFlushWhenLimitReached()
	{
		$parts = array();
		$bulk = new Search_Elastic_BulkOperation(
			10,
			function ($data) use (& $parts) {
				$parts[] = $data;
			}
		);

		foreach (range(1, 15) as $i) {
			$bulk->index('test', 'foo', $i, array('a' => $i));
		}

		$bulk->flush();

		$this->assertCount(2, $parts);
	}

	function testFlushOnLimit()
	{
		$parts = array();
		$bulk = new Search_Elastic_BulkOperation(
			15,
			function ($data) use (& $parts) {
				$parts[] = $data;
			}
		);

		foreach (range(1, 45) as $i) {
			$bulk->index('test', 'foo', $i, array('a' => $i));
		}

		$this->assertCount(3, $parts);

		$bulk->flush(); // Does nothing

		$this->assertCount(3, $parts);
	}
}

