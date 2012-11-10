<?php

class JisonParser_HtmlToWikiTest extends JisonParser_OutputTest
{
	static $verbose = false;
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
		$parsed = self::$htmlToWikiParser->parse($actual);

		if (self::$verbose) {
			echo "\n\nWiki: '" . $actual . "'";
			echo "\n\nReversal: '" . $parsed . "'";
			echo "\n\nExpected: '" . $syntax . "'";
		}

		return parent::assertEquals($syntax, $parsed, $syntaxName, $actual);
	}
}