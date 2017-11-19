<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alaindesilets
 * Date: 2013-10-08
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

class TikiLibTest extends TikiTestCase
{

	private $some_page_name1 = 'SomePage1';
	private $some_page_name2 = 'SomePage2';
	private $some_page_name3 = 'SomePage3';

	protected function setUp()
	{
		global $testhelpers;

		$testhelpers->simulate_tiki_script_context();
	}

	protected function tearDown()
	{
		global $testhelpers;

		$testhelpers->remove_all_versions($this->some_page_name1);
		$testhelpers->remove_all_versions($this->some_page_name2);
		$testhelpers->remove_all_versions($this->some_page_name3);

		$testhelpers->remove_all_versions('PageThatDoesntExist');


		$testhelpers->stop_simulating_tiki_script_context();
	}

	public function test__remove_all_versions__Removes_all_relations_also()
	{
		global $testhelpers;
		$relationlib = TikiLib::lib('relation');
		$tikilib = TikiLib::lib('tiki');

		$testhelpers->create_page($this->some_page_name1, 0, "Hello from " . $this->some_page_name1);
		$testhelpers->create_page($this->some_page_name2, 0, "Hello from " . $this->some_page_name2);
		$testhelpers->create_page($this->some_page_name3, 0, "Hello from " . $this->some_page_name3);

		$relation_name = 'tiki.wiki.somerelation';
		$relationlib->add_relation($relation_name, 'wiki page', $this->some_page_name1, 'wiki page', $this->some_page_name2);
		$relationlib->add_relation($relation_name, 'wiki page', $this->some_page_name3, 'wiki page', $this->some_page_name1);

		$got_relations = $relationlib->get_relations_from('wiki page', $this->some_page_name1, $relation_name);
		$this->assertEquals(
			1,
			count($got_relations),
			"Initially, there should have been 1 relation from " . $this->some_page_name1
		);
		$got_relations = $relationlib->get_relations_to('wiki page', $this->some_page_name1, $relation_name);
		$this->assertEquals(
			1,
			count($got_relations),
			"Initially, there should have been 1 relation to " . $this->some_page_name1
		);

		$tikilib->remove_all_versions($this->some_page_name1);
		$got_relations = $relationlib->get_relations_from('wiki page', $this->some_page_name1, $relation_name);
		$this->assertEquals(
			count($got_relations),
			0,
			"After deleting the page, there shouldn't be any relations left from " . $this->some_page_name1
		);
		$got_relations = $relationlib->get_relations_to('wiki page', $this->some_page_name1, $relation_name);
		$this->assertEquals(
			count($got_relations),
			0,
			"After deleting the page, there shouldn't be any relations left to " . $this->some_page_name1
		);
	}
}
