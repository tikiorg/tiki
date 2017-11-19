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

class DeclFilter_ConfigureTest extends TikiTestCase
{
	function testSimple()
	{
		$configuration = [
			['staticKeyFilters' => [
				'hello' => 'digits',
				'world' => 'alpha',
			]],
			['staticKeyFiltersForArrays' => [
				'foo' => 'digits',
			]],
			['catchAllFilter' => new Zend\Filter\StringToUpper],
		];

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			[
				'hello' => '123abc',
				'world' => '123abc',
				'foo' => [
					'abc123',
					'def456',
				],
				'bar' => 'undeclared',
			]
		);

		$this->assertEquals($data['hello'], '123');
		$this->assertEquals($data['world'], 'abc');
		$this->assertContains('123', $data['foo']);
		$this->assertContains('456', $data['foo']);
		$this->assertEquals($data['bar'], 'UNDECLARED');
	}

	/**
	 * Triggered errors become exceptions...
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testDisallowed()
	{
		$configuration = [
			['catchAllFilter' => new Zend\Filter\StringToUpper],
		];

		$filter = DeclFilter::fromConfiguration($configuration, ['catchAllFilter']);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testMissingLevel()
	{
		$configuration = [
			'catchAllUnset' => null,
		];

		$filter = DeclFilter::fromConfiguration($configuration);
	}

	function testUnsetSome()
	{
		$configuration = [
			['staticKeyUnset' => ['hello', 'world']],
			['catchAllFilter' => new Zend\Filter\StringToUpper],
		];

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			[
				'hello' => '123abc',
				'world' => '123abc',
				'bar' => 'undeclared',
			]
		);

		$this->assertFalse(isset($data['hello']));
		$this->assertFalse(isset($data['world']));
		$this->assertEquals($data['bar'], 'UNDECLARED');
	}

	function testUnsetOthers()
	{
		$configuration = [
			['staticKeyFilters' => [
				'hello' => 'digits',
				'world' => 'alpha',
			]],
			['catchAllUnset' => null],
		];

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			[
				'hello' => '123abc',
				'world' => '123abc',
				'bar' => 'undeclared',
			]
		);

		$this->assertEquals($data['hello'], '123');
		$this->assertEquals($data['world'], 'abc');
		$this->assertFalse(isset($data['bar']));
	}

	function testFilterPattern()
	{
		$configuration = [
			['keyPatternFilters' => [
				'/^hello/' => 'digits',
			]],
			['keyPatternFiltersForArrays' => [
				'/^fo+$/' => 'alpha',
			]],
		];

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			[
				'hello123' => '123abc',
				'hello456' => '123abc',
				'world' => '123abc',
				'foo' => [
					'abc123',
					'def456',
				],
			]
		);

		$this->assertEquals($data['hello123'], '123');
		$this->assertEquals($data['hello456'], '123');
		$this->assertEquals($data['world'], '123abc');
		$this->assertContains('abc', $data['foo']);
		$this->assertContains('def', $data['foo']);
	}

	function testUnsetPattern()
	{
		$configuration = [
			['keyPatternUnset' => [
				'/^hello/',
			]],
		];

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			[
				'hello123' => '123abc',
				'hello456' => '123abc',
				'world' => '123abc',
			]
		);

		$this->assertFalse(isset($data['hello123']));
		$this->assertFalse(isset($data['hello456']));
		$this->assertEquals($data['world'], '123abc');
	}
}
