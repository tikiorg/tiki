<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once 'UpdateSentences.php';
include_once 'SentenceSegmentor.php';
include_once 'SentenceAlignments.php';
include_once 'MockMTWrapper.php';

class Multilingual_Aligner_UpdatePages
{

	public $alignments = null;

	public function setUp()
	{
		$this->alignments = new Multilingual_Aligner_SentenceAlignments();
		$this->translator = new Multilingual_Aligner_MockMTWrapper();
	}

	public function UpdatingTargetPage($source_outofdate, $source_modified, $target_outofdate, $target_modified, $source_lng, $target_lng)
	{
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();

		$source_outofdate_string = $source_outofdate;
		$source_modified_string = $source_modified;

		$target_outofdate_string = $target_outofdate;
		$target_modified_string = $target_modified;

		$source_outofdate_sentences = $segmentor->segment($source_outofdate_string);
		$source_modified_sentences = $segmentor->segment($source_modified_string);

		$target_outofdate_sentences = $segmentor->segment($target_outofdate_string);
		$target_modified_sentences = $segmentor->segment($target_modified_string);
		$target_modified_sentences[count($target_modified_sentences)] = 'dummy';
		$i = -1;
		for ($a=0; $a<count($source_outofdate_sentences); $a++) {
			$source_outofdate_sentences[$a] = trim($source_outofdate_sentences[$a]);
		}
		
		for ($a=0; $a<count($source_modified_sentences); $a++) {
			$source_modified_sentences[$a] = trim($source_modified_sentences[$a]);
		}

		for ($a=0; $a<count($target_outofdate_sentences); $a++) {
			$target_outofdate_sentences[$a] = trim($target_outofdate_sentences[$a]);
		}

		for($a=0; $a<count($target_modified_sentences); $a++) {
			$target_modified_sentences[$a] = trim($target_modified_sentences[$a]);
		}

		$update = new Multilingual_Aligner_UpdateSentences1();
		$source_diff = $update->DifferencebetweenOriginalFileandModifiedFile($source_outofdate_sentences, $source_modified_sentences, $this->alignments, $this->translator, 'en', 'fr', 1);
		$target_diff = $update->DifferencebetweenOriginalFileandModifiedFile($target_outofdate_sentences, $target_modified_sentences, $this->alignments, $this->translator, 'en', 'fr', 0);

		for ($a=0; $a<count($source_diff); $a++) {
			$source_diff[$a] = trim($source_diff[$a]);
		}
			
		for ($a=0; $a<count($target_diff); $a++) {
			$target_diff[$a] = trim($target_diff[$a]);
			if ($target_diff[$a] == '+dummy')
			unset($target_diff[$a]);
		}
		$target_diff = array_values($target_diff);
		for ($a=0; $a<count($source_diff); $a++) {
			$index = 0;
			$i = 2;
			if ($source_diff[$a] == '' || ($source_diff[$a][0] != '+')) {
				$pat = 0;
				if ($source_diff[$a] != '' && $source_diff[$a][0] == '<' && is_numeric($source_diff[$a][1])) {
					$b = 2;
					while (is_numeric($source_diff[$a][$b])) {
						$b++;
					}
					if ($source_diff[$a][$b] == '>')
						$pat = 1;
				}
				if ($source_diff[$a] == ''|| $pat == 0) {
					$index = array_search($source_diff[$a], $source_diff);
					$index++;
					$first = 0;
					while(($c = $this->array_search_function($source_diff[$a], $source_diff, $index)) != -1) {
						$first = 1;
						$source_diff[$c] = '<$i>' . $source_diff[$c];
						$i++;
						$index = $c + 1;
					}
					$source_diff[$a] = '<1>' . $source_diff[$a];
				}
			}
		}

		for ($a=0; $a<count($target_diff); $a++) {
			$index = 1;
			$i = 2;
			if ($target_diff[$a] == '' || ($target_diff[$a][0] != '+')) {
				$pat = 0;
				if ($target_diff[$a] != '' && $target_diff[$a][0] == '<' && is_numeric($target_diff[$a][1])) {
					$b = 2;
					while (is_numeric($target_diff[$a][$b])) {
						$b++;
					}
					if ($target_diff[$a][$b] == '>')
						$pat = 1;
				}
				if ( $target_diff[$a] == '' || $pat == 0) {
					$index = array_search($target_diff[$a], $target_diff);
					$index++;
					$first = 0;
					while (($c = $this->array_search_function($target_diff[$a], $target_diff, $index)) != -1) {
						$first = 1;
						$target_diff[$c] = '<$i>' . $target_diff[$c];
						$i++;
						$index = $c + 1;
					}
					$target_diff[$a] = '<1>' . $target_diff[$a];
				}
			}
		}

		$final_updated = $update->FinalUpdatedFileinTagetLanguage($source_diff, $target_diff);
			/*
				 echo "final_updated_sentences<br/>";
				 foreach($final_updated as $item)
				 echo "sentence-> ".$item."<br/>";
				 $content = implode(' ', $final_updated);
				 echo "$content<br/>";
				 if(strcmp("Firefox supporte les caract�res internationaux pour des langues tel que lindien. Added_Source This is a test statement. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien. La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.", $content)==0)
				 echo "match<br/>";
				 else
				 echo "fail";
			 */
		return $final_updated;

	}

	public function SetAlignment($source_file, $target_file, $source_lng, $target_lng)
	{
		$source_array = explode('<br/>', $source_file);
		$target_array = explode('<br/>', $target_file);
		for ($i=1; $i<count($target_array); $i++) {
				//$target_array[$i]=utf8_decode($target_array[$i]);
		}
			for ($i=0; $i<count($source_array); $i++) {
			$this->alignments->addSentencePair($source_array[$i], $source_lng, $target_array[$i], $target_lng);
		}
	}


	public function array_search_function($temp, $array, $index)
	{
		$i = 0;
		for ($i=$index; $i<count($array); $i++) {
			$val = $array[$i];
			if (strcmp($temp, $val) == 0) {
				return $i;
			}
		}
		return -1;
	}

}

// algorithm can be checked by running through these instructions
/*
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
 */

//		$source_lng="en";
//		$target_lng="fr";
//		$source_outofdate="Firefox supports international characters for languages such as Hindi. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
//		$source_modified="Firefox supports international characters for languages such as Hindi.This is a test statement. You can test your Firefoxs support of Hindi scripts at BBC Hindi.Most sites that require additional fonts will have a page describing where you can get the font.";
//		$target_outofdate="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
//		$target_modified="Firefox supporte les caract�res internationaux pour des langues tel que lindien. Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
//		$source_alignment="Firefox supports international characters for languages such as Hindi.<br/>You can test your Firefoxs support of Hindi scripts at BBC Hindi.<br/>Most sites that require additional fonts will have a page describing where you can get the font.";
//		$target_alignment="Firefox supporte les caract�res internationaux pour des langues tel que lindien.<br/>Vous pouvez tester le support Firefox des scripts indiens sur BBC indien.<br/>La plupart des sites qui ont besoin de polices suppl�mentaires vont avoir une page qui d�crit o� vous pouvez obtenir la police.";
//		
//		$source_Mtranslation="This is a test statement.";
//		$target_Mtranslation="C'est une d�claration d'essai.";
//				
//	$test=new Multilingual_Aligner_UpdatePages();
//	$test->setUp();
//	$test->SetAlignment($source_alignment,$target_alignment,$source_lng,$target_lng);
//	$test->translator->SetMT($source_Mtranslation,$source_Mtranslation,$source_lng,$target_lng);
//	$test->UpdatingTargetPage($source_outofdate,$source_modified,$target_outofdate,$target_modified,$source_lng,$target_lng);
