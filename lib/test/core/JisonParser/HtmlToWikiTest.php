<?php

class JisonParser_HtmlToWikiTest extends JisonParser_OutputTest
{
	public $verbose = true;

	function setUp()
	{
		global $prefs;
		$prefs['feature_jison_wiki_parser'] = 'y';
		$this->parser = new JisonParser_Html_Handler();
		$this->provider();
	}

	static function assertEquals($expected, $actual, $syntaxName, $syntax)
	{
		//We switch the expected with the syntax, since we are doing a reversal of html to wiki
		//print_r(array($syntax, $actual, $syntaxName, $expected));
		parent::assertEquals($syntax, $actual, "html_to_wiki:" . $syntaxName, $expected);
	}
}