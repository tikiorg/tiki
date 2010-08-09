<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

	include_once 'lib/diff/Diff.php';
	include_once 'lib/diff/difflib.php';
  include_once 'lib/diff/Renderer.php';
  include_once 'lib/diff/renderer_unified.php';
  include_once 'SentenceAlignments.php';
  include_once 'SentenceSegmentor.php';
	include_once 'MockMTWrapper.php';
	
 
/*
 * Class used to update the modifications done in one version of page to the other version of same page.
 */

class Multilingual_Aligner_UpdateSentences1
{
	
	//$translation is 1 in case of source modification(H) and 0 in case of target modification(T"), final_diff is carrying end result
	public function DifferencebetweenOriginalFileandModifiedFile($unchangedSource_array,$changedSource_array,$alignments,$translator,$source_lng,$target_lng,$translation)
	{
	$changed_diff_unchanged=array();
	$changedSource_translated=array();
	$changed_diff_unchanged=$this->text_diff($unchangedSource_array,$changedSource_array);
	if(count($changed_diff_unchanged)==0)	//both files are same
	{
		$changed_diff_unchanged=$changedSource_array;
	}
	$changed_diff_unchanged=$this->remove_wikisyntax($changed_diff_unchanged);
	$changed_diff_unchanged=$this->identify_shuffled_and_negative_sentences($changed_diff_unchanged);
	//Converting sentences in Source Language to Target language
	if($translation==1)//files are in source language
	{
		$changedSource_translated=$this->changedSourceFileTranslatedIntoTargetLanguage($changed_diff_unchanged,$alignments,$translator,$source_lng,$target_lng);
		$final_diff= $changedSource_translated;
	}//if
	else
	{
		$changed_final=array();
		$ii=0;
		foreach($changed_diff_unchanged as $val)
		{
			if(strcmp($changed_diff_unchanged[$ii],"*deleted*")!=0)
			$changed_final[]=trim($changed_diff_unchanged[$ii]);
			$ii=$ii+1;
		}//foreach
		$final_diff=$changed_final;
	}//else
	return $final_diff;
	}//function ends
	
	
	public function identify_shuffled_and_negative_sentences($changed_diff_unchanged)
	{
	$ii=0;
	foreach($changed_diff_unchanged as $value)
	{
		if(strcmp(substr($value,0,1),"-")==0)	//sentence is preceded by '-'
		{
			$temp="+".substr($value,1);
			$match=$this->array_search_function($temp,$changed_diff_unchanged);
			if($match!=-1) //sentence is shuffled
			{
				$changed_diff_unchanged[$ii]="";//eliminating the -ve sentence
				$changed_diff_unchanged[$match]=substr($value,1);
			}
			else
			{
				$changed_diff_unchanged[$ii]="*deleted*";
			}
		}//outer if
	
		if(strcmp(substr($value,0,1),"+")==0)	//sentence is preceded by '+'
		{
			$temp="-".substr($value,1);
			$match=$this->array_search_function($temp,$changed_diff_unchanged);
			if($match!=-1) //sentence is shuffled
			{
				$changed_diff_unchanged[$match]="*deleted*";//eliminating the -ve sentence
				$changed_diff_unchanged[$ii]=substr($value,1);
		
			}
		}//outer if
		$ii=$ii+1;
	}//foreach
	return 	$changed_diff_unchanged;
	}//function ends here
	
	public function remove_wikisyntax($sentences)
	{
	
	foreach($sentences as $val)
	{
		$num=0;
		$val=preg_replace ('/<span class="diffchar">/', '', $val );
		$val=preg_replace ('#</span>#', '', $val );
		$new_val=explode('<br />',$val);
		foreach($new_val as $nn)
		{
			if($val!=""&&$val[0]=="-")
			{
				if($num==0)
					$sentences_new[]=trim($nn);
				else
					$sentences_new[]="-".trim($nn);
			}
			else if($val!=""&&$val[0]=="+")
			{
				if($num==0)
					$sentences_new[]=trim($nn);
				else
					$sentences_new[]="+".trim($nn);
			}
			else
				$sentences_new[]=$nn;
			$num++;
		}//foreach	
	}//foreach
		return $sentences_new;
	}//function ends
	
	
	public function text_diff($unchangedSentence_array,$changedSentence_array)
	{
	$changed_diff_unchanged=array();
	$diff = new Text_Diff($unchangedSentence_array,$changedSentence_array);
	$context=count($unchangedSentence_array);
	$renderer = new Text_Diff_Renderer_unified($context);
	$arr=$renderer->render($diff);
	$kk=0;
	$body=0;
	$del=0;
	$add=0;
	foreach($arr as $ee)
	{
		if($kk!=0)
		{
			foreach($ee as $key=>$val)
			{
				if($val=="diffbody")
				$body=1;
				if($val=="diffdeleted")
				$del=1;
				if($val=="diffadded")
				$add=1;
				if($key=="data")
				{
					foreach($val as $item)
					{
						if($body==1)
						$changed_diff_unchanged[]=$item;
						if($del==1)
						$changed_diff_unchanged[]="-".$item;
						if($add==1)
						$changed_diff_unchanged[]="+".$item;
					}//foreach
					$body=0;
					$del=0;
					$add=0;
				}//if

			}//foreach

		}//if $kk
		$kk=$kk+1;
	}//foreach
	return $changed_diff_unchanged;
	}//function ends

	public function changedSourceFileTranslatedIntoTargetLanguage($changed_diff_unchanged,$alignments,$translator,$source_lng,$target_lng)
	{
	$segmentor = new Multilingual_Aligner_SentenceSegmentor();
	$num=0;
	foreach($changed_diff_unchanged as $value)
	{
		if($value=="*deleted*")
		unset($changed_diff_unchanged[$num]);
		$num++;
	}
	$changed_diff_unchanged = array_values($changed_diff_unchanged);
	$num=0;
	while(count($changed_diff_unchanged )>0)
	{
		$value=$changed_diff_unchanged[0];
		$num++;
		$key_value="";
		$target_lng_array=$alignments->getSentenceInOtherLanguage($value, $source_lng,$key_value,$changed_diff_unchanged,$this->array_search_function($value,$changed_diff_unchanged));  //as two or more target sentences are being considered as one string, here instead of string arrays should be returned
		$key_value=$target_lng_array[0];
		$target_lng_sentence=$target_lng_array[1];
		if(strcmp($target_lng_sentence,"NULL")!=0)
		{
			$source_sent=$segmentor->segment(trim($key_value));
			$index=$this->array_search_function($value,$changed_diff_unchanged);
			$jj=0;
			for ($ii=$index; $ii<count($source_sent)+$index+$jj; $ii++)
			{
				if($changed_diff_unchanged[$ii]=="" || $changed_diff_unchanged[$ii][0]!="+")
				{		
					unset($changed_diff_unchanged[$ii]);
				}
				else
					$jj++;
			}	//for
			$sentences=$segmentor->segment(trim($target_lng_sentence));
			foreach($sentences as $item)
			{
				$changedSource_translated[]=trim($item);
			}
		}//if
		else  //Machine Translation is required
		{
			if($value!="" && $value!="+")
			{
				if($value[0]=="+")
				{
					$temp=substr($value,1);
					$translation = $translator->getTranslationInOtherLanguage($temp,$source_lng);
					if($translation!="NULL")
					{
						$changedSource_translated[]="+".trim($translation);
					}//if !NULL
					else
					{
						//$changedSource_translated[]="+"."no translation is available in french for $temp";
						$changedSource_translated[]="+"."$temp";
					}
				}//if [0]=="+"
   	  			else
   	  			{
   	  				$translation = $translator->getTranslationInOtherLanguage($value,$source_lng);
					if($translation!="NULL")
					{
						$changedSource_translated[]="+".trim($translation);
					}//if !NULL
   	  				else
   	  				{
   	  					//$changedSource_translated[]="+"."no translation is available in french for $value";
						$changedSource_translated[]="+"."$value";
					}
   	  			}//else
			}//if
			else
			{
				$changedSource_translated[]=$value;
			}
			$index=$this->array_search_function($value,$changed_diff_unchanged);
			unset($changed_diff_unchanged[$index]);
		}//else
		$changed_diff_unchanged = array_values($changed_diff_unchanged);
	}//while
	return $changedSource_translated;
	}//function ends
	
	public function separate_negative_positive_normal_sentences($newarray_diff_oldarray)
	{
	$negative_array=array();
	$positive_array=array();
	$normal_array=array();
	$ii=-1;
	foreach($newarray_diff_oldarray as $value)
	{
		$ii++;
		if(strcmp(substr($value,0,1),"-")==0)//sentence starts with '-'
		{
			$temp="+".substr($value,1);
			$match=$this->array_search_function($temp,$newarray_diff_oldarray);
			if($match!=-1)
			{
				if($temp[1]=='+')//if same sentence is being added in to both source and target files
				{
					$newarray_diff_oldarray[$ii]="";
				}
				else
				{
					if(($this->array_search_function(substr($value,1),$normal_array))==-1)
					{
						$normal_array[]=substr($value,1);
					}//if not present in normal_array
				}//else
			}//if match
			else
			{
				$negative_array[]=$value;	
			}//match not found
		}//if '-'
		else if(strcmp(substr($value,0,1),"+")==0)//sentence starts with '+'
		{
			$temp="-".substr($value,1);
			$match=$this->array_search_function($temp,$newarray_diff_oldarray);
			if($match!=-1)
			{
				if($temp[1]=="+")//if same sentence is being added in to both source and target files
				{
					$positive_array[]=$value;
				}
				else
				{
					if($this->array_search_function(substr($value,1),$normal_array)==-1)
					{
						$normal_array[]=substr($value,1);
					}//if not present in normal_array
				}//else
			}//if match found
			else
			{
				$positive_array[]=$value;	
			}//match not found
		}//if '+'
		else  //normal sentence
		{
			$normal_array[]=$value;
		}//normal sentence
	}//foreach
	$combo_array=array($negative_array,$positive_array,$normal_array);
	return $combo_array;
	}//function ends here
	
	public function getlocation_addedsentenceintoSource_or_deletedsentencefromTarget($negative_array,$positive_array,$normal_array,$target_diff_source)
	{
	$add_beginning=array();
	$sentence_location=array();
	foreach($negative_array as $item)
	{
		$index=$this->array_search_function($item,$target_diff_source);
		$get=0; // to check if there is any normal sentence before this negative sentence
		for($jj=$index-1;$jj>=0;$jj--)
		{
			if($get==1)
				break;
			if($target_diff_source[$jj][0]=="+" || $target_diff_source[$jj][0]=="-")
				$temp=substr($target_diff_source[$jj],1);
			else
				$temp=$target_diff_source[$jj];
			$search_result=$this->array_search_function($temp,$normal_array);
			if($search_result!=-1)//found in normal array
			{
				$found=0; //to chack if already present in hash table
				$get=1; ///found a normal sentence before
				foreach($sentence_location as $key=>$val)
				{
					if(strcmp($key,$temp)==0)
					{
						$found=1;
						$sentence_location[$key][count($sentence_location[$key])]=$item;
					}
						
				}//foreach
				if($found==0)
				{
					$sentence_location[$temp]=array($item);
				}
			}//if search_result
			//search in positive_array is doubtful
		}//for $jj
	
		if($get==0)
		{
			$add_beginning[]=$item;
		}
	}//foreach negative_array
	$combo_arr=array($add_beginning,$sentence_location);
	return $combo_arr;
	}//function ends here
	
	
	
	public function FinalUpdatedFileinTagetLanguage($Souce_Updated_Translated,$Target_Updated)
	{
	$target_diff_source=$this->text_diff($Souce_Updated_Translated,$Target_Updated);
	$target_diff_source_new=$this->remove_wikisyntax($target_diff_source);
	//generation of three arrays
	$combo_array=$this->separate_negative_positive_normal_sentences($target_diff_source_new);
	$negative_array=$combo_array[0];
	$positive_array=$combo_array[1];
	$normal_array=$combo_array[2];
	//generation of three arrays is complete	
	//Creating hash table to get the proper location for insertion
	$combo_arr=$this->getlocation_addedsentenceintoSource_or_deletedsentencefromTarget($negative_array,$positive_array,$normal_array,$target_diff_source_new);
	$add_beginning=$combo_arr[0];
	$sentence_location=$combo_arr[1];
	//sentence_location and add_beginning is complete
	//generation of final updated target file
	foreach($add_beginning as $item)
	{
		if($item[1]=="+")
		{
			$finalUpdatedTarget[]="Added_Source ".substr($item,2);
		}
		else
		{
			$bb=2;
			while(is_numeric($item[$bb]))
			{
				$bb++;
			}
			$finalUpdatedTarget[]="Deleted_Target ".substr($item,$bb+1);
		}
	}//foreach
			
	foreach($Target_Updated as $item)
	{
		if(($index=$this->array_search_function("+".$item,$positive_array))!=-1)   //if present in positive_array
		{
			if($positive_array[$index]!="+" && $positive_array[$index][1]=='+')//'++' case
				$temp=substr($item,1);
			else if($positive_array[$index]=="+" || $positive_array[$index][1]!='+')  //"+" case
			{
				$bb=2;
				while(is_numeric($item[$bb]))
				{
					$bb++;
				}
				$item=substr($item,$bb+1);
				$temp="Deleted_Source ".$item;
			}
			$finalUpdatedTarget[]=$temp;		
		}//if present in positive_array
		else  //present in normal arrray	
		{
			$item1=$item;
			if($item!="")
			{
				if($item[0]=="+")//if same sentence is added at same positions in both source and target
				$item1=substr($item,1);
				else
				{
					$bb=2;
					while(is_numeric($item[$bb]))
					{
						$bb++;
					}
					$item1=substr($item,$bb+1);
				}//else
			}//if
			$finalUpdatedTarget[]=$item1;
			foreach($sentence_location as $key=>$val)
			{
				if(strcmp($key,$item)==0)
				{
					foreach($val as $add)
					{
						if($add!="-" && $add[1]=="+")
						{
							$finalUpdatedTarget[]="Added_Source ".substr($add,2);
						}
						else if($add=="-" || $add[1]!="+" )
						{
							if($add!="-")
							{
								$bb=2;
								while(is_numeric($add[$bb]))
								{
									$bb++;
								}
								$add=substr($add,0,1).substr($add,$bb+1);
							}//if
							$finalUpdatedTarget[]="Deleted_Target ".substr($add,1);
						}//else if
					}//foreach
					break;
				}//if
			}//foreach
		}//else in normal sentence
	}//foreach $Target_Updated
	return $finalUpdatedTarget;	
	}//function ends
	
	public function array_search_function($temp,$array)
	{$ii=0;
	foreach($array as $val)
	{
	if(strcmp($temp,$val)==0)
	{
	return $ii;
	}
	$ii++;
	}
	return -1;
	}//function over
	
	public function strpos_function($string,$pat)
	{ 
		if(strlen($string)==0 && strlen($pat)==0)
		return 0;
		else if(strlen($string)==0 ||strlen($pat)==0)
		return -1;
		$start=0;
		$lasts=strlen($string)-1;
		$lastp=strlen($pat)-1;
		$endmatch=$lastp;
		$jj=0;
		for($ii=0;$endmatch<=$lasts;$endmatch++,$start++)
		{
		if($string[$endmatch]==$pat[$lastp])
			{for($jj=0,$ii=$start;$jj<$lastp && $string[$ii]==$pat[$jj];$ii++,$jj++);
			}//for $jj		
			if($jj==$lastp)
			return $start;
		}//for $ii
		return -1;
	}//function over
}
