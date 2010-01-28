<?php

/*
 * Test groups that this PHPUnit test belongs to
 * 
 * @group unit
 * 
 */

//include_once "lib/core/lib/Multilingual/Aligner/SentenceAlignments.php";
//include_once "lib/core/lib/Multilingual/Aligner/UpdateSentences.php";
//include_once "lib/test/TikiTestCase.php";
class  Multilingual_Aligner_UpdatePagesTest extends TikiTestCase
{

	public function ___test_reminder()  {
		$this->fail("remember to reactivate all tests in UpdateSentences");
	}
	
	protected function setUp()  {
		$this->updater = new Multilingual_Aligner_UpdatePages();
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
		$source_lng = "en";
		$target_lng = "fr";
		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$source_modified="Firefox supports international characters for languages such as Hindi.This is a test statement. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_outofdate="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$target_modified="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caract�res internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une d�claration d'essai.";
		
		$final=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified);

		/*
		$content = implode(' ',$final);
		echo "final in test#####<br/>";
		foreach($final_updated as $item) {
			echo "sentence-> ".$item."<br/>";
		}
		
		$this->assertEquals("Firefox supporte les caract�res internationaux pour des langues tel que lindien. Added_Source This is a test statement. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.", $content, "error while adding a sentence in to source side");
		*/
	}


	public function ___test_This_is_how_you_AddSentenceintoTragetside() {
		$source_lng="en";
		$target_lng="fr";
		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$source_modified="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_outofdate="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$target_modified="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. C'est une d�claration d'essai.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
		$target_alignment="Firefox supporte les caract�res internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
		
		$source_Mtranslation="This is a test statement.";
		$target_Mtranslation="C'est une d�claration d'essai.";
		$final=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified);
		$content = implode(' ',$final);
		echo "final in test#####<br/>";
		foreach($final_updated as $item)
		echo "sentence-> ".$item."<br/>";
		
		$this->assertEquals("Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. C'est une d�claration d'essai. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.", $content, "error while adding a sentence in to target side");
		
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
		$final=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified);
		$content = implode(' ',$final);
		echo "final in test#####<br/>";
		foreach($final_updated as $item)
		echo "sentence-> ".$item."<br/>";
		
		$this->assertEquals("Deleted_Source Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.", $content, "error while deleting a sentence from source side");
		
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
		$target_Mtranslation="C'est une d�claration d'essai.";
		$final=$this->do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified);
		$content = implode(' ',$final);
		echo "final in test#####<br/>";
		foreach($final_updated as $item)
		echo "sentence-> ".$item."<br/>";
		
		$this->assertEquals("Deleted_Target Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.", $content, "error while deleting a sentence from target side");
		
	}
	
	public function do_test_basic_updating($source_alignment,$target_alignment,$source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng,$source_outofdate,$source_modified,$target_outofdate,$target_modified)
	{
		$this->updater->SetAlignment($source_alignment,$target_alignment,$source_lng,$target_lng);
		$this->updater->SetMT($source_Mtranslation,$target_Mtranslation,$source_lng,$target_lng);
		$final=$this->updater->UpdatingTargetPage($source_outofdate,$source_modified,$target_outofdate,$target_modified,$source_lng,$target_lng);
		return $final;
	}
}

