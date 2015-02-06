<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/wiki-plugins/wikiplugin_translationof.php');
require_once('lib/test/TestHelpers.php');
$relationlib = TikiLib::lib('relation');

class WikiPlugin_TranslationOfTest extends TikiTestCase
{
    public $orig_user;

    private $page_containing_plugin = "PageToBeCreated";

    protected function setUp()
    {
        global $user, $prefs;
		$multilinguallib = TikiLib::lib('multilingual');
		$tikilib = TikiLib::lib('tiki');
        $this->orig_user = $user;

        $prefs['site_language'] = 'en';


        /* Need to set those global vars to be able to create and delete pages */
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = 'phpunit';
        $user = "user_that_can_edit";

        /* Remove all translationof relations */
        //

    }

    protected function tearDown()
    {
        global $tikilib, $user, $testhelpers;

        $testhelpers->remove_all_versions($this->page_containing_plugin);

        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['REQUEST_URI']);
        $user = $this->orig_user;
    }

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
			array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-content=\'<a href="tiki-edit_translation.php?page=SomePage&target_lang=fr#new_translation">Translate this link</a>\'>SomePage</a>',
                  array('orig_page' => 'SomePage', 'translation_lang' => 'fr'),
                  "Happy Path Case"),
            array('', '<a href="tiki-index.php?page=SomePage"   class="tips"" data-content=\'<a href="tiki-edit_translation.php?page=SomePage&target_lang=fr&translation_name=UnePage#new_translation">Translate this link</a>\'>UnePage</a>',
                  array('orig_page' => 'SomePage', 'translation_lang' => 'fr', 'translation_page' => 'UnePage'),
                  "Case with name of translated page provided"),
		);
	}

    public function test_create_page_that_contains_a_TranslationOf_plugin_generates_an_object_relation()
    {
        global $testhelpers;
		$tikilib = TikiLib::lib('tiki');
		$relationlib = TikiLib::lib('relation');

        // Make sure the page doesn't exist to start with.
        $tikilib->remove_all_versions($this->page_containing_plugin);

        $link_source_page = "SourcePage";
        $link_target_page = "TargetPage";

        $relation_id = $relationlib->get_relation_id('tiki.wiki.translationof', 'wiki page', $this->page_containing_plugin, 'wiki page', $link_target_page);
        $this->assertTrue($relation_id == null,
            "Before creating a page that contains a TranslationOf plugin, there should NOT have been a 'translationof' relation from $this->page_containing_plugin to $link_target_page.");

        $page_containing_plugin_content = "{TranslationOf(orig_page=\"$link_source_page\" translation_page=\"$link_target_page\") /}";
        $tikilib_class = get_class($tikilib);
        $tikilib->create_page($this->page_containing_plugin, 0, $page_containing_plugin_content, time(), "");

        $relation_id = $relationlib->get_relation_id('tiki.wiki.translationof', 'wiki page', $this->page_containing_plugin, 'wiki page', $link_target_page);
        $this->assertTrue($relation_id != null,
            "After we created a page that contains a TranslationOf plugin, there SHOULD have been a 'translationof' relation from $this->page_containing_plugin to $link_target_page.");

    }
}
