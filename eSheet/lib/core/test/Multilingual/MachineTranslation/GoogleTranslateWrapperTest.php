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
   
   public function test_translate_text_with_up_to_1800_chars() {
   	  $text = str_repeat("Hello world! How are you? ",59); //max url: 2065 chars; urlencoded string: 1980 (keep it at 1800 just in case)
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Ciao mondo! Come stai? ",59)), $translation, "The translation was not correct for text of 1800 chars.");
   }
   
     public function test_translate_text_with_more_than_1800_chars() {
   	  $text = str_repeat("Hello world! How are you? ",63); 
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals(trim(str_repeat("Ciao mondo! Come stai? ",63)), $translation, "The translation was not correct for text of more than 1800 chars.");
   }

   

}
?>
