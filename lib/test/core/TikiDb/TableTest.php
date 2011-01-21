<?php

class TikiDb_TableTest extends PHPUnit_Framework_TestCase
{
	function testInsertOne()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'INSERT INTO `my_table` (`label`) VALUES (?)';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('hello')));

		$mock->expects($this->once())
			->method('lastInsertId')
			->with()
			->will($this->returnValue(42));

		$table = new TikiDb_Table($mock, 'my_table');
		$this->assertEquals(42, $table->insert(array(
			'label' => 'hello',
		)));
	}

	function testInsertWithMultipleValues()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'INSERT INTO `test_table` (`label`, `description`, `count`) VALUES (?, ?, ?)';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('hello', 'world', 15)));

		$mock->expects($this->once())
			->method('lastInsertId')
			->with()
			->will($this->returnValue(12));

		$table = new TikiDb_Table($mock, 'test_table');
		$this->assertEquals(12, $table->insert(array(
			'label' => 'hello',
			'description' => 'world',
			'count' => 15,
		)));
	}

	function testDeletionOnSingleCondition()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'DELETE FROM `my_table` WHERE 1=1 AND `some_id` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array(15)));

		$table = new TikiDb_Table($mock, 'my_table');

		$table->delete(array(
			'some_id' => 15,
		));
	}

	function testDeletionOnMultipleConditions()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('wiki page', 'HomePage')));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->delete(array(
			'objectType' => 'wiki page',
			'objectId' => 'HomePage',
		));
	}

	function testDeletionForMultiple()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ?';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('wiki page', 'HomePage')));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->deleteMultiple(array(
			'objectType' => 'wiki page',
			'objectId' => 'HomePage',
		));
	}

	function testDeleteNullCondition()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ? AND (`lang` = ? OR `lang` IS NULL) LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('wiki page', 'HomePage', null)));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->delete(array(
			'objectType' => 'wiki page',
			'objectId' => 'HomePage',
			'lang' => '',
		));
	}
}

