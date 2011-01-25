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

	function testUpdate()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'UPDATE `my_table` SET `title` = ?, `description` = ? WHERE 1=1 AND `objectType` = ? AND `objectId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('hello world', 'foobar', 'wiki page', 'HomePage')));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(array(
			'title' => 'hello world',
			'description' => 'foobar',
		), array(
			'objectType' => 'wiki page',
			'objectId' => 'HomePage',
		));
	}

	function testUpdateMultiple()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'UPDATE `my_table` SET `title` = ?, `description` = ? WHERE 1=1 AND `objectType` = ? AND `objectId` = ?';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('hello world', 'foobar', 'wiki page', 'HomePage')));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->updateMultiple(array(
			'title' => 'hello world',
			'description' => 'foobar',
		), array(
			'objectType' => 'wiki page',
			'objectId' => 'HomePage',
		));
	}

	function testExpressionAssign()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'UPDATE `my_table` SET `hits` = `hits` + ? WHERE 1=1 AND `fileId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array(5, 42)));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(array(
			'hits' => $table->expr('$$ + ?', array(5)),
		), array(
			'fileId' => 42,
		));
	}

	function testComplexBuilding()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'UPDATE `my_table` SET `hits` = `weight` * ? * (`hits` + ?) WHERE 1=1 AND `fileId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array(1.5, 5, 42)));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(array(
			'hits' => $table->expr('`weight` * ? * ($$ + ?)', array(1.5, 5)),
		), array(
			'fileId' => 42,
		));
	}

	function testComplexCondition()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'DELETE FROM `my_table` WHERE 1=1 AND `pageName` = ? AND `modified` < ?';

		$mock->expects($this->once())
			->method('query')
			->with($this->equalTo($query), $this->equalTo(array('SomePage', 12345)));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->deleteMultiple(array(
			'pageName' => 'SomePage',
			'modified' => $table->expr('$$ < ?', array(12345)),
		));
	}

	function testReadOne()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT `user` FROM `tiki_user_watches` WHERE 1=1 AND `watchId` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42)), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue(array(
				array('user' => 'hello'),
			)));

		$table = new TikiDb_Table($mock, 'tiki_user_watches');

		$this->assertEquals('hello', $table->fetchOne('user', array('watchId' => 42)));
	}

	function testFetchColumn()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT `group` FROM `tiki_group_watches` WHERE 1=1 AND `object` = ? AND `event` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42, 'foobar')), $this->equalTo(-1), $this->equalTo(-1))
			->will($this->returnValue(array(
				array('group' => 'hello'),
				array('group' => 'world'),
			)));

		$table = new TikiDb_Table($mock, 'tiki_group_watches');
		$this->assertEquals(array('hello', 'world'), $table->fetchColumn('group', array('object' => 42, 'event' => 'foobar')));
	}

	function testFetchRow()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT `user`, `email` FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$row = array('user' => 'hello', 'email' => 'hello@example.com');

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42)), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue(array(
				$row,
			)));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals($row, $table->fetchRow(array('user', 'email'), array('userId' => 42)));
	}

	function testFetchCount()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT COUNT(*) FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42)), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue(array(
				array(15),
			)));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals(15, $table->fetchCount(array('userId' => 42)));
	}

	function testFetchFullRow() {
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT * FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$row = array('user' => 'hello', 'email' => 'hello@example.com');

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42)), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue(array(
				$row,
			)));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals($row, $table->fetchFullRow(array('userId' => 42)));
	}

	function testFetchMap()
	{
		$mock = $this->getMock('TikiDb');

		$query = 'SELECT `user`, `email` FROM `users_users` WHERE 1=1 AND `userId` > ?';


		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo(array(42)), $this->equalTo(-1), $this->equalTo(-1))
			->will($this->returnValue(array(
				array('user' => 'hello', 'email' => 'hello@example.com'),
				array('user' => 'world', 'email' => 'world@example.com'),
			)));

		$table = new TikiDb_Table($mock, 'users_users');

		$expect = array(
			'hello' => 'hello@example.com',
			'world' => 'world@example.com',
		);
		$this->assertEquals($expect, $table->fetchMap('user', 'email', array('userId' => $table->greaterThan(42))));
	}

	function testIncrement()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ + ?', array(1)), $table->increment(1));
	}

	function testDecrement()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ - ?', array(1)), $table->decrement(1));
	}

	function testGreaterThan()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ > ?', array(1)), $table->greaterThan(1));
	}

	function testLesserThan()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ < ?', array(1)), $table->lesserThan(1));
	}

	function testLike()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ LIKE ?', array('foo%')), $table->like('foo%'));
	}

	function testExactMatch()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('BINARY $$ = ?', array('foo%')), $table->exactly('foo%'));
	}

	function testAllFields()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals(array($table->expr('*', array())), $table->all());
	}

	function testCountAll()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('COUNT(*)', array()), $table->count());
	}

	function testSumField()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('SUM(`hits`)', array()), $table->sum('hits'));
	}

	function testMaxField()
	{
		$mock = $this->getMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('MAX(`hits`)', array()), $table->max('hits'));
	}
}

