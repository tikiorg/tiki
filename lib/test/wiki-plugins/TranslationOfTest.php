<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_translationof.php');

class WikiPlugin_TranslationOfTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider provider
	 */
	public function testWikiPlugin_TranslationOf($data, $expectedOutput, $params = array(), $message = "")
	{
		$this->assertEquals($expectedOutput, wikiplugin_translationof($data, $params), $message);
	}

	public function provider()
	{
		return array(
			array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-cluetip-body=\'<a href="tiki-edit_translation.php?page=SomePage#new_translation">Translate this link</a>\' data-cluetip-options=\'{"activation":"mouseover","sticky":true,"mouseOutClose":false,"showTitle":false,"attribute":"data-cluetip-body"}\'>SomePage</a>',
                  array('source_page' => 'SomePage'),
                  "Happy Path Case"),
            array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-cluetip-body=\'<a href="tiki-edit_translation.php?page=SomePage&translation_name=UnePage#new_translation">Translate this link</a>\' data-cluetip-options=\'{"activation":"mouseover","sticky":true,"mouseOutClose":false,"showTitle":false,"attribute":"data-cluetip-body"}\'>UnePage</a>',
                  array('source_page' => 'SomePage', 'translated_anchor_text' => 'UnePage'),
                  "Case with translated anchor text provided"),
		);
	}
}
