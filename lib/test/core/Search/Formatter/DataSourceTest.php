<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Formatter_DataSourceTest extends PHPUnit_Framework_TestCase
{
	private $wikiSource;
	private $categorySource;
	private $permissionSource;

	function setUp()
	{
		$this->wikiSource = new Search_ContentSource_Static(
			['Test' => ['description' => 'ABC'],],
			['description' => 'sortable']
		);

		$this->categorySource = new Search_GlobalSource_Static(
			['wiki page:Test' => ['categories' => [1, 2, 3]],],
			['categories' => 'multivalue']
		);

		$this->permissionSource = new Search_GlobalSource_Static(
			[
				'wiki page:Test' => ['allowed_groups' => ['Editors', 'Admins']],
			],
			['allowed_groups' => 'multivalue']
		);
	}

	function testObtainInformationFromContentSource()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertEquals(['description' => 'ABC'], $source->getData(['object_type' => 'wiki page', 'object_id' => 'Test'], 'description'));
	}

	function testRequestedValueNotProvided()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertEquals([], $source->getData(['object_type' => 'wiki page', 'object_id' => 'Test'], 'title'));
	}

	function testValueFromGlobal()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addGlobalSource($this->categorySource);
		$source->addGlobalSource($this->permissionSource);

		$this->assertEquals(['categories' => [1, 2, 3]], $source->getData(['object_type' => 'wiki page', 'object_id' => 'Test'], 'categories'));
		$this->assertEquals(['allowed_groups' => ['Editors', 'Admins']], $source->getData(['object_type' => 'wiki page', 'object_id' => 'Test'], 'allowed_groups'));
	}

	function testContentSourceNotAvailable()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$this->assertEquals([], $source->getData(['object_type' => 'wiki page', 'object_id' => 'Test'], 'title'));
	}
}
