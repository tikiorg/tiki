<?php
/*
 * Created on Jan 27, 2009
 *
 */
 
class Multilingual_MachineTranslation_GoogleTranslateWrapperTest extends PHPUnit_Framework_TestCase {

   function test_This_is_how_you_create_a_GoogleTranslateWrapper() {
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
   
   
   function test_This_is_how_you_translate_some_text() {
   	  $text = "Hello";
   	  $translation = $this->translator->translateText($text);
   	  $this->assertEquals($translation, "Ciao", "The translation was not correct for text: $text.");
   }

   

}
?>
