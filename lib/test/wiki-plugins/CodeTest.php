<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		$this->assertEquals($expectedOutput, wikiplugin_code($data, $params));
	}

	public function provider()
	{
		return array(
			array('', '<pre class="codelisting"  dir="ltr" style="overflow:auto;" id="codebox1" >~np~~/np~</pre>'),
			array('<script>alert(document.cookie);</script>', '<pre class="codelisting"  dir="ltr" style="overflow:auto;" id="codebox2" >~np~<script>alert(document.cookie);</script>~/np~</pre>', array('ishtml' => 1)),
		);
	}
}
