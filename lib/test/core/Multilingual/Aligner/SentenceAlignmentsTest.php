<?php

class  Multilingual_Aligner_SentenceAlignmentsTest extends TikiTestCase
{

   public function ___test_reminder()  {
      $this->fail("remember to reactivate all tests in SentenceAlignments");
   }

   public function test_nevermind()  {
      $this->fail("this test is just to verify that we are indeed executing tests from SentencedAlignmetTest");
   }


   ////////////////////////////////////////////////////////////////
   // Documentation tests
   //    These tests illustrate how to use this class.
   ////////////////////////////////////////////////////////////////
    
    public function test_this_is_how_you_create_a_SentenceAlignments() {
       $aligner = new Multilingual_Aligner_SentenceAlignments();
    }

   ////////////////////////////////////////////////////////////////
   // Note: In the rest of these tests, you can assume that 
   //       $this->alignments is an instance of SentenceAlignments
   //       created as above.
   ////////////////////////////////////////////////////////////////

   protected function setUp()  {
      $this->alignments = new Multilingual_Aligner_SentenceAlignments();
   }
     
    
   ////////////////////////////////////////////////////////////////
   // Internal tests
   //    These tests check the internal workings of the class.
   ////////////////////////////////////////////////////////////////
   
}
