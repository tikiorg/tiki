<?php

/**
 * @group unit
 */
class Search_Index_LuceneTest extends PHPUnit_Framework_TestCase
{
	const DOCUMENT_DATE = 1234567890;
	private $dir;
	private $index;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Index_Lucene($this->dir);
		$typeFactory = $index->getTypeFactory();
		$index->addDocument(array(
			'object_type' => $typeFactory->identifier('wiki page'),
			'object_id' => $typeFactory->identifier('HomePage'),
			'language' => $typeFactory->identifier('en'),
			'modification_date' => $typeFactory->timestamp(self::DOCUMENT_DATE),
			'description' => $typeFactory->plaintext('a description for the page'),
			'wiki_content' => $typeFactory->wikitext('Hello world!'),
			'categories' => $typeFactory->multivalue(array(1, 2, 5, 6)),
			'allowed_groups' => $typeFactory->multivalue(array('Project Lead', 'Editor', 'Admins')),
		));

		$this->index = $index;
	}

	function tearDown()
	{
		$dir = escapeshellarg($this->dir);
		`rm -Rf $dir`;
	}

	function testBasicSearch()
	{
		$positive = new Search_Query('Hello');
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
		$off->filterContent('description', 'wiki_content');
		$found = new Search_Query;
		$found->filterContent('description', 'description');

		$this->assertGreaterThan(0, count($found->search($this->index)));
		$this->assertEquals(0, count($off->search($this->index)));
	}

	function testWithOrCondition()
	{
		$query = new Search_Query('foobar or hello');

		$this->assertGreaterThan(0, count($query->search($this->index)));
	}

	function testWithNotCondition()
	{
		$query = new Search_Query('not world and hello');
		$result = $query->search($this->index);

		$this->assertEquals(0, count($result));
	}

	function testFilterType()
	{
		$this->assertResultCount(0, 'filterType', 'wiki');
		$this->assertResultCount(0, 'filterType', 'blog post');
		$this->assertResultCount(1, 'filterType', 'wiki page');
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
		$query = new Search_Query('foobar or hello');
		$resultSet = $query->search($this->index);

		// Manually adding the content to avoid initializing the entire formatter
		foreach ($resultSet as & $entry) {
			$entry['content'] = 'Hello World';
		}

		$plugin = new Search_Formatter_Plugin_WikiTemplate('{display name=highlight}');
		$formatter = new Search_Formatter($plugin);
		$output = $formatter->format($resultSet);

		$this->assertContains('<b style="color:black;background-color:#ff66ff">Hello</b>', $output);
		$this->assertNotContains('<body>', $output);
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

