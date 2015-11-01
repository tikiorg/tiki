<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class Multilingual_Aligner_SentenceSegmentorTest extends TikiTestCase
{

	////////////////////////////////////////////////////////////////
	// Documentation tests
	//    These tests illustrate how to use this class.
	////////////////////////////////////////////////////////////////

	/**
	 * @group multilingual
	 */
	public function test_This_is_how_you_create_a_SentenceSegmentor()
	{
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
	}

	/**
	 * @group multilingual
	 */
	public function test_this_is_how_you_segment_text_into_sentences()
	{
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
		$text = "hello. world";
		$sentences = $segmentor->segment($text);
	}

	////////////////////////////////////////////////////////////////
	// Internal tests
	//    These tests check the internal workings of the class.
	////////////////////////////////////////////////////////////////


	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_period()
	{
		$text = "hello brand new. world.";
		$expSentences = array("hello brand new.", " world.");
		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with separation with period."
		);
	}

	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_question_mark()
	{
		$text = "hello? Anybody home?";
		$expSentences = array("hello?", " Anybody home?");
		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with separation with question mark."
		);
	}

	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_several_question_marks()
	{
		$text = "hello???? Anybody home?";
		$expSentences = array("hello????", " Anybody home?");
		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with separation with question mark."
		);
	}

	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_exclamation_mark()
	{
		$text = "hello! Anybody home!";
		$expSentences = array("hello!", " Anybody home!");
		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with separation with exclamation mark."
		);
	}


	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_mix_of_exclamation_and_question_marks()
	{
		$text = "hello?!? Anybody home!";
		$expSentences = array("hello?!?", " Anybody home!");

		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with separation with exclamation mark."
		);
	}


	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_empty_string()
	{
		$text = "";
		$expSentences = array();
		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with empty string."
		);
	}

	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_wiki_paragraph_break()
	{
		$text = "This sentence ends with a period and a newline.\n" .
						"This sentence has no period, but ends with a wiki paragraph break\n\n" .
						"This is the start of a new paragraph.";

		$expSentences = array(
						"This sentence ends with a period and a newline.",
						"\nThis sentence has no period, but ends with a wiki paragraph break\n\n",
						"This is the start of a new paragraph."
		);

		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with wiki paragraph break."
		);

	}

	/**
	 * @group multilingual
	 */
	public function test_segmentation_deals_with_bullet_lists()
	{
		$text = "This sentence precedes a bullet list.\n" .
					"* Bullet 1\n" .
					"** Bullet 1-1\n" .
					"* Bullet 2\n" .
					"After bullet list";

		$expSentences = array(
					"This sentence precedes a bullet list.",
					"\n",
					"* Bullet 1\n",
					"** Bullet 1-1\n",
					"* Bullet 2\nAfter bullet list");

		$this->do_test_basic_segmentation(
			$text,
			$expSentences,
			"Segmentation did not deal properly with bullet list."
		);

	}

	////////////////////////////////////////////////////////////////
	// Helper methods
	////////////////////////////////////////////////////////////////

	public function do_test_basic_segmentation($text, $expSentences, $message)
	{
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
		$sentences = $segmentor->segment($text);
		$got_sentences_as_string = implode(', ', $sentences);
		$exp_sentences_as_string = implode(', ', $expSentences);

		$this->assertEquals(
			$expSentences, $sentences,
			$message."\n".
			"Segmented sentences differed from expected.\n".
			"Expected Sentences: $exp_sentences_as_string\n".
			"Got      Sentences: $got_sentences_as_string\n"
		);
	}
}
