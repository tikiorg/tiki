<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_OutputTest extends TikiTestCase
{
	private $called;
	private $parser;

	private $syntaxSets = array(
		'italic'            => array("''italic''", '<i>italic</i>'),
		'bold'              => array('__bold__', '<strong>bold</strong>'),
		'linethrough'       => array('--linethrough--', '<strike>linethrough</strike>'),
		'horizontal line'   => array('---', '<hr />'),
		'box'               => array('^box^', '<div class="simplebox">box</div>'),
		'center'            => array('::center::', '<center>center</center>'),
		'color text1'       => array('~~red:color text~~', '<span style="color: red;">color text</span>'),
		'color text2'       => array('~~#ff00ff:color text2~~', '<span style="color: #ff00ff;">color text2</span>'),
		'simple break'      => array("\nTest\n", "<br />Test<br />"),
		'htmllink'          => array("[www.google.com|Google]", '<a href="http://www.google.com">Google</a>'),
		'wikilink'          => array("((Wiki Page))", '<a href="tiki-index.php?page=Wiki Page">Wiki Page</a>'),
		'table'             => array("||A1|B1|C1\nA2|B2|C2||", '<table class="wikitable"><tr><td class="wikicell">A1</td><td class="wikicell">B1</td><td class="wikicell">C1</td></tr><tr><td class="wikicell">A2</td><td class="wikicell">B2</td><td class="wikicell">C2</td></tr></table>'),

		'np'                => array('~np~~np~--np--~/np~~/np~', '~np~--np--~/np~'),
		'tc'                => array('~tc~Tiki Comment~/tc~', ''),

		'header1'           => array('!header1', '<h1 class="showhide_heading" id="header1">header1</h1>'),
		'header2'           => array('!!header2', '<h2 class="showhide_heading" id="header2">header2</h2>'),
		'header3'           => array('!!!header3', '<h3 class="showhide_heading" id="header3">header3</h3>'),
		'header4'           => array('!!!!header4', '<h4 class="showhide_heading" id="header4">header4</h4>'),
		'header5'           => array('!!!!!header5', '<h5 class="showhide_heading" id="header5">header5</h5>'),
		'header6'           => array('!!!!!!header6', '<h6 class="showhide_heading" id="header6">header6</h6>'),
		'header7'           => array('!!!!!!!header7', '<h6 class="showhide_heading" id="header7">header7</h6>'),

		'list1'             => array(),
		'list2'             => array(),
		'list3'             => array(),
		'list4'             => array(),

		'listnested1'       => array(),
		'listnested2'       => array(),
	);

	function setUp()
	{
		$this->called = 0;
		$this->parser = new JisonParser_Wiki_Handler();
		$this->parser->runningTest();
	}

	public function testOutput() {
		foreach($this->syntaxSets as $syntaxName => $syntax) {
			if (isset($syntax[0])) {
				$parsed = $this->parser->parse($syntax[0]);
			} else {
				$customHandled = $this->$syntaxName();
				$parsed = $customHandled['parsed'];
				$syntax = $customHandled['syntax'];
			}

			echo "\ntesting:\t\t" . $syntaxName . ",\t\t";

			//echo "out:  " .$parsed;

			$parsed = trim($parsed);
			if ($parsed != $syntax[1]) {
				echo "fail";
				$this->fail("Syntax failure: " . $syntaxName . " \nExpected:" . $syntax[1] . "\nGot:" . $parsed);
			} else {
				echo "passed";
			}
		}
	}

	private function tryRemoveIdsFromHtmlList(&$parsed)
	{
		$parsed = preg_replace('/id="id[0-9]+"/', 'id=""', $parsed);
	}

	private function list1()
	{
		$syntax = array(
			"*line 1\n*line 2\n*line 3",
			'<ul class="tikiList" id="" style=""><li class="tikiListItem">line 1</li><li class="tikiListItem">line 2</li><li class="tikiListItem">line 3</li></ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	private function list2()
	{
		$syntax = array(
			"#line 1\n#line 2\n#line 3",
			'<ol class="tikiList" id="" style=""><li class="tikiListItem">line 1</li><li class="tikiListItem">line 2</li><li class="tikiListItem">line 3</li></ol>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	private function list3()
	{
		$syntax = array(
			"*line 1\n*line 2\n+line 3",
			'<ul class="tikiList" id="" style=""><li class="tikiListItem">line 1</li><li class="tikiListItem">line 2</li><div class="tikiUnlistItem">line 3</div></ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	private function list4()
	{
		$syntax = array(
			"+line 1\n*line 2\n+line 3",
			'<ul class="tikiList" id="" style=""><div class="tikiUnlistItem">line 1</div><li class="tikiListItem">line 2</li><div class="tikiUnlistItem">line 3</div></ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	private function listnested1()
	{
		$syntax = array(
			"*line 1\n**line 2\n***line 3\n**line 4\n*line 5\n**line 6\n***line 7\n**line 8\n*line 9",
			'<ul class="tikiList" id="" style=""><li class="tikiListItem">line 1<ul class="tikiList" id="" style=""><li class="tikiListItem">line 2<ul class="tikiList" id="" style=""><li class="tikiListItem">line 3</li></ul></li><li class="tikiListItem">line 4</li></ul></li><li class="tikiListItem">line 5<ul class="tikiList" id="" style=""><li class="tikiListItem">line 6<ul class="tikiList" id="" style=""><li class="tikiListItem">line 7</li></ul></li><li class="tikiListItem">line 8</li></ul></li><li class="tikiListItem">line 9</li></ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}

	private function listnested2()
	{
		$syntax = array(
			"*line 1\n##line 2\n##line 3\n**-line 4",
			'<ul class="tikiList" id="" style=""><li class="tikiListItem">line 1<a id="fillerid" href="javascript:flipWithSign(' . "''" . ');" class="link">[+]</a><ol class="tikiList" id="" style="display: none;"><li class="tikiListItem">line 2</li><li class="tikiListItem">line 3</li><li class="tikiListItem">line 4</li></ol></li></ul>'
		);

		$parsed = $this->parser->parse($syntax[0]);
		$this->tryRemoveIdsFromHtmlList($parsed);
		$parsed = preg_replace('/flipperid[0-9]+/', 'fillerid', $parsed);
		$parsed = preg_replace("/flipWithSign[(][']id[0-9]+['][)]/", "flipWithSign('')", $parsed);

		return array("parsed" => $parsed, "syntax" => $syntax);
	}
}

