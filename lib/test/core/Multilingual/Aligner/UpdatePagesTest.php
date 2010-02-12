<?php

/**
 * @group unit
 * 
 */

//include_once "lib/core/lib/Multilingual/Aligner/SentenceAlignments.php";
//include_once "lib/core/lib/Multilingual/Aligner/UpdateSentences.php";
//include_once "lib/test/TikiTestCase.php";
class  Multilingual_Aligner_UpdatePagesTest extends TikiTestCase
{
	private $orig_source_sentences =
				array(
					"Firefox supports international characters for languages such as Hindi.",
					"You can test your Firefoxs support of Hindi scripts at BBC Hindi.",
					"Most sites that require additional fonts will have a page describing where you can get the font.",
				);
	private $orig_target_sentences =
			array(
				"Firefox supporte les caractères internationaux pour des langues tel que lindien.",
				"Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.",
				"La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police."
			 	); 
	private $extra_source_sentence = "This is an extra sentence.";
	private $extra_target_sentence = "C'est une phrase extra.";
	
	protected function setUp()  {
		$this->updater = new Multilingual_Aligner_UpdatePages();
		$this->updater->alignments = new Multilingual_Aligner_SentenceAlignments();
		$this->updater->translator=new Multilingual_Aligner_MockMTWrapper();
	} 


	public function test_reminder()  {
		$this->fail("remember to reactivate all tests in UpdateSentences");
	}

	////////////////////////////////////////////////////////////////
	// Documentation tests
	//    These tests illustrate how to use this class.
	////////////////////////////////////////////////////////////////
    
	public function test_this_is_how_you_create_a_UpdatePages() {
		$test = new Multilingual_Aligner_UpdatePages();
	}
	 	 
	////////////////////////////////////////////////////////////////
	// Note: In the rest of these tests, you can assume that 
	//       $this->updater is an instance of UpdatePages
	//       created as above.
	////////////////////////////////////////////////////////////////
	 
	public function test_This_is_how_you_AddSentenceintoSourceside() {

	// OLD WAY

		$source_lng = "en";
		$target_lng = "fr";
		
		$source_outofdate = join('', $this->orig_source_sentences);
				
		$source_modified="Firefox supports international characters for languages such as Hindi.This is a test statement. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
//		$source_modified_array = $this->orig_source_sentences;
//		$source_modified_array = $this->insertSentenceAtIndex(1, $this->extra_source_sentence, $source_modified_array);
//		$source_modified = join('', $source_modified_array);

		$target_outofdate = join('', $this->orig_target_sentences);

		$target_modified = $target_outofdate;

		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caractères internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une déclaration d'essai.";
		

		$expected_content = 
			array(
				"Firefox supporte les caractères internationaux pour des langues tel que lindien.",
			 	"Added_Source C'est une déclaration d'essai.",
			 	"Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.",
			 	"La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police."
			 	); 

		
		$final_updated=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified, $expected_content, "");
	}


	public function ___test_This_is_how_you_AddSentenceintoTragetside() {
		$source_lng="en";
		$target_lng="fr";
		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$source_modified="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_outofdate="Firefox supporte les caractères internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui décrit ou vous pouvez obtenir la police.";
		$target_modified="Firefox supporte les caractères internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. C'est une déclaration d'essai. La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police.";
		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caractères internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une déclaration d'essai.";

		$exp_content = 
			array(
				"Firefox supporte les caractères internationaux pour des langues tel que lindien.",
				"Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.",
				"C'est une déclaration d'essai. ",
				"La plupart des sites qui ont besoin de polices supplémentaires vont avoir une page qui décrit ou vous pouvez obtenir la police."
			);
			
		$final_updated=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified, $exp_content, "");
	}
	
	public function ___test_This_is_how_you_DeleteSentencefromSourceside() {
		$source_lng="en";
		$target_lng="fr";
		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$source_modified="You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_outofdate="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$target_modified="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caract�res internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une d�claration d'essai.";


		$expected_content = "Deleted_Source Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";


		$final_updated=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified, $expected_content, "");
				
	}
	
	public function ___test_This_is_how_you_DeleteSentencefromTargetside() {
		$source_lng="en";
		$target_lng="fr";
		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$source_modified="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_outofdate="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$target_modified="Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caract�res internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une déclaration d'essai.";

		$expected_content = "Deleted_Target Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";

		$final_updated=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified, $expected_content, "");
		
	}
	
	public function do_test_basic_updating($source_alignment,$target_alignment,
						$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,
						$source_outofdate,$source_modified,$target_outofdate,$target_modified,
						$expected_updated_target, $message)
	{
//		$source_outofdate = join(' ', $source_outofdate_array);
//		$source_modified = join(' ', $source_modified_array);			
//		$target_outofdate = join(' ', $target_outofdate_array);
//		$target_modified = join(' ', $target_modified_array);

//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$source_outofdate="; var_dump($source_outofdate); echo "</pre>\n";
//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$target_outofdate="; var_dump($target_outofdate); echo "</pre>\n";
//
//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$source_modified="; var_dump($source_modified); echo "</pre>\n";
//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$target_modified="; var_dump($target_modified); echo "</pre>\n";

		$this->updater->SetAlignment($source_alignment,$target_alignment,$source_lng,$target_lng);
		$this->updater->SetMT($source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng);
		$final=$this->updater->UpdatingTargetPage($source_outofdate,$source_modified,$target_outofdate,$target_modified,$source_lng,$target_lng);

//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$final="; var_dump($final); echo "</pre>\n";
//		echo "<pre>-- UpdatePagesTest.do_test_basic_updating: \$expected_updated_target="; var_dump($expected_updated_target); echo "</pre>\n";
				
		$this->assertEquals($expected_updated_target, $final, $message);
		
		return $final;
	}

	////////////////////////////////////////////////////////////////
	// Helper functions.
	////////////////////////////////////////////////////////////////

	function insertSentenceAtIndex($index, $sentenceToAdd, $sentenceList) {
		$modifiedSentenceList = array();
		$ii;
		for ($ii=0; $ii < count($sentenceList); $ii++) {
			if ($ii == $index) {
				$modifiedSentenceList[] = $sentenceToAdd;
			}
			$modifiedSentenceList[] = $sentenceList[$ii];
		}
		return $modifiedSentenceList;
	}

}

