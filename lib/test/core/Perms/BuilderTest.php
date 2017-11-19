<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Perms_BuilderTest extends PHPUnit_Framework_TestCase
{
	function testDefaultBuilder()
	{
		$builder = new Perms_Builder;
		$perms = $builder->build();

		$expect = $this->getExpect();
		$this->assertEquals($expect, $perms);
	}

	function testBuildAdminPermissionMap()
	{
		$builder = new Perms_Builder;
		$perms = $builder
			->withCategories(true)
			->build();

		$expect = $this->getExpect(true);
		$this->assertEquals($expect, $perms);
	}

	function testAdminIndirects()
	{
		$builder = new Perms_Builder;
		$perms = $builder->withDefinitions(
			[
				[
					'name' => 'tiki_p_admin_wiki',
					'type' => 'wiki',
					'scope' => 'object',
					'admin' => true,
				],
				[
					'name' => 'tiki_p_edit',
					'type' => 'wiki',
					'scope' => 'object',
					'admin' => false,
				],
			]
		)->build();

		$expect = $this->getExpect(
			false,
			['edit' => 'admin_wiki',]
		);

		$this->assertEquals($expect, $perms);
	}

	function testGlobalChecksOnly()
	{
		$builder = new Perms_Builder;

		$perms = $builder->withDefinitions(
			[
				[
					'name' => 'tiki_p_search',
					'type' => 'tiki',
					'scope' => 'global',
					'admin' => false,
				],
				[
					'name' => 'tiki_p_edit',
					'type' => 'wiki',
					'scope' => 'object',
					'admin' => false,
				],
			]
		)->build();

		$expect = $this->getExpect(false, [], ['search']);
		$this->assertEquals($expect, $perms);
	}

	private function getExpect($categories = false, $indirectMap = [], $globals = [])
	{
		$expect = new Perms;
		$expect->setPrefix('tiki_p_');

		$expect->setCheckSequence(
			[
				$globalAdminCheck = new Perms_Check_Alternate('admin'),
				$fixedResolverCheck = new Perms_Check_Fixed($globals),
				new Perms_Check_Direct,
				new Perms_Check_Indirect($indirectMap),
			]
		);

		$expect->setResolverFactories(
			array_values(
				array_filter(
					[
						new Perms_ResolverFactory_ObjectFactory,
						$categories ? new Perms_ResolverFactory_CategoryFactory : null,
						new Perms_ResolverFactory_ObjectFactory('parent'),
						$categories ? new Perms_ResolverFactory_CategoryFactory('parent') : null,
						new Perms_ResolverFactory_GlobalFactory,
					]
				)
			)
		);

		$resolver = $expect->getAccessor()->getResolver();
		$globalAdminCheck->setResolver($resolver);
		$fixedResolverCheck->setResolver($resolver);

		return $expect;
	}
}
