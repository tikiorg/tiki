<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
class Multilingual_MachineTranslation_GoogleTranslateWrapperTest extends TikiTestCase {
	
//  protected $backupGlobals = FALSE;

   public function test_This_is_how_you_create_a_GoogleTranslateWrapper() {
      $source_lang = 'en';
      $target_lang = 'it'; 	   	
      $translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang,$target_lang);
   }
   

   ////////////////////////////////////////////////////////////////
   // Note: In the rest of these tests, you can assume that 
   //       $this->aligner is an instance of GoogleTranslateWrapper 
   //       created as above.
   ////////////////////////////////////////////////////////////////


   protected function setUp()  {
      $source_lang = 'en';
      $target_lang = 'it'; 	
      $this->translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($source_lang,$target_lang);
   }
   
   
   public function test_This_is_how_you_translate_some_text() {
   	  $text = "Hello";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("Ciao", $translation, "The translation was not correct for text: $text.");
   }

   public function test_This_is_how_you_translate_sentence_by_sentence() {
   	  $text = "Hello world! How are you?";
   	  $translation = $this->translator->translateSentenceBySentence($text);
   	  $this->assertEquals("Ciao mondo!Come stai?", $translation, "The translation was not correct for text: $text.");
   }
   
   public function test_translate_text_that_translates_into_accentuated_text() {
   	  $text = "Nothing in the world is ever completely wrong; even a stopped clock is right twice a day.";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno.", $translation, "The translation was not correct for text that translates into text that contains accentuated chars.");
   }

   public function test_translate_text_with_up_to_1800_chars() {
   	  $text = str_repeat("Nothing in the world is ever completely wrong; even a stopped clock is right twice a day. ",19); //max url: 2065 chars; urlencoded string: 1980
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno. ",19)), $translation, "The translation was not correct for text of 1800 chars.");
   }
   
   public function test_translate_text_with_more_than_1800_chars() {
   	  $text = str_repeat("Nothing in the world is ever completely wrong; even a stopped clock is right twice a day. ",24); 
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Niente al mondo è mai completamente sbagliato; fermato anche un orologio è giusto due volte al giorno. ",24)), $translation, "The translation was not correct for text of 1800 chars.");
   }

   

}
?>
