<?php

class JisonParser_HtmlToWikiTest extends JisonParser_OutputTest
{
	public $verbose = false;
	public static $htmlToWikiParser;

	function setUp()
	{
		global $prefs;
		$prefs['feature_jison_wiki_parser'] = 'y';
		$this->parser = new JisonParser_WikiCKEditor_Handler();
		self::$htmlToWikiParser = new JisonParser_Html_Handler();
		$this->provider();
	}

	static function assertEquals($expected, $actual, $syntaxName, $syntax)
	{
		file_put_contents("/var/www/parsetest.log", $actual);

		$actual = self::$htmlToWikiParser->parse($actual);
		echo "\n\nReversal: '" . $actual . "'";
		echo "\n\nExpected: '" . $syntax . "'";

		return parent::assertEquals($syntax, $actual, $syntaxName, $actual);
	}
}