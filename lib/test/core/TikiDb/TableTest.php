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
}

