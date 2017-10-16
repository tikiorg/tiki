<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Tests\Yaml;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Yaml\Yaml;
use Tiki\Yaml\Directives as Directives;

class DirectivesTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Directives
	 */
	protected $directives;
	protected $fixtures;

	public function setUp()
	{
		$this->fixtures = __DIR__ . '/Fixtures/';
		$this->directives = new Directives(null, $this->fixtures);
	}

	public function testNoChangeIfNoDirective()
	{
		$yamlString = file_get_contents($this->fixtures . 'no_directives.yml');
		$yaml = Yaml::parse($yamlString);

		$yamlJson = json_encode($yaml);
		$this->directives->process($yaml);
		$this->assertEquals($yamlJson, json_encode($yaml));
	}

	/**
	 * @dataProvider includeDataProvider
	 */
	public function testInclude($yamlFile, $yamlResultFile)
	{
		$yamlString = file_get_contents($this->fixtures . $yamlFile);
		$yaml = Yaml::parse($yamlString);

		$yamlResultString = file_get_contents($this->fixtures . $yamlResultFile);
		$yamlResult = Yaml::parse($yamlResultString);


		$this->directives->process($yaml);
		$this->assertEquals(json_encode($yamlResult), json_encode($yaml));
	}

	public function includeDataProvider()
	{
		return array(
			array('include_replace_key.yml', 'include_replace_key_result.yml'),
			array('include_replace_key_2.yml', 'include_replace_key_result.yml'),
			array('include_replace_deep_key.yml', 'include_replace_deep_key_result.yml'),
			array('include_appending.yml', 'include_appending_result.yml'),
		);
	}
}