<?php
namespace Test\ObjectSelector;

class ObjectSelectorTest extends \PHPUnit_Framework_TestCase
{
	private $selector;
	private $mock;
	
	private $calls = [];

	function setUp()
	{
		$this->selector = new \Tiki\Object\Selector($this);
	}

	function testReadEmpty()
	{
		$this->assertEquals(null, $this->selector->read(''));
	}

	function testReadObjectFromString()
	{
		$expect = new \Tiki\Object\SelectorItem($this->selector, 'wiki page', 'HomePage');
		$this->assertEquals($expect, $this->selector->read('wiki page:HomePage'));
	}

	function testReadMultiple()
	{
		$this->assertEquals([], $this->selector->readMultiple(''));
	}

	function testReadMultipleFromString()
	{
		$this->assertEquals([
			$this->selector->read('wiki page:HomePage'),
			$this->selector->read('trackeritem:12'),
		], $this->selector->readMultiple("wiki page:HomePage\r\ntrackeritem:12\r\n"));
	}

	function testReadMultipleFromArray()
	{
		$this->assertEquals([
			$this->selector->read('wiki page:HomePage'),
			$this->selector->read('trackeritem:12'),
		], $this->selector->readMultiple([
			'wiki page:HomePage',
			'trackeritem:12',
		]));
	}

	function testExcludeDuplicates()
	{
		$this->assertEquals([
			$this->selector->read('trackeritem:12'),
		], $this->selector->readMultiple([
			'trackeritem:12',
			'trackeritem:12',
		]));
	}

	function testObtainTitle()
	{
		$object = $this->selector->read('trackeritem:12');

		$this->assertEquals('Foobar', $object->getTitle());
	}

	function testArrayAccess()
	{
		$object = $this->selector->read('trackeritem:12');

		$this->assertEquals('trackeritem', $object['type']);
		$this->assertEquals('12', $object['id']);
		$this->assertEquals('Foobar', $object['title']);
		$this->assertEquals('trackeritem:12', (string) $object);
	}

	function testReadMultipleSimpleOnEmpty()
	{
		$this->assertEquals([], $this->selector->readMultipleSimple('trackeritem', '', ','));
	}

	function testReadMultipleSimpleFromString()
	{
		$this->assertEquals([
			$this->selector->read('trackeritem:14'),
			$this->selector->read('trackeritem:12'),
		], $this->selector->readMultipleSimple('trackeritem', '14:12', ':'));
	}

	function testReadMultipleSimpleEliminatesDuplicates()
	{
		$this->assertEquals([
			$this->selector->read('trackeritem:14'),
		], $this->selector->readMultipleSimple('trackeritem', '14,14', ','));
	}

	function testReadMultipleSimpleHandlesArrays()
	{
		$this->assertEquals([
			$this->selector->read('trackeritem:14'),
			$this->selector->read('trackeritem:12'),
		], $this->selector->readMultipleSimple('trackeritem', ['14', '12'], ':'));
	}

	function get_title($type, $id)
	{
		return 'Foobar';
	}
}
