<?php

error_reporting(E_ALL);

require_once 'PHPUnit/Framework.php';
require_once 'SentenceSegmentor.php';
 
class  BilingualAlignerTest extends PHPUnit_Framework_TestCase
{

   ////////////////////////////////////////////////////////////////
   // Documentation tests
   //    These tests illustrate how to use this class.
   ////////////////////////////////////////////////////////////////

   public function testThisIsHowYouCreateASentenceSegmentor() {
      $segmentor = new SentenceSegmentor();
   }
   
   public function testThisIsHowYouSegmentTextIntoSentences() {
      $segmentor = new SentenceSegmentor();
      $text = "hello. world";
      $sentences = $segmentor->segment($text);  
   }

   ////////////////////////////////////////////////////////////////
   // Internal tests
   //    These tests check the internal workings of the class.
   ////////////////////////////////////////////////////////////////

   public function test_segmentation_deals_withPeriod() {
      $text = "hello brand new. world";
      $expSentences = array("hello brand new", " world");
      $this->doTestBasicSegmentation($text, $expSentences, 
                                     "Segmentation did not deal properly with separation with period.");
   }
   
   public function testSegmentationDealsWithQuestionMark() {
      $text = "hello? Anybody home";
      $expSentences = array("hello", " Anybody home");
      $this->doTestBasicSegmentation($text, $expSentences, 
                                     "Segmentation did not deal properly with separation with question mark.");
   }   

   
   public function testSegmentationDealsWithExclamationMark() {
      $text = "hello! Anybody home";
      $expSentences = array("hello", " Anybody home");
      $this->doTestBasicSegmentation($text, $expSentences, 
                                     "Segmentation did not deal properly with separation with question mark.");
   }  
   
   public function testSegmentationDealsWithEmptyString() {
      $text = "";
      $expSentences = array("");
      $this->doTestBasicSegmentation($text, $expSentences, 
                                     "Segmentation did not deal properly with separation with question mark.");
   }     
   

   
   ////////////////////////////////////////////////////////////////
   // Helper methods
   ////////////////////////////////////////////////////////////////
   
   public function doTestBasicSegmentation($text, $expSentences, $message) {    
      $segmentor = new SentenceSegmentor();
      $sentences = $segmentor->segment($text); 
      $got_sentences_as_string = implode(', ', $sentences);
      $exp_sentences_as_string = implode(', ', $expSentences);
      $this->assertEquals($expSentences, $sentences, 
                          $message."\n".
                          "Segmented sentences differed from expected.\n".
                          "Expected Sentences: $exp_sentences_as_string\n".
                          "Got      Sentences: $got_sentences_as_string\n");      
   }
}

?>