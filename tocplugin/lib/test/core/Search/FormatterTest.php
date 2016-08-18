<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_FormatterTest extends PHPUnit_Framework_TestCase
{
	function testBasicFormatter()
	{
		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=object_id} ({display name=object_type})\n");

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
				array('object_type' => 'wiki page', 'object_id' => 'SomePage'),
			)
		);

		$expect = <<<OUT
* HomePage (wiki page)
* SomePage (wiki page)

OUT;
		$this->assertEquals($expect, $output);
	}

	function testSpecifyFormatter()
	{
		global $prefs;
		$prefs['short_date_format'] = '%b %e, %Y';

		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=object_id} ({display name=modification_date format=date})\n");

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'HomePage',
					'modification_date' => strtotime('2010-10-10 10:10:10')
				),
				array(
					'object_type' => 'wiki page',
					'object_id' => 'SomePage',
					'modification_date' => strtotime('2011-11-11 11:11:11')
				),
			)
		);

		$expect = <<<OUT
* HomePage (Oct 10, 2010)
* SomePage (Nov 11, 2011)

OUT;
		$this->assertEquals($expect, $output);
	}

	function testUnknownFormattingRule()
	{
		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=object_id} ({display name=object_type format=doesnotexist})\n");

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
				array('object_type' => 'wiki page', 'object_id' => 'SomePage'),
			)
		);

		$expect = <<<OUT
* HomePage (Unknown formatting rule 'doesnotexist' for 'object_type')
* SomePage (Unknown formatting rule 'doesnotexist' for 'object_type')

OUT;
		$this->assertEquals($expect, $output);
	}

	function testValueNotFound()
	{
		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=doesnotexist} ({display name=doesnotexisteither default=Test})\n");

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(array(array('object_type' => 'wiki page', 'object_id' => 'HomePage'),));

		$expect = <<<OUT
* No value for 'doesnotexist' (Test)

OUT;
		$this->assertEquals($expect, $output);
	}

	function testBasicSmartyFormatter()
	{
		$plugin = new Search_Formatter_Plugin_SmartyTemplate(dirname(__FILE__).'/basic.tpl');
		$plugin->setData(array('foo' => array('bar' => 'baz'),));

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
				array('object_type' => 'wiki page', 'object_id' => 'SomePage'),
			)
		);

		$expect = <<<OUT
<div>~np~<table>
	<caption>baz: 2</caption>
	<tr><th>Object</th><th>Type</th></tr>
	<tr><td>HomePage</td><td>wiki page</td></tr>
	<tr><td>SomePage</td><td>wiki page</td></tr>
</table>
~/np~</div>
OUT;
		$this->assertXmlStringEqualsXmlString($expect, "<div>$output</div>");
	}

	function testForEmbeddedMode()
	{
		$plugin = new Search_Formatter_Plugin_SmartyTemplate(dirname(__FILE__).'/embedded.tpl', true);

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
				array('object_type' => 'wiki page', 'object_id' => 'SomePage'),
			)
		);

		$expect = <<<OUT
<div>~np~<table>
	<caption>Count: 2</caption>
	<tr><th>Object</th><th>Type</th></tr>
	<tr><td>HomePage</td><td>wiki page</td></tr>
	<tr><td>SomePage</td><td>wiki page</td></tr>
</table>
~/np~</div>
OUT;
		$this->assertXmlStringEqualsXmlString($expect, "<div>$output</div>");
	}

	function testAdditionalFieldDefinition()
	{
		$plugin = new Search_Formatter_Plugin_SmartyTemplate(dirname(__FILE__).'/basic.tpl');

		$formatter = new Search_Formatter($plugin);
		$formatter->addSubFormatter('object_id', new Search_Formatter_Plugin_WikiTemplate("{display name=object_id}\n{display name=description default=None}"));

		$output = $formatter->format(
			array(
				array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
				array('object_type' => 'wiki page', 'object_id' => 'SomePage', 'description' => 'About'),
			)
		);

		$expect = <<<OUT
<div>~np~<table>
	<caption>Count: 2</caption>
	<tr><th>Object</th><th>Type</th></tr>
	<tr><td>~/np~HomePage
None~np~</td><td>wiki page</td></tr>
	<tr><td>~/np~SomePage
About~np~</td><td>wiki page</td></tr>
</table>
~/np~</div>
OUT;
		$this->assertXmlStringEqualsXmlString($expect, "<div>$output</div>");
	}

	function testPaginationInformationProvided()
	{
		$this->markTestSkipped('Template issues in this context.');
		$plugin = new Search_Formatter_Plugin_SmartyTemplate(dirname(__FILE__).'/paginate.tpl');

		$formatter = new Search_Formatter($plugin);
		$output = $formatter->format(
			new Search_ResultSet(
				array(
					array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
					array('object_type' => 'wiki page', 'object_id' => 'SomePage', 'description' => 'About'),
				),
				22,
				20,
				10
			)
		);

		$this->assertContains('>1<', $output);
		$this->assertContains('>2<', $output);
		$this->assertContains('>3<', $output);
		$this->assertNotContains('>4<', $output);
	}

	function testSpecifyDataSource()
	{
		$searchResult = Search_ResultSet::create(array(
			array('object_type' => 'wiki page', 'object_id' => 'HomePage'),
			array('object_type' => 'wiki page', 'object_id' => 'SomePage'),
		));
		$withData = array(
			array('object_type' => 'wiki page', 'object_id' => 'HomePage', 'description' => 'ABC'),
			array('object_type' => 'wiki page', 'object_id' => 'SomePage', 'description' => 'DEF'),
		);

		$source = $this->getMock('Search_Formatter_DataSource_Interface');
		$source->expects($this->any())
			->method('getData')
			->will($this->returnCallback(function ($entry, $field) use (& $withData) {
				$this->assertEquals('description', $field);
				return array_shift($withData);
			}));

		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=object_id} ({display name=description})\n");

		$formatter = new Search_Formatter($plugin);
		$searchResult->applyTransform(new Search_Formatter_Transform_DynamicLoader($source));

		$output = $formatter->format($searchResult);

		$expect = <<<OUT
* HomePage (ABC)
* SomePage (DEF)

OUT;
		$this->assertEquals($expect, $output);
	}

	function testFormatValueAsLink()
	{
		global $prefs;
		$prefs['feature_sefurl'] = 'y';

		$plugin = new Search_Formatter_Plugin_WikiTemplate("* {display name=title format=objectlink}\n");

		$formatter = new Search_Formatter($plugin);

		$output = $formatter->format(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'HomePage',
					'title' => 'Home'
				),
				array(
					'object_type' => 'wiki page',
					'object_id' => 'Some Page',
					'title' => 'Test'
				),
			)
		);

		$expect = <<<OUT
* ~np~<a href="HomePage" class="" title="Home" data-type="wiki page" data-object="HomePage">Home</a>~/np~
* ~np~<a href="Some+Page" class="" title="Test" data-type="wiki page" data-object="Some Page">Test</a>~/np~

OUT;
		$this->assertEquals($expect, $output);
	}

	function testLinkInsideSmartyTemplate()
	{
		global $prefs;
		$prefs['feature_sefurl'] = 'y';

		$plugin = new Search_Formatter_Plugin_SmartyTemplate(dirname(__FILE__).'/basic.tpl');

		$formatter = new Search_Formatter($plugin);
		$formatter->addSubFormatter('object_id', new Search_Formatter_Plugin_WikiTemplate("{display name=object_id format=objectlink}"));

		$output = $formatter->format(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'HomePage'
				),
			)
		);

		$expect = <<<OUT
<div>~np~<table>
	<caption>Count: 1</caption>
	<tr><th>Object</th><th>Type</th></tr>
	<tr><td><a href="HomePage" class="" title="HomePage" data-type="wiki page" data-object="HomePage">HomePage</a></td><td>wiki page</td></tr>
</table>
~/np~</div>
OUT;
		$this->assertXmlStringEqualsXmlString($expect, "<div>$output</div>");
	}

	function testHighlightRequested()
	{
		$plugin = new Search_Formatter_Plugin_WikiTemplate('{display name=highlight}');

		$resultSet = new Search_ResultSet(
			array(
				array(
					'object_type' => 'wiki page',
					'object_id' => 'HomePage',
					'content' => 'Hello World'
				),
				array(
					'object_type' => 'wiki page',
					'object_id' => 'SomePage',
					'content' => 'Test'
				),
			),
			22,
			20,
			10
		);
		$resultSet->setHighlightHelper(new Search_FormatterTest_HighlightHelper);

		$formatter = new Search_Formatter($plugin);
		$output = $formatter->format($resultSet);

		$this->assertContains('<strong>Hello</strong>', $output);
	}
}

class Search_FormatterTest_HighlightHelper implements Zend\Filter\FilterInterface
{
	function filter($content)
	{
		return str_replace('Hello', '<strong>Hello</strong>', $content);
	}
}

