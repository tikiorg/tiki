<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Yaml\Yaml;

/*
 * @group unit
 */

class Tiki_Command_ProfileExport_TrackerItemTest extends TikiTestCase
{
	protected $writer;

	function setUp()
	{
		// create a sample writer that will never write to disk (save is mocked)
		$this->writer = $this->getMockBuilder(\Tiki_Profile_Writer::class)
			->setMethods(['save'])
			->setConstructorArgs([__DIR__ . "/Fixtures", 'test'])
			->getMock();

		$this->writer->method('save')->will(
			$this->throwException(new \Exception('Tiki_Profile_Writer::save() should not be called during tests'))
		);

		parent::setUp();
	}

	/**
	 * Assertion test, uses a YAML file from disk and an YAML string, converts both as array to be able to compare
	 *
	 * @param $yamlFile
	 * @param $yamlString
	 */
	protected function assertYamlFileMatchYamlString($yamlFile, $yamlString)
	{
		$expected = Yaml::parse(file_get_contents($yamlFile));
		$result = Yaml::parse($yamlString);

		$this->assertEquals($expected, $result);
	}

	/**
	 * Simple test of a tracker item export
	 */
	public function testTrackerItemExport()
	{
		$item = [
			'trackerId' => 1,
			'itemId' => 1,
			'status' => 'o',
			'field_values' => [
				['fieldId' => 1, 'value' => 'some_value'],
			],
		];

		Tiki_Profile_InstallHandler_TrackerItem::export($this->writer, $item);

		$this->assertYamlFileMatchYamlString(
			__DIR__ . '/Fixtures/testTrackerItemExportResult.yml',
			$this->writer->dump()
		);
	}

	/**
	 * Simple tes if the tracker don't have fields to be exported
	 */
	public function testTrackerItemExportWithoutFields()
	{
		$item = [
			'trackerId' => 1,
			'itemId' => 1,
			'status' => 'o',
		];

		Tiki_Profile_InstallHandler_TrackerItem::export($this->writer, $item);

		$this->assertYamlFileMatchYamlString(
			__DIR__ . '/Fixtures/testTrackerItemExportWithoutFieldsResult.yml',
			$this->writer->dump()
		);
	}
}
