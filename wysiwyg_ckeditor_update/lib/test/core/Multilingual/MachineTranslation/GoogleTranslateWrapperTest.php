<?php

/**
 * @group unit
 * @group multilingual
 * @group GoogleTranslate
 * 
 */
 
class Multilingual_MachineTranslation_GoogleTranslateWrapperTest extends TikiTestCase
{
	
//  protected $backupGlobals = FALSE;

   /**
    * @group multilingual
    */ 
   public function _test_This_is_how_you_create_a_GoogleTranslateWrapper() {
      $source_lang = 'en';
      $target_lang = 'it'; 	   	
      $translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang,$target_lang, false);
   }
   

   ////////////////////////////////////////////////////////////////
   // Note: In the rest of these tests, you can assume that 
   //       $this->translator is an instance of GoogleTranslateWrapper 
   //       created as above.
   ////////////////////////////////////////////////////////////////


   protected function setUp()  {
      $source_lang = 'en';
      $target_lang = 'it'; 	
      //3rd param false for translating wiki syntax, true for html
      $this->translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang,$target_lang, true);
   }
   
   
   public function _test_This_is_how_you_translate_some_text() {
   	  $text = "Hello";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("Ciao", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_This_is_how_you_translate_sentence_by_sentence() {
   	  $text = "Hello world! How are you?";
   	  $translation = $this->translator->translateSentenceBySentence($text);
   	  $this->assertEquals("Ciao mondo!Come stai?", $translation, "The translation was not correct for text: $text.");
   }
   
   public function _test_translate_text_that_translates_into_accentuated_text() {
   	  $text = "Nothing in the world is ever completely wrong; even a stopped clock is right twice a day.";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno.", $translation, "The translation was not correct for text that translates into text that contains accentuated chars.");
   }

   public function _test_translate_text_with_up_to_1800_chars() {
   	  $text = str_repeat("Nothing in the world is ever completely wrong; even a stopped clock is right twice a day. ",19); //max url: 2065 chars; urlencoded string: 1980
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno. ",19)), $translation, "The translation was not correct for text of 1800 chars.");
   }
   
   public function _test_translate_text_with_more_than_1800_chars() {
   	  $text = str_repeat("Nothing in the world is ever completely wrong; even a stopped clock is right twice a day. ",24); 
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno. ",24)), $translation, "The translation was not correct for text of 1800 chars.");
   }

   public function _test_This_is_how_you_translate_some_text2() {
   	  $text = "split";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("dividere", $translation, "The translation was not correct for text: $text.");
   }


////////////////////////////////////////////////
//
//  Tests for machine translating html content
//
///////////////////////////////////////////////


   /**
    * @group multilingual
    */ 
   public function test_Google_should_not_translate_html_syntax() {
   	  $text = "<a href='blah'>Hello world</a>";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertRegExp("/<a href='blah'>\s?Ciao mondo<\/a>/", $translation, "The translation was not correct for text: $text.");
   }


   /**
    * @group multilingual
    */ 
	public function test_Google_should_not_translate_more_complicated_html() {
	  $text = "<strong><a title='refresh' accesskey='2' href='tiki-index.php?page=Hello+World'>Hello World</a></strong>";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("<strong><a title='refresh' accesskey='2' href='tiki-index.php?page=Hello+World'>Ciao Mondo</a></strong>", $translation, "The translation was not correct for text: $text.");

	}

   /**
    * @group multilingual
    */ 
	public function test_that_ul_tag_gets_translated_properly() {
	  $text = "<ul><li>You want to get started quickly<br /></li></ul>";
	  $translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper('en','fr');
	  $translation = $translator->translateText($text);
   	  $this->assertEquals('<ul><li>Vous voulez démarrer rapidement<br /></li></ul>.', $translation, "The translation was not correct for text: $text");
	}

   /**
    * @group multilingual
    */ 
	public function test_that_parens_stay_after_translation() {
		$text = 'profile (<a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">install profile now</a>)';
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper('en','fr');
	  	$translation = $translator->translateText($text);
   	  	$this->assertEquals(strtolower('voir le profil (<a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">profil installer maintenant</a>)'), strtolower($translation), "The translation was not correct for text: $text.");
	}
	
   /**
    * @group multilingual
    */ 
	public function test_strong_html_tag_renders_well_after_translation() {
		$text = 'different ways to <strong>Get Started</strong> with Tiki';
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper('en','fr');
	  	$translation = $translator->translateText($text);
		$this->assertEquals('différentes façons de <strong>Get Started</strong> avec Tiki', $translation, "The translation was not correct for text: $text.");
	}
	
   /**
    * @group multilingual
    */ 
	public function test_english_one_title_gets_translated() {
		$text = '<h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">Get Started using Admin Panel</a><br /></h3>';
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper('en','fr');
	  	$translation = $translator->translateText($text);
		$this->assertEquals('<h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">commencer à utiliser le panneau admin</a><br /></h3>', $translation, "The translation was not correct for text: $text.");	
	}


   /**
    * @group multilingual
    */ 
	public function test_english_titles_get_translated() {
		$text = '<h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">Get Started using Admin Panel</a><br /></h3><h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">Get Started using Profiles</a><br /></h3>';
		$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper('en','fr');
	  	$translation = $translator->translateText($text);
		$this->assertEquals('<h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">commencer à utiliser le panneau admin</a><br /></h3><h3 class="showhide_heading" id="Get_Started_using_Profiles"><a class="wiki"  href="tiki-admin.php?profile=&amp;category=Featured+profiles&amp;repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&amp;preloadlist=y&amp;page=profiles&amp;list=List#profile-results" rel="">commencer à utiliser les profils</a><br /></h3>', $translation, "The translation was not correct for text: $text.");	
	}

////////////////////////////////////////////////
//
//  Tests for machine translating wiki syntax
//
///////////////////////////////////////////////

   public function _test_Google_should_not_translate_wiki_plugin_markup() {
   	  $text = "Hello{SPLIT}world";
   	  $translation = $this->translator->translateText($text);
	  $this->assertEquals("Ciao{SPLIT}mondo", $translation, "The translation was not correct for text: $text.");
   }
   

   public function _test_Google_should_not_translate_wiki_syntax_UNDERLINE() {
   	  $text = "===Hello===";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("===Ciao===", $translation, "The translation was not correct for text: $text.");
   }


   public function _test_Google_should_not_translate_wiki_syntax_TWO_WORDS_UNDERLINED() {
   	  $text = "===Hello world===";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("===Ciao mondo===", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_MONOSPACED_TEXT() {
   	  $text = "-+Hello world+-";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("-+Ciao mondo+-", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_BULLET() {
   	  $text = "*Hello world";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertRegExp("/\*\s?Ciao mondo/", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_INDENTED_TEXT() {
   	  $text = ";Hello world: Hello world";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(";Ciao mondo: Ciao mondo", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_LINK() {
   	  $text = "((Hello World))";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("((Ciao Mondo))", $translation, "The translation was not correct for text: $text.");
   }
   
   public function _test_Google_should_no_translate_wiki_syntax_NO_WIKIWORD() {
   	  $text = "This is the default ))HomePage((.<br />And some other text.";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("Questa è l'impostazione predefinita ))HomePage((.\ne di alcuni altri testi.", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_LINK_TO_A_SITE() {
   	  $text = "[doc.tiki.org|Tiki Documentation]";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("[doc.tiki.org|Tiki Documentazione]", $translation, "The translation was not correct for text: $text.");
   }
   
   public function _test_Google_should_not_translate_wiki_syntax_LINK_TO_A_SITE2() {
   	  $text = "[hhtp://www.something.com/some/thing]";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("[hhtp://www.something.com/some/thing]", $translation, "The translation was not correct for text: $text.");
   }
   
   
   public function _test_Google_should_not_translate_wiki_syntax_TIKI_COMMENT() {
   	  $text = "~tc~Hello world~/tc~";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("~tc~Hello world~/tc~", $translation, "The translation was not correct for text: $text.");
   }
   
   public function _test_Google_should_not_translate_wiki_syntax_HTML_COMMENT() {
   	  $text = "~hc~Hello world~/hc~";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("~hc~Hello world~/hc~", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_HORIZONTAL_SPACE() {
   	  $text = "~hs~Hello world";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("~hs~Ciao mondo", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_HEADING() {
   	  $text = "!!!#Hello world";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("!!!#Ciao mondo", $translation, "The translation was not correct for text: $text.");
   }

   public function _test_Google_should_not_translate_wiki_syntax_COLOURS() {
   	  $text = "~~blue:Hello world~~";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("~~blue:Ciao mondo~~", $translation, "The translation was not correct for text: $text.");
   }
 
   public function _test_Google_should_not_translate_wiki_syntax_BOLD() {
   	  $text = "* __{* comment *}__";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("* __{* comment *}__", $translation, "The translation was not correct for text: $text.");
   }
   
   public function _test_Google_should_not_translate_wiki_syntax_IN_THE_MIDDLE() {
	  $text = "Hello __beautiful__ world";
	  $translation = $this->translator->translateText($text);
	  $this->assertEquals("Ciao __bella__ mondo", $translation, "The translation was not correct for text: $text.");
   }
   	


}
