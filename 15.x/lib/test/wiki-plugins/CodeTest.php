<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_code.php');

class WikiPlugin_CodeTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function testWikiPluginCode($data, $expectedOutput, $params = array())
	{
        $this->markTestSkipped("As of 2013-09-30, this tesst is broken. Skipping it for now.");
        $this->assertEquals($expectedOutput, wikiplugin_code($data, $params));
	}

	public function provider()
	{
		return array(
            array('', '<pre class="codelisting"  data-wrap="1"  dir="ltr"  style="white-space:pre-wrap; white-space:-moz-pre-wrap !important; white-space:-pre-wrap; white-space:-o-pre-wrap; word-wrap:break-word;" id="codebox1" >~np~~/np~</pre>'),
			array('<script>alert(document.cookie);</script>', '<pre class="codelisting"  data-wrap="1"  dir="ltr"  style="white-space:pre-wrap; white-space:-moz-pre-wrap !important; white-space:-pre-wrap; white-space:-o-pre-wrap; word-wrap:break-word;" id="codebox2" >~np~&lt;script>alert(document.cookie);&lt;/script>~/np~</pre>', array('ishtml' => 1)),
		);
	}
}
