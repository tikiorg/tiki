<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alaindesilets
 * Date: 2013-09-30
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

class MultilingualLibTest extends TikiTestCase
{
	public $orig_user;

	protected function setUp()
	{
		global $user, $prefs;
		$tikilib = TikiLib::lib('tiki');
		$multilinguallib = TikiLib::lib('multilingual');
		$this->orig_user = $user;

		$prefs['site_language'] = 'en';


		/* Need to set those global vars to be able to create and delete pages */
		$_SERVER['HTTP_HOST'] = 'localhost';
		$_SERVER['REQUEST_URI'] = 'phpunit';
		$user = "user_that_can_edit";

		$page_name = "SomePage";
		$content = "This page is in English.\n" .
				   "It contains links to ((A Page That Is Already Translated)) and ((A Page That Is NOT Already Translated)).";
		$lang = 'en';
		$tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);

		$page_name = "A Page That Is Already Translated";
		$content = "This page is already translated.";
		$lang = 'en';
		$tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);

		$targ_page = "Une page déjà traduite";
		$targ_content = "Cette page est déjà traduite";
		$targ_lang = "fr";
		$multilinguallib->createTranslationOfPage($page_name, $lang, $targ_page, $targ_lang, $targ_content);

		$page_name = "A Page That Is NOT Already Translated";
		$content = "This page is NOT already translated.";
		$lang = 'en';
		$tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);
	}

	protected function tearDown()
	{
		global $tikilib, $user;

		$tikilib->remove_all_versions("SomePage");
		$tikilib->remove_all_versions("A Page That Is Already Translated");
		$tikilib->remove_all_versions("Une page déjà traduite");
		$tikilib->remove_all_versions("A Page That Is NOT Already Translated");

		unset($_SERVER['HTTP_HOST']);
		unset($_SERVER['REQUEST_URI']);
		$user = $this->orig_user;
	}

	/**
	 * @group multilingual
	 * @dataProvider dataProvider_translateLinksInPageContent
	 */
	public function test_translateLinksInPageContent($src_content, $targ_lang, $exp_translated_content, $message)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$got_translated_content = $multilinguallib->translateLinksInPageContent($src_content, $targ_lang);

		$this->assertEquals(
			$exp_translated_content,
			$got_translated_content,
			"$message\nLinks were not properly translated in source page content."
		);
	}


	function dataProvider_translateLinksInPageContent()
	{
		return [

			["((A Page That Is Already Translated))", "fr",
				  "((Une page déjà traduite))",
				  "Case Description: Link to a page that already has a translation. The link should be " .
				  "be replaced by the link of the translation."],

			["((A Page That Is NOT Already Translated))", "fr",
				  "{TranslationOf(orig_page=\"A Page That Is NOT Already Translated\" translation_lang=\"fr\" translation_page=\"\") /}",
				  "Case Description: Link to a page that is NOT already translated. The link should be " .
					"be replaced by a {TranslationOf} plugin."],

			["((A Page That Is Already Translated|click here))", "fr",
				  "((Une page déjà traduite|click here))",
				  "Case Description: Link to a page that already has a translation, but with an anchor text override. " .
				  "The link should be replaced by a link to the translation, but anchor text should remain the same."],
		];
	}

	/**
	 * @group multilingual
	 * @dataProvider dataProvider_defaultTargetLanguageForNewTranslation
	 */
	function test_defaultTargetLanguageForNewTranslation(
		$src_lang,
		$langs_already_translated,
		$user_langs,
		$exp_lang,
		$message
	) {

		$multilinguallib = TikiLib::lib('multilingual');

		$got_lang = $multilinguallib->defaultTargetLanguageForNewTranslation($src_lang, $langs_already_translated, $user_langs);
		$this->assertEquals($got_lang, $exp_lang, $message . "\nThe default target language was not as expected.");
	}

	function dataProvider_defaultTargetLanguageForNewTranslation()
	{
		return [

			['en', ['en', 'es'], ['en', 'fr'],
				  'fr',
				  "Case Description: There is one language spoken by user, for which there is no translation. Choose that one."],

			['en', ['en', 'fr', 'es'], ['en', 'fr'],
				'',
				"Case Description: Page has already been translated to all the languages that the user speaks. In that case, we don't know which default to pick."]

		];
	}

	/**
	 * @group multilingual
	 * @dataProvider dataProvider_partiallyPretranslateContentOfPage
	 */
	function test_partiallyPretranslateContentOfPage($source_page, $targ_lang, $exp_pretranslation, $message)
	{
		$multilinguallib = TikiLib::lib('multilingual');

		$got_pretranslation = $multilinguallib->partiallyPretranslateContentOfPage($source_page, $targ_lang);
		$this->assertEquals($got_pretranslation, $exp_pretranslation, "$message\nSource page was not properly pretranslated.");
	}

	function dataProvider_partiallyPretranslateContentOfPage()
	{
		return [
			["SomePage", "fr",
				  "This page is in English.\n" .
				  "It contains links to ((Une page déjà traduite)) and {TranslationOf(orig_page=\"A Page That Is NOT Already Translated\" translation_lang=\"fr\" translation_page=\"\") /}.",
				  "Case description: Happy path."]
		];
	}
}
