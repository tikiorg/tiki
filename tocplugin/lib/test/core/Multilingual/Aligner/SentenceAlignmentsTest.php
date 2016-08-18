<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 * 
 */

class  Multilingual_Aligner_SentenceAlignmentsTest extends TikiTestCase
{

	public function ___test_reminder()
	{
		$this->fail("remember to reactivate all tests in SentenceAlignments");
	}

	protected function setUp()
	{
		$this->alignments = new Multilingual_Aligner_SentenceAlignments();
	}     

	////////////////////////////////////////////////////////////////
	// Documentation tests
	//    These tests illustrate how to use this class.
	////////////////////////////////////////////////////////////////

	/**
	 * @group multilingual
	 */ 
	public function test_this_is_how_you_create_a_SentenceAlignments()
	{
		$aligner = new Multilingual_Aligner_SentenceAlignments();
	}

	////////////////////////////////////////////////////////////////
	// Note: In the rest of these tests, you can assume that 
	//       $this->alignments is an instance of SentenceAlignments
	//       created as above.
	////////////////////////////////////////////////////////////////

	/*
	 * In the remainder of these tests, you can assume that 
	 * $this->alignments alignments contains an instance of
	 * SentenceAligners built as in the above test.
	 */ 
	/**
	 * @group multilingual
	 */ 
	public function test_This_is_how_you_add_sentences()
	{
		$en_sentence = "hello world";
		$fr_sentence = "bonjour le monde";
		$this->alignments->addSentencePair($en_sentence, 'en', $fr_sentence, 'fr');
	}

	/**
	 * @group multilingual
	 */ 
	public function __test_This_is_how_you_retrieve_a_sentence_in_the_other_language ()
	{
		$en_sentence = "hello world";
		$fr_sentence = $this->alignments->getSentenceInOtherLanguage($en_sentence, 'en');
	}



	////////////////////////////////////////////////////////////////
	// Internal tests
	//    These tests check the internal workings of the class.
	////////////////////////////////////////////////////////////////

}
