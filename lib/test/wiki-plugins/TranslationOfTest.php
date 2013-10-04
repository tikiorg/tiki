<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_translationof.php');

class WikiPlugin_TranslationOfTest extends PHPUnit_Framework_TestCase
{
//    public $orig_user;
//
//    protected function setUp()
//    {
//        global $tikilib, $_SERVER, $user, $prefs, $multilinguallib;
//
//        $this->orig_user = $user;
//
//        $prefs['site_language'] = 'en';
//
//
//        /* Need to set those global vars to be able to create and delete pages */
//        $_SERVER['HTTP_HOST'] = 'localhost';
//        $_SERVER['REQUEST_URI'] = 'phpunit';
//        $user = "user_that_can_edit";
//
//        $page_name = "APageContainingATranslationOfPlugin";
//        $content = "{TranslationOf(orig_page=\"ChildPage\" translation_page=\"PageEnfant\" target_lang=\"fr\") /}";
//        $lang = 'en';
//        $tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);
//    }
//
//    protected function tearDown()
//    {
//        global $tikilib, $user;
//
//        $tikilib->remove_all_versions("APageContainingATranslationOfPlugin");
//
//        unset($_SERVER['HTTP_HOST']);
//        unset($_SERVER['REQUEST_URI']);
//        $user = $this->orig_user;
//    }

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
			array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-cluetip-body=\'<a href="tiki-edit_translation.php?page=SomePage&target_lang=fr#new_translation">Translate this link</a>\' data-cluetip-options=\'{"activation":"mouseover","sticky":true,"mouseOutClose":false,"showTitle":false,"attribute":"data-cluetip-body"}\'>SomePage</a>',
                  array('orig_page' => 'SomePage', 'translation_lang' => 'fr'),
                  "Happy Path Case"),
            array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-cluetip-body=\'<a href="tiki-edit_translation.php?page=SomePage&target_lang=fr&translation_name=UnePage#new_translation">Translate this link</a>\' data-cluetip-options=\'{"activation":"mouseover","sticky":true,"mouseOutClose":false,"showTitle":false,"attribute":"data-cluetip-body"}\'>UnePage</a>',
                  array('orig_page' => 'SomePage', 'translation_lang' => 'fr', 'translation_page' => 'UnePage'),
                  "Case with name of translated page provided"),
		);
	}

//    function test_create_page_that_contains_a_TranslationOf_plugin_creates_an_object_relation()
//    {
//        $this->fail('test incomplete');
//    }

}
