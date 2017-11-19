<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class Perms_ResolverFactory_TestFactoryTest extends TikiTestCase
{
	/**
	 * @dataProvider hashes
	 */
	function testHashCorrect($known, $in, $out)
	{
		$factory = new Perms_ResolverFactory_TestFactory($known, []);

		$this->assertEquals($out, $factory->getHash($in));
	}

	function hashes()
	{
		return [
			'empty' => [[], [], 'test:'],
			'exact' => [['a'], ['a' => 1], 'test:1'],
			'miss' => [['b'], ['a' => 1], 'test:'],
			'multiple' => [['a', 'b'], ['a' => 1, 'b' => 2], 'test:1:2'],
			'extra' => [['a'], ['a' => 1, 'b' => 2], 'test:1'],
			'ordering' => [['a', 'b'], ['b' => 1, 'a' => 2], 'test:2:1'],
		];
	}

	function testFetchKnown()
	{
		$factory = new Perms_ResolverFactory_TestFactory(
			['a'],
			['test:1' => $a = new Perms_Resolver_Default(true)]
		);

		$this->assertSame($a, $factory->getResolver(['a' => 1]));
	}

	function testFetchUnknown()
	{
		$factory = new Perms_ResolverFactory_TestFactory(
			['a'],
			['test:1' => $a = new Perms_Resolver_Default(true)]
		);

		$this->assertNull($factory->getResolver(['a' => 2]));
	}
}
