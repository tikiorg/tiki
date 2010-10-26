<?php

/**
 * @group unit
 */
class Search_IndexerTest extends PHPUnit_Framework_TestCase
{
	function testWithoutContentSource()
	{
		$index = new Search_Index_Memory(new Search_Type_Factory_Lucene);

		$indexer = new Search_Indexer($index);
		$indexer->rebuild();

		$this->assertEquals(0, $index->size());
	}

	function testSingleContentProvider()
	{
		$timeA = strtotime('2010-10-10 10:10:10');
		$timeB = strtotime('2010-10-26 12:00:00');

		$data = array(
			'HomePage' => array('wiki_page_name' => 'HomePage', 'wiki_content' => 'Hello World', 'modification_date' => $timeA),
			'Help' => array('wiki_page_name' => 'Help', 'wiki_content' => 'None available.', 'modification_date' => $timeB),
		);

		$typeMap = array(
			'modification_date' => 'timestamp',
			'wiki_content' => 'wikitext',
			'wiki_page_name' => 'plaintext',
		);

		$index = new Search_Index_Memory;
		$indexer = new Search_Indexer($index);
		$indexer->addContentSource('wiki page', new Search_ContentSource_Static($data, $typeMap));
		$indexer->rebuild();

		$this->assertEquals(2, $index->size());

		$document = $index->getDocument(0);

		$typeFactory = $index->getTypeFactory();
		$this->assertEquals($typeFactory->identifier('wiki page'), $document['object_type']);
		$this->assertEquals($typeFactory->identifier('HomePage'), $document['object_id']);
		$this->assertEquals($typeFactory->wikitext('Hello World'), $document['wiki_content']);
		$this->assertEquals($typeFactory->timestamp($timeA), $document['modification_date']);
	}

	function testSourceAggregation()
	{
		$timeA = strtotime('2010-10-10 10:10:10');
		$timeB = strtotime('2010-10-26 12:00:00');

		$typeMap = array(
			'modification_date' => 'timestamp',
			'wiki_content' => 'wikitext',
			'wiki_page_name' => 'plaintext',
			'forum_post_title' => 'plaintext',
			'forum_post_body' => 'wikitext',
		);

		$data = array(
			'HomePage' => array('wiki_page_name' => 'HomePage', 'wiki_content' => 'Hello World', 'modification_date' => $timeA),
			'Help' => array('wiki_page_name' => 'Help', 'wiki_content' => 'None available.', 'modification_date' => $timeB),
		);

		$wikiSource = new Search_ContentSource_Static($data, $typeMap);

		$data = array(
			10 => array('forum_post_title' => 'Hello', 'forum_post_body' => 'Foobar.', 'modification_date' => $timeA),
		);

		$forumSource = new Search_ContentSource_Static($data, $typeMap);

		$index = new Search_Index_Memory;
		$indexer = new Search_Indexer($index);
		$indexer->addContentSource('wiki page', $wikiSource);
		$indexer->addContentSource('forum post', $forumSource);
		$indexer->rebuild();

		$this->assertEquals(3, $index->size());
	}
}

