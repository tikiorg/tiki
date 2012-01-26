<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once "SentenceAlignments.php";
  
class Multilingual_Aligner_MockMTWrapper extends Multilingual_Aligner_SentenceAlignments
{

	public function getTranslationInOtherLanguage($source_lng_sentence, $source_lng) {
	
		if($source_lng=="en")
			$k=1;
		else if($source_lng=="fr")
			$k=0;
		foreach($this->alignment_table as $key=>$val)
		{
			if($k==1)
			{
				if($key==$source_lng_sentence)
				return $val;
			}
			else
			{
				if($val==$source_lng_sentence)
				return $key;
			}	
				 
		}
		return "NULL";
	}
	
	public function SetMT($source_file,$target_file,$source_lng,$target_lng)
		{
		$source_array=explode("<br/>",$source_file);
		$target_array=explode("<br/>",$target_file);
		
		for($i=0, $ct_a=count($target_array);$i<$ct_a;$i++)
		{
			$target_array[$i]=trim($target_array[$i]);
			//	$target_array[$i]=utf8_decode($target_array[$i]);
		}
		for($i=0, $cs_a=count($source_array);$i<$cs_a;$i++)
		{
			$source_array[$i]=trim($source_array[$i]);
		}
		for($i=0, $cs_a=count($source_array);$i<$cs_a;$i++)
		{
			$this->addSentencePair($source_array[$i],$source_lng,$target_array[$i],$target_lng);
		}
		}//function ends
}//class ends
