<?php

error_reporting(E_ALL);

require_once 'PHPUnit/Framework.php';
require_once 'BilingualAligner.php';
 
class  BilingualAlignerTest extends PHPUnit_Framework_TestCase
{

   ////////////////////////////////////////////////////////////////
   // Documentation tests
   //    These tests illustrate how to use this class.
   ////////////////////////////////////////////////////////////////
    
    public function test_this_is_how_you_create_a_BilingualAligner() {
       $aligner = new BilingualAligner();
    }

   ////////////////////////////////////////////////////////////////
   // Note: In the rest of these tests, you can assume that 
   //       $this->aligner is an instance of BilingualAligner
   //       created as above.
   ////////////////////////////////////////////////////////////////

   protected function setUp()  {
      $this->aligner = new BilingualAligner();
   }
     
    public function test_this_is_how_you_align_two_texts() {
       $aligner = new BilingualAligner();
       $en_entences = array("Hello earthlings. Take me to your leader.");
       $fr_sentences = array("Bonjour terriens. Inutile de résister. Amenez moi à votre chef.");
       $aligned_sentences = $aligner->align($en_entences, $fr_sentences);
       $first_pair = $aligned_sentences[0];
       $first_en_sent = $first_pair[0];
       $first_fr_sent = $first_pair[1];
    }
   
   ////////////////////////////////////////////////////////////////
   // Internal tests
   //    These tests check the internal workings of the class.
   ////////////////////////////////////////////////////////////////
   
   public function test__segment_into_sentences() {
      $text = "This is sentence 1! This is sentence 2\n* This is sentence 3";
      $got_sentences = $this->aligner->_segment_into_sentences($text);
      $exp_sentences = array("This is sentence 1!",
                             " This is sentence 2\n",
                             "* This is sentence 3");
      $this->assertEquals($exp_sentences, $got_sentences, 
                          "Sentences were not properly segmented"); 
   }

   public function test__segment_parallel_texts_to_sentences() {
      $l1_text = "This is sentence 1! This is sentence 2.";
      $l2_text = "Voici la phrase 1! Voici la phrase 2.";
      
      $exp_l1_sentences = array("This is sentence 1!", " This is sentence 2.");
      $exp_l2_sentences = array("Voici la phrase 1!", " Voici la phrase 2.");

      $this->aligner->_segment_parallel_texts_to_sentences($l1_text, $l2_text);

      $this->assertEquals($exp_l1_sentences, $this->aligner->l1_sentences, 
                          "L1 sentences not generated properly.");
      $this->assertEquals($exp_l2_sentences, $this->aligner->l2_sentences, 
                          "L2 sentences not generated properly.");

   }
   
   public function test__sentence_length_delta() {
      
      $l1_sentence = "Hello world.";
      $l2_sentence = "Bonjour le monde.";
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 0.417,
                 "Bad delta for case with two non-empty sentences.");
                 
      $l1_sentence = "";
      $l2_sentence = "Bonjour le monde.";
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 1,
                 "Bad delta for case with only L1 sentence empty.");
      
      $l1_sentence = "Hello world.";
      $l2_sentence = "";
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 1,
                 "Bad delta for case with only L2 sentence empty.");
                 
      $l1_sentence = "";
      $l2_sentence = "";
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, -0,
                 "Bad delta for case with both sentences empty.");

      $l1_sentence = null;
      $l2_sentence = "Bonjour le monde.";
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 1,
                 "Bad delta for case with only L1 sentence null.");
      
      $l1_sentence = "Hello world.";
      $l2_sentence = null;
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 1,
                 "Bad delta for case with only L2 sentence null.");
                 
      $l1_sentence = null;
      $l2_sentence = null;
      $this->assert_sentence_length_delta_is($l1_sentence, $l2_sentence, 0,
                 "Bad delta for case with both sentences null.");   
   }
   
   ////////////////////////////////////////////////////////////////
   // Helper methods
   ////////////////////////////////////////////////////////////////

   public function assert_sentence_length_delta_is($l1_sentence, $l2_sentence, $exp_delta, $message) {
      $got_delta = $this->aligner->_sentence_length_delta($l1_sentence, $l2_sentence);
      $message = $message."\nSentence length delta was wrong.";
      $this->assertEquals($exp_delta, $got_delta, $message, 0.001);
   }
}
?>