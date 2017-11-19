<?php
use Tiki\BigBlueButton\Configuration;

class BigBlueButton_DynamicConfigurationTest extends PHPUnit_Framework_TestCase
{
	private $xml;

	function __construct()
	{
		$this->xml = file_get_contents(__DIR__ . '/config.xml');
	}

	function testPassthrough()
	{
		$config = new Configuration($this->xml);

		$this->assertXmlStringEqualsXmlString($this->xml, $config->getXml());
	}

	function testDisableModule()
	{
		$config = new Configuration($this->xml);
		$config->removeModule('PhoneModule');

		$xml = $config->getXml();
		$this->assertNotContains('<module name="PhoneModule"', $xml);
	}

	function testDisableModuleWithDependencies()
	{
		$config = new Configuration($this->xml);
		$config->removeModule('PresentModule');

		$xml = $config->getXml();
		$this->assertNotContains('<module name="PresentModule"', $xml);
		$this->assertNotContains('<module name="WhiteboardModule"', $xml);
	}
}
