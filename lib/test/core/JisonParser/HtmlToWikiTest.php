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

		$this->syntaxSets[] = array("! WYSIWYG Sample Page
Start off by clicking \"edit\" then switching the editor to use the wysiwyg editor using the {img src=\"pics/icons/pencil_go.png\"} button on the toolbar.
!! Text formatting
__bold__
''italic''
===underlined===
::centred text::

!! Lists and table
*An unordered list item
**A subitem
And some text
*Another item
. . . and more text . . .
*Item 3
. . . and some more text, to illustrate the line spacing.

A table:
||Row One, Column One|Row One, Column Two
Row Two, Column One|Row Two, Column Two||

^A box^

A [http://tiki.org|link|nocache]

__And these are produced by wiki plugins:__

!! Plugins
!!! Quote plugin:
{QUOTE()}
Just what do you think you're doing, Dave?

__HAL, in 2001:__ A Space Odyssey (1968)
{QUOTE}

!!! Code plugin:
{CODE(caption=\"Hello World in Pascal\")}
program HelloWorld(output);
begin
  WriteLn('Hello World!');
end.
{CODE}

!!! FANCYTABLE example:
{FANCYTABLE(head=>head one~|~head two~|~head three)}cell one~|~cell two~|~cell three
row 2 cell 1~|~ row 2 cell 2~|~ row 2 cell 3{FANCYTABLE}", "");
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