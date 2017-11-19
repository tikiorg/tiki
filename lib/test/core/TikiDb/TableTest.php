<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_TableTest extends PHPUnit_Framework_TestCase
{
	protected $obj;

	protected $tikiDb;

	function testInsertOne()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'INSERT IGNORE INTO `my_table` (`label`) VALUES (?)';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['hello']));

		$mock->expects($this->once())
			->method('lastInsertId')
			->with()
			->will($this->returnValue(42));

		$table = new TikiDb_Table($mock, 'my_table');
		$this->assertEquals(
			42,
			$table->insert(
				['label' => 'hello',],
				true
			)
		);
	}

	function testInsertWithMultipleValues()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'INSERT INTO `test_table` (`label`, `description`, `count`) VALUES (?, ?, ?)';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['hello', 'world', 15]));

		$mock->expects($this->once())
			->method('lastInsertId')
			->with()
			->will($this->returnValue(12));

		$table = new TikiDb_Table($mock, 'test_table');
		$this->assertEquals(
			12,
			$table->insert(
				[
					'label' => 'hello',
					'description' => 'world',
					'count' => 15,
				]
			)
		);
	}

	function testDeletionOnSingleCondition()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'DELETE FROM `my_table` WHERE 1=1 AND `some_id` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo([15]));

		$table = new TikiDb_Table($mock, 'my_table');

		$table->delete(['some_id' => 15,]);
	}

	function testDeletionOnMultipleConditions()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['wiki page', 'HomePage']));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->delete(
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
			]
		);
	}

	function testDeletionForMultiple()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ?';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['wiki page', 'HomePage']));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->deleteMultiple(
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
			]
		);
	}

	function testDeleteNullCondition()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'DELETE FROM `other_table` WHERE 1=1 AND `objectType` = ? AND `objectId` = ? AND (`lang` = ? OR `lang` IS NULL) LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['wiki page', 'HomePage', null]));

		$table = new TikiDb_Table($mock, 'other_table');

		$table->delete(
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
				'lang' => '',
			]
		);
	}

	function testUpdate()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'UPDATE `my_table` SET `title` = ?, `description` = ? WHERE 1=1 AND `objectType` = ? AND `objectId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['hello world', 'foobar', 'wiki page', 'HomePage']));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(
			[
				'title' => 'hello world',
				'description' => 'foobar',
			],
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
			]
		);
	}

	function testUpdateMultiple()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'UPDATE `my_table` SET `title` = ?, `description` = ? WHERE 1=1 AND `objectType` = ? AND `objectId` = ?';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['hello world', 'foobar', 'wiki page', 'HomePage']));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->updateMultiple(
			[
				'title' => 'hello world',
				'description' => 'foobar',
			],
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
			]
		);
	}

	function testInsertOrUpdate()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'INSERT INTO `my_table` (`title`, `description`, `objectType`, `objectId`) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `title` = ?, `description` = ?';

		$mock->expects($this->once())
			->method('queryException')
			->with(
				$this->equalTo($query),
				$this->equalTo(
					[
						'hello world',
						'foobar',
						'wiki page',
						'HomePage',
						'hello world',
						'foobar'
					]
				)
			);

		$table = new TikiDb_Table($mock, 'my_table');
		$table->insertOrUpdate(
			[
				'title' => 'hello world',
				'description' => 'foobar',
			],
			[
				'objectType' => 'wiki page',
				'objectId' => 'HomePage',
			]
		);
	}

	function testExpressionAssign()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'UPDATE `my_table` SET `hits` = `hits` + ? WHERE 1=1 AND `fileId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo([5, 42]));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(
			['hits' => $table->expr('$$ + ?', [5]),],
			['fileId' => 42,]
		);
	}

	function testComplexBuilding()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'UPDATE `my_table` SET `hits` = `weight` * ? * (`hits` + ?) WHERE 1=1 AND `fileId` = ? LIMIT 1';

		$mock->expects($this->once())
			->method('queryException')
			->with(
				$this->equalTo($query),
				$this->equalTo([1.5, 5, 42])
			);

		$table = new TikiDb_Table($mock, 'my_table');
		$table->update(
			['hits' => $table->expr('`weight` * ? * ($$ + ?)', [1.5, 5]),],
			['fileId' => 42,]
		);
	}

	function testComplexCondition()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'DELETE FROM `my_table` WHERE 1=1 AND `pageName` = ? AND `modified` < ?';

		$mock->expects($this->once())
			->method('queryException')
			->with($this->equalTo($query), $this->equalTo(['SomePage', 12345]));

		$table = new TikiDb_Table($mock, 'my_table');
		$table->deleteMultiple(
			[
				'pageName' => 'SomePage',
				'modified' => $table->expr('$$ < ?', [12345]),
			]
		);
	}

	function testReadOne()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `user` FROM `tiki_user_watches` WHERE 1=1 AND `watchId` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(1), $this->equalTo(0))
			->will(
				$this->returnValue(
					[['user' => 'hello'],]
				)
			);

		$table = new TikiDb_Table($mock, 'tiki_user_watches');

		$this->assertEquals('hello', $table->fetchOne('user', ['watchId' => 42]));
	}

	function testFetchColumn()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `group` FROM `tiki_group_watches` WHERE 1=1 AND `object` = ? AND `event` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42, 'foobar']), $this->equalTo(-1), $this->equalTo(-1))
			->will(
				$this->returnValue(
					[
						['group' => 'hello'],
							['group' => 'world'],
					]
				)
			);

		$table = new TikiDb_Table($mock, 'tiki_group_watches');
		$this->assertEquals(['hello', 'world'], $table->fetchColumn('group', ['object' => 42, 'event' => 'foobar']));
	}

	function testFetchColumnWithSort()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `group` FROM `tiki_group_watches` WHERE 1=1 AND `object` = ? AND `event` = ? ORDER BY `group` ASC';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42, 'foobar']), $this->equalTo(-1), $this->equalTo(-1))
			->will(
				$this->returnValue(
					[
						['group' => 'hello'],
						['group' => 'world'],
					]
				)
			);

		$table = new TikiDb_Table($mock, 'tiki_group_watches');
		$this->assertEquals(['hello', 'world'], $table->fetchColumn('group', ['object' => 42, 'event' => 'foobar'], -1, -1, 'ASC'));
	}

	function testFetchAll_shouldConsiderOnlyProvidedFields()
	{
		$expectedResult = [
			['user' => 'admin'],
			['user' => 'test']
		];

		$query = 'SELECT `user`, `email` FROM `users_users` WHERE 1=1';

		$tikiDb = $this->createMock('TikiDb');
		$tikiDb->expects($this->once())->method('fetchAll')
			->with($query, [], -1, -1)
			->will($this->returnValue($expectedResult));

		$table = new TikiDb_Table($tikiDb, 'users_users');

		$this->assertEquals($expectedResult, $table->fetchAll(['user', 'email'], []));
	}

	function testFetchAll_shouldReturnAllFieldsIfFirstParamIsEmpty()
	{
		$expectedResult = [
			['user' => 'admin'],
			['user' => 'test']
		];

		$query = 'SELECT * FROM `users_users` WHERE 1=1';

		$tikiDb = $this->createMock('TikiDb');
		$tikiDb->expects($this->exactly(2))->method('fetchAll')
			->with($query, [], -1, -1)
			->will($this->returnValue($expectedResult));

		$table = new TikiDb_Table($tikiDb, 'users_users');

		$this->assertEquals($expectedResult, $table->fetchAll([], []));
		$this->assertEquals($expectedResult, $table->fetchAll());
	}

	function testFetchRow()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `user`, `email` FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$row = ['user' => 'hello', 'email' => 'hello@example.com'];

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue([$row,]));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals($row, $table->fetchRow(['user', 'email'], ['userId' => 42]));
	}

	function testFetchCount()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT COUNT(*) FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue([[15],]));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals(15, $table->fetchCount(['userId' => 42]));
	}

	function testFetchFullRow()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT * FROM `users_users` WHERE 1=1 AND `userId` = ?';

		$row = ['user' => 'hello', 'email' => 'hello@example.com'];

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(1), $this->equalTo(0))
			->will($this->returnValue([$row,]));

		$table = new TikiDb_Table($mock, 'users_users');

		$this->assertEquals($row, $table->fetchFullRow(['userId' => 42]));
	}

	function testFetchMap()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `user`, `email` FROM `users_users` WHERE 1=1 AND `userId` > ? ORDER BY `user` DESC';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(-1), $this->equalTo(-1))
			->will(
				$this->returnValue(
					[
						['user' => 'hello', 'email' => 'hello@example.com'],
						['user' => 'world', 'email' => 'world@example.com'],
					]
				)
			);

		$table = new TikiDb_Table($mock, 'users_users');

		$expect = [
				'hello' => 'hello@example.com',
				'world' => 'world@example.com',
				];
		$this->assertEquals($expect, $table->fetchMap('user', 'email', ['userId' => $table->greaterThan(42)], -1, -1, ['user' => 'DESC']));
	}

	function testAliasField()
	{
		$mock = $this->createMock('TikiDb');

		$query = 'SELECT `user`, `email` AS `address` FROM `users_users` WHERE 1=1 AND `userId` > ? ORDER BY `user` DESC';

		$mock->expects($this->once())
			->method('fetchAll')
			->with($this->equalTo($query), $this->equalTo([42]), $this->equalTo(-1), $this->equalTo(-1))
			->will(
				$this->returnValue(
					[
						['user' => 'hello', 'address' => 'hello@example.com'],
						['user' => 'world', 'address' => 'world@example.com'],
					]
				)
			);

		$table = new TikiDb_Table($mock, 'users_users');

		$expect = [
				['user' => 'hello', 'address' => 'hello@example.com'],
				['user' => 'world', 'address' => 'world@example.com'],
				];
		$this->assertEquals($expect, $table->fetchAll(['user', 'address' => 'email'], ['userId' => $table->greaterThan(42)], -1, -1, ['user' => 'DESC']));
	}

	function testIncrement()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ + ?', [1]), $table->increment(1));
	}

	function testDecrement()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ - ?', [1]), $table->decrement(1));
	}

	function testNot()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ <> ?', [1]), $table->not(1));
	}

	function testGreaterThan()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ > ?', [1]), $table->greaterThan(1));
	}

	function testLesserThan()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ < ?', [1]), $table->lesserThan(1));
	}

	function testLike()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ LIKE ?', ['foo%']), $table->like('foo%'));
	}

	function testInWithEmptyArray()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('1=0', []), $table->in([]));
	}

	function testInWithData()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('$$ IN(?, ?, ?)', [1, 2, 3]), $table->in([1, 2, 3]));
	}

	function testInWithDataNotSensitive()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('BINARY $$ IN(?, ?, ?)', [1, 2, 3]), $table->in([1, 2, 3], true));
	}

	function testExactMatch()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('BINARY $$ = ?', ['foo%']), $table->exactly('foo%'));
	}

	function testAllFields()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals([$table->expr('*', [])], $table->all());
	}

	function testCountAll()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('COUNT(*)', []), $table->count());
	}

	function testSumField()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('SUM(`hits`)', []), $table->sum('hits'));
	}

	function testMaxField()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('MAX(`hits`)', []), $table->max('hits'));
	}

	function testFindIn()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');

		$this->assertEquals($table->expr('(`a` LIKE ? OR `b` LIKE ? OR `c` LIKE ?)', ["%X%", "%X%", "%X%"]), $table->findIn('X', ['a', 'b', 'c']));
	}

	function testEmptyConcat()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');
		$this->assertEquals($table->expr('', []), $table->concatFields([]));
	}

	function testEmptyConcatWithMultiple()
	{
		$mock = $this->createMock('TikiDb');
		$table = new TikiDb_Table($mock, 'my_table');
		$this->assertEquals($table->expr('CONCAT(`a`, `b`, `c`)', []), $table->concatFields(['a', 'b', 'c']));
	}
}
