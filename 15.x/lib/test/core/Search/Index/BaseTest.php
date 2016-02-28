<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 */
abstract class Search_Index_BaseTest extends PHPUnit_Framework_TestCase
{
	const DOCUMENT_DATE = 1234567890;
	protected $index;

	protected function populate($index)
	{
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(
			array(
				'object_type' => $typeFactory->identifier('wiki page'),
				'object_id' => $typeFactory->identifier('HomePage'),
				'title' => $typeFactory->sortable('HomePage'),
				'language' => $typeFactory->identifier('en'),
				'modification_date' => $typeFactory->timestamp(self::DOCUMENT_DATE),
				'description' => $typeFactory->sortable('a description for the page'),
				'categories' => $typeFactory->multivalue(array(1, 2, 5, 6)),
				'allowed_groups' => $typeFactory->multivalue(array('Project Lead', 'Editor', 'Admins')),
				'contents' => $typeFactory->plaintext('a description for the page Bonjour world!'),
				'relations' => $typeFactory->multivalue(
					array(
						Search_Query_Relation::token('tiki.content.link', 'wiki page', 'About'),
						Search_Query_Relation::token('tiki.content.link', 'wiki page', 'Contact'),
						Search_Query_Relation::token('tiki.content.link.invert', 'wiki page', 'Product'),
						Search_Query_Relation::token('tiki.user.favorite.invert', 'user', 'bob'),
					)
				),
			)
		);

	}

	function testBasicSearch()
	{
		$positive = new Search_Query('Bonjour');
		$negative = new Search_Query('NotInDocument');

		$this->assertContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $this->stripExtra($positive->search($this->index)));
		$this->assertNotContains(array('object_type' => 'wiki page', 'object_id' => 'HomePage'), $this->stripExtra($negative->search($this->index)));
	}

	private function stripExtra($list)
	{
		$out = array();

		foreach ($list as $entry) {
			$out[] = array_intersect_key($entry, array('object_type' => '', 'object_id' => ''));
		}

		return $out;
	}

	function testFieldSpecificSearch()
	{
		$off = new Search_Query;
		$off->filterContent('description', 'title');
		$found = new Search_Query;
		$found->filterContent('description', 'description');

		$this->assertGreaterThan(0, count($found->search($this->index)));
		$this->assertEquals(0, count($off->search($this->index)));
	}

	function testWithOrCondition()
	{
		$positive = new Search_Query('foobar or bonjour');
		$negative = new Search_Query('foobar or baz');

		$this->assertGreaterThan(0, count($positive->search($this->index)));
		$this->assertEquals(0, count($negative->search($this->index)));
	}

	function testWithNotCondition()
	{
		$negative = new Search_Query('not world and bonjour');
		$positive = new Search_Query('not foobar and bonjour');

		$this->assertEquals(0, count($negative->search($this->index)));
		$this->assertGreaterThan(0, count($positive->search($this->index)));
	}

	function testFilterType()
	{
		$this->assertResultCount(1, 'filterType', 'wiki page');
		$this->assertResultCount(0, 'filterType', 'wiki');
		$this->assertResultCount(0, 'filterType', 'blog post');
	}

	function testFilterCategories()
	{
		$this->assertResultCount(0, 'filterCategory', '3');
		$this->assertResultCount(1, 'filterCategory', '1 and 2');
		$this->assertResultCount(0, 'filterCategory', '1 and not 2');
		$this->assertResultCount(1, 'filterCategory', '1 and (2 or 3)');
	}

	function testLanguageFilter()
	{
		$this->assertResultCount(1, 'filterLanguage', 'en');
		$this->assertResultCount(1, 'filterLanguage', 'en or fr');
		$this->assertResultCount(0, 'filterLanguage', 'en and fr');
		$this->assertResultCount(0, 'filterLanguage', 'fr');
		$this->assertResultCount(0, 'filterLanguage', 'NOT en');
		$this->assertResultCount(0, 'filterLanguage', 'NOT en');
	}

	function testFilterPermissions()
	{
		$this->assertResultCount(0, 'filterPermissions', array('Anonymous'));
		$this->assertResultCount(0, 'filterPermissions', array('Registered'));
		$this->assertResultCount(1, 'filterPermissions', array('Registered', 'Editor'));
		$this->assertResultCount(1, 'filterPermissions', array('Project Lead'));
		$this->assertResultCount(0, 'filterPermissions', array('Project'));
	}

	function testRangeFilter()
	{
		$this->assertResultCount(1, 'filterRange', self::DOCUMENT_DATE - 1000, self::DOCUMENT_DATE + 1000);
		$this->assertResultCount(0, 'filterRange', self::DOCUMENT_DATE - 1000, self::DOCUMENT_DATE - 500);
		$this->assertResultCount(1, 'filterRange', 2, 2000000000); // Check lexicography
	}

	function testIndexProvidesHighlightHelper()
	{
		$query = new Search_Query('foobar or Bonjour');
		$resultSet = $query->search($this->index);

		// Manually adding the content to avoid initializing the entire formatter
		foreach ($resultSet as & $entry) {
			$entry['content'] = 'Bonjour World';
		}

		$plugin = new Search_Formatter_Plugin_WikiTemplate('{display name=highlight}');
		$formatter = new Search_Formatter($plugin);
		$output = $formatter->format($resultSet);

		$this->assertContains('<b style="color:black;background-color:#ff66ff">Bonjour</b>', $output);
		$this->assertNotContains('<body>', $output);
	}

	function testInvalidQueries()
	{
		$this->assertResultCount(0, 'filterContent', 'in*lid');
		$this->assertResultCount(0, 'filterContent', 'i?lid');
	}

	function testMatchInitial()
	{
		$this->assertResultCount(1, 'filterInitial', 'a description for', 'description');
		$this->assertResultCount(0, 'filterInitial', 'a description in', 'description');

		$this->assertResultCount(1, 'filterInitial', 'HomePage');
		$this->assertResultCount(1, 'filterInitial', 'Home');
		$this->assertResultCount(0, 'filterInitial', 'Fuzzy');
		$this->assertResultCount(0, 'filterInitial', 'Ham');
		$this->assertResultCount(0, 'filterInitial', 'HomePagd');
		$this->assertResultCount(0, 'filterInitial', 'Home Page');
	}

	function testNotMatchInitial()
	{
		$this->assertResultCount(0, 'filterNotInitial', 'a description for', 'description');
		$this->assertResultCount(1, 'filterNotInitial', 'a description in', 'description');

		$this->assertResultCount(0, 'filterNotInitial', 'HomePage');
		$this->assertResultCount(0, 'filterNotInitial', 'Home');
		$this->assertResultCount(1, 'filterNotInitial', 'Fuzzy');
		$this->assertResultCount(1, 'filterNotInitial', 'Ham');
		$this->assertResultCount(1, 'filterNotInitial', 'HomePagd');
		$this->assertResultCount(1, 'filterNotInitial', 'Home Page');
	}

	function testFilterRelations()
	{
		$about = new Search_Query_Relation('tiki.content.link', 'wiki page', 'About');
		$contact = new Search_Query_Relation('tiki.content.link', 'wiki page', 'Contact');
		$product = new Search_Query_Relation('tiki.content.link.invert', 'wiki page', 'Product');
		$user = new Search_Query_Relation('tiki.user.favorite.invert', 'user', 'bob');
		$nothing = new Search_Query_Relation('foo.bar.baz', 'trackeritem', '2');

		$invert_product = new Search_Query_Relation('tiki.content.link', 'wiki page', 'Product');
		$invert_user = new Search_Query_Relation('tiki.user.favorite', 'user', 'bob');

		$this->assertResultCount(0, 'filterRelation', "$nothing");
		$this->assertResultCount(1, 'filterRelation', "$about");
		$this->assertResultCount(1, 'filterRelation', "$about or $product");
		$this->assertResultCount(1, 'filterRelation', "$user and not $nothing");
		$this->assertResultCount(0, 'filterRelation', "$user and not $about");

		$this->assertResultCount(1, 'filterRelation', "$invert_product and $invert_user", array('tiki.content.link', 'tiki.user.favorite'));
	}

	private function assertResultCount($count, $filterMethod, $argument)
	{
		$arguments = func_get_args();
		$arguments = array_slice($arguments, 2);

		$query = new Search_Query;
		call_user_func_array(array($query, $filterMethod), $arguments);

		$this->assertEquals($count, count($query->search($this->index)));
	}
}

