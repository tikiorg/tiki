<?php

class Search_Formatter_DataSourceTest extends PHPUnit_Framework_TestCase
{
	private $wikiSource;
	private $categorySource;
	private $permissionSource;

	function setUp()
	{
		$this->wikiSource = new Search_ContentSource_Static(array(
			'Test' => array('description' => 'ABC'),
		), array('description' => 'sortable'));

		$this->categorySource = new Search_GlobalSource_Static(array(
			'wiki page:Test' => array('categories' => array(1, 2, 3)),
		), array('categories' => 'multivalue'));

		$this->permissionSource = new Search_GlobalSource_Static(array(
			'wiki page:Test' => array('allowed_groups' => array('Editors', 'Admins')),
		), array('allowed_groups' => 'multivalue'));
	}

	function testObtainInformationFromContentSource()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertEquals(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test', 'description' => 'ABC'),
		), $source->getInformation(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), array('object_id', 'description')));
	}

	function testRequestedValueNotProvided()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);

		$this->assertEquals(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), $source->getInformation(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), array('object_id', 'title')));
	}

	function testValueFromGlobal()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addGlobalSource($this->categorySource);
		$source->addGlobalSource($this->permissionSource);

		$this->assertEquals(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test', 'categories' => array(1, 2, 3), 'allowed_groups' => array('Editors', 'Admins')),
		), $source->getInformation(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), array('object_id', 'description', 'categories', 'allowed_groups')));
	}

	function testContentSourceNotAvailable()
	{
		$source = new Search_Formatter_DataSource_Declarative;

		$this->assertEquals(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), $source->getInformation(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), array('object_id', 'description', 'categories', 'allowed_groups')));
	}

	function testCompleteTest()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);
		$source->addGlobalSource($this->categorySource);

		$this->assertEquals(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test', 'description' => 'ABC', 'categories' => array(1, 2, 3)),
		), $source->getInformation(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), array('object_id', 'description', 'categories', 'allowed_groups')));
	}

	function testProvideResultSet()
	{
		$source = new Search_Formatter_DataSource_Declarative;
		$source->addContentSource('wiki page', $this->wikiSource);
		$source->addGlobalSource($this->categorySource);

		$in = new Search_ResultSet(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test'),
		), 11, 10, 10);

		$out = new Search_ResultSet(array(
			array('object_type' => 'wiki page', 'object_id' => 'Test', 'description' => 'ABC', 'categories' => array(1, 2, 3)),
		), 11, 10, 10);

		$this->assertEquals($out, $source->getInformation($in, array('object_id', 'description', 'categories', 'allowed_groups')));
	}
}

