<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Class used to store aligned bilingual sentences for two different
 * linguistic versions of a same document. For example, English 
 * sentences with their corresponding French sentences.
 */
include_once "SentenceSegmentor.php";

class Multilingual_Aligner_SentenceAlignments
{
	protected $alignment_table=array();
	protected $l1="en";
	protected $l2="fr";

	//$lng2_sentence is an array of sentences and $lng1_sentences is a sentence/**not applicable now
	public function addSentencePair($lng1_sentence, $lng1, $lng2_sentence, $lng2)
	{
		//echo "in addsentencepair<br/>";
		if($lng1==$this->l1)
		{
		//echo "in addpair eng<br/>";
		$this->alignment_table[$lng1_sentence]=$lng2_sentence;
		}
		else if($lng2==$this->l1)
		{//echo "in addpair fr<br/>";
		$this->alignment_table[$lng2_sentence]=$lng1_sentence;
        }	
         }
	//this function returns an array of sentences /**not applicable now
	public function getSentenceInOtherLanguage($source_lng_sentence, $source_lng,$key_value,$sentence_array,$index)
	{
		echo "in getSentenceInOtherLanguage<br/>";
		$segmentor = new Multilingual_Aligner_SentenceSegmentor();
					
		if($source_lng==$this->l1)
			$k=1;
		else if($source_lng==$this->l2)
			$k=0;
		foreach($this->alignment_table as $key=>$val)
			{
				if($k==1)
				{//if(strcmp(trim($key),trim($source_lng_sentence))==0)
					echo "key##$key<br/>";
					//if($key!="\n\n")
					$sentences = $segmentor->segment(trim($key));
					//else
					//$sentences[]=$key;
					//echo "sentence[0]##$sentences[0]<br/>";
					echo "count ".count($sentences)."<br/>";
					foreach($sentences as $t)
					echo "line after segmenting ##$t<br/>";
					//if(count($sentences)!=1)
					//##unset($sentences[count($sentences)-1]);//remove last blank line
	 				//echo "count ".count($sentences)."<br/>";
					//foreach($i=0;$i<count($sentences);$i++) //for each sentence of $key
					{
					if(strcmp(trim($sentences[0]),trim($source_lng_sentence))==0)//if one of those is matched
					{
					$found=1;
					for($j=1,$l=1;$j<count($sentences) ;$l++)
					{$flag=0;
					if(($l +$index)>=count($sentence_array))
					{$found=0;
					break;
					}
					if(strcmp(trim($sentence_array[$index+$l]),trim($sentences[$j]))!=0)
					{
					if($sentence_array[$index+$l]=="" || $sentence_array[$index+$l][0]!="+") //if it is an added sentence
					{$found=0;
					break;
					}//if
					else
					$flag=1;
					}//if
					if($flag==0)
					$j++;
					}//for
					if($found==1)
					{
					$key_value=$key;
					$array=array($key,$val);
					return $array;
					}//if
					}//if
					
					}//foreach
					
				}//if $k
				else
				{ //if(trim($val)==trim($source_lng_sentence))
					///return $key;
					
					$sentences = $segmentor->segment(trim($val));
					//##unset($sentences[count($sentences)-1]);//remove last blank line
	 
					//foreach($i=0;$i<count($sentences);$i++) //for each sentence of $key
					{
					if(strcmp(trim($sentences[0]),trim($source_lng_sentence))==0)//if one of those is matched
					{
					$found=1;
					for($j=$i+1,$l=1;$j<count($sentences);$l++)
					{$flag=0;
					if(($l +$index)>=count($sentence_array))
					{$found=0;
					break;
					}
					
					if(strcmp(trim($sentence_array[$index+$l]),trim($sentences[$j]))!=0)
					{
					if($sentence_array[$index+$l]=="" || $sentence_array[$index+$l][0]!="+") //if it is an added sentence
					{$found=0;
					break;
					}//if
					else
					$flag=1;
					}//if
					if($flag==0)
					$j++;
					}//for
					if($found==1)
					{
					$key_value=$val;
					$array=array($val,$key);
					return $array;
					}//if
					}//if
					
					}//foreach
					
	
					
				}	//else
				 
			}//foreach
		
		if($k==1)
		{	//####for many lines->many translatn		
			$times=0;
			$i=-1;
			$temp1="NULL";
			$temp2="NULL";
			$index1=$index;
			$start=0;
			$value="";
			$found=0;
					
		foreach($this->alignment_table as $key=>$val)
		{			$start++;
					$sent_ind=0;
					$sentences = $segmentor->segment(trim($key));
					for($j=0;$j<count($sentences);$j++)
					{
						$sentences[$j]=trim($sentences[$j]);
					}
					echo "another sentence<br/>";
					//echo "sentence[0]##$sentences[0]<br/>";
					//echo "count ".count($sentences)."<br/>";
					//echo "line ##".$sentences[count($sentences)-1]."<br/>";
					//if(count($sentences)!=1 && $sentences[count($sentences)-1] =="")
					//##unset($sentences[count($sentences)-1]);//remove last blank line
	 				//echo "count ".count($sentences)."<br/>";
					
					while(1)
					{$found=0;
					
					//foreach($i=0;$i<count($sentences);$i++) //for each sentence of $key
					//if source line is a part of translation
					if($temp1=="NULL" && $sent_ind<count($sentences))
					{$temp1=$sentences[$sent_ind];
					//echo "temp1=NULL  $temp1<br/>";
					$sent_ind++;
					}
					if($temp2=="NULL")
					{$temp2=$source_lng_sentence;
					$index1;
					}
					$temp1=trim($temp1);
					$temp2=trim($temp2);
					
					if(($c=$this->strpos_function($temp1,$temp2))!=-1 && $c ==0)
					{		$found=1;
					echo "inside strpos_function($temp1,$temp2)<br/>";
							if(strlen($temp1)==strlen($temp2) && $sent_ind==count($sentences))
							{echo "inside strlen($temp1)==strlen($temp2)  and ####start= $start<br/>";
							for($u=0;$u<$start;$u++)//return key and val ###########
							{//echo "inside<br/>";
							prev($this->alignment_table);
							}
							$d=key($this->alignment_table);
							$key_value=$key_value.$d;
							$value=$value.current($this->alignment_table);
							//echo "## $key_value----$value<br/>";
							for($u=0;$u<$start-1;$u++)//return key and val
							{echo "outside<br/>";
							next($this->alignment_table);
							$d=key($this->alignment_table);
							
							$key_value=$key_value.$d;
							$value=$value.current($this->alignment_table);
							//echo "## $key_value----$value<br/>";
							
							}
							$array=array($key_value,$value,$dummy);
							$start=0;
							return $array;
							}
							$temp1 = substr($temp1,strlen($temp2));
							if($temp1=="")
							$temp1="NULL";
							while(($index1+1)<count($sentence_array))
							{if($sentence_array[$index1+1]=="" || $sentence_array[$index1+1][0]!="+")
							{
							$temp2=$sentence_array[$index1+1];
							//echo "temp2  $temp2<br/>";
							$index1++;
							break;
							}//if
							$index1++;
							}//while
						continue;
						
					}//if strpos_function($sentence[0],$source_lng_sentence)
					else if(($c=$this->strpos_function($temp2,$temp1))!=-1 && $c ==0)
					{		
							echo "inside strpos_function($temp2,$temp1)<br/>";
					
							$found=1;
							if(strlen($temp1) == strlen($temp2) && $sent_ind==count($sentences))
							{
							echo "inside strlen($temp1)==strlen($temp2)  and ####start= $start<br/>";
							
							for($u=0;$u<$start;$u++)//return key and val#############
							{
							prev($this->alignment_table);
							}
							$d=key($this->alignment_table);
							
							$key_value=$key_value.$d;
							$value=$value.current($this->alignment_table);
							for($u=0;$u<$start-1;$u++)//return key and val
							{
							next($this->alignment_table);
							$key_value=$key_value.key($this->alignment_table);
							$value=$value.current($this->alignment_table);
							
							}
							$array=array($key_value,$value,$dummy);
							$start=0;
							return $array;
							}//if equal
							
							$temp2=substr($temp2,strlen($temp1));
							//echo "inside else if temp2 $temp2<br/>";
							if($sent_ind>=count($sentences))
							{
							$temp1="NULL";
							break;
							}
							else
							{
							$temp1=$sentences[$sent_ind];
							$sent_ind++;
							}
					}//if strpos_function($sentence[0],$source_lng_sentence)
			if($found==0)
			{echo "break<br/>";
			$start=0;
			$value="";
			break;
			}//if
			}//while
			if($found==0)
			{
			$temp1="NULL";
			$temp2="NULL";
			$index1=$index;
					
			}
		}//foreach		
					
		}//if $k
	else
		{	//####for many lines->many translatn		
			$times=0;
			$i=-1;
			$temp1="NULL";
			$temp2="NULL";
			$index1=$index;
			$start=0;
			$value="";
			$found=0;
					
		foreach($this->alignment_table as $key=>$val)
		{			$start++;
					$sent_ind=0;
					$sentences = $segmentor->segment(trim($val));
					for($j=0;$j<count($sentences);$j++)
					{
						$sentences[$j]=trim($sentences[$j]);
					}
					
					//echo "sentence[0]##$sentences[0]<br/>";
					//echo "count ".count($sentences)."<br/>";
					//echo "line ##".$sentences[count($sentences)-1]."<br/>";
			//		if(count($sentences)!=1 && $sentences[count($sentences)-1] =="")
				//##	unset($sentences[count($sentences)-1]);//remove last blank line
	 				//echo "count ".count($sentences)."<br/>";
					
					while(1)
					{$found=0;
					
					//foreach($i=0;$i<count($sentences);$i++) //for each sentence of $key
					//if source line is a part of translation
					if($temp1=="NULL" && $sent_ind<count($sentences))
					{$temp1=$sentences[$sent_ind];
					//echo "temp1=NULL  $temp1<br/>";
					$sent_ind++;
					}
					if($temp2=="NULL")
					{$temp2=$source_lng_sentence;
					$index1;
					}
					$temp1=trim($temp1);
					$temp2=trim($temp2);
					
					if(($c=$this->strpos_function($temp1,$temp2))!=-1 && $c ==0)
					{		$found=1;
					//echo "inside strpos_function($temp1,$temp2)<br/>";
							if(strlen($temp1)==strlen($temp2) && $sent_ind==count($sentences))
							{//echo "inside strlen($temp1)==strlen($temp2)  and ####start= $start<br/>";
							for($u=0;$u<$start;$u++)//return key and val
							{//echo "inside<br/>";
							prev($this->alignment_table);
							}
							$d=current($this->alignment_table);
							$key_value=$key_value.$d;
							$value=$value.key($this->alignment_table);
							//echo "## $key_value----$value<br/>";
							for($u=0;$u<$start-1;$u++)//return key and val
							{//echo "outside<br/>";
							next($this->alignment_table);
							$d=current($this->alignment_table);
							
							$key_value=$key_value.$d;
							$value=$value.key($this->alignment_table);
							//echo "## $key_value----$value<br/>";
							
							}
							$array=array($key_value,$value,$dummy);
							$start=0;
							return $array;
							}
							$temp1 = substr($temp1,strlen($temp2));
							if($temp1=="")
							$temp1="NULL";
							while(($index1+1)<count($sentence_array))
							{if($sentence_array[$index1+1]=="" || $sentence_array[$index1+1][0]!="+")
							{
							$temp2=$sentence_array[$index1+1];
							//echo "temp2  $temp2<br/>";
							$index1++;
							break;
							}//if
							$index1++;
							}//while
						continue;
						
					}//if strpos_function($sentence[0],$source_lng_sentence)
					else if(($c=$this->strpos_function($temp2,$temp1))!=-1 && $c ==0)
					{		
							//echo "inside strpos_function($temp2,$temp1)<br/>";
					
							$found=1;
							if(strlen($temp1) == strlen($temp2) && $sent_ind==count($sentences))
							{
							for($u=0;$u<$start;$u++)//return key and val
							{
							prev($this->alignment_table);
							}
							$d=current($this->alignment_table);
							
							$key_value=$key_value.$d;
							$value=$value.key($this->alignment_table);
							for($u=0;$u<$start-1;$u++)//return key and val
							{
							next($this->alignment_table);
							$key_value=$key_value.current($this->alignment_table);
							$value=$value.current($this->alignment_table);
							
							}
							$array=array($key_value,$value,$dummy);
							$start=0;
							return $array;
							}//if equal
							
							$temp2=substr($temp2,strlen($temp1));
							//echo "inside else if temp2 $temp2<br/>";
							if($sent_ind>=count($sentences))
							{
							$temp1="NULL";
							break;
							}
							else
							{
							$temp1=$sentences[$sent_ind];
							$sent_ind++;
							}
					}//if strpos_function($sentence[0],$source_lng_sentence)
			if($found==0)
			{echo "break<br/>";
			$start=0;
			$value="";
			break;
			}//if
			}//while
			if($found==0)
			{
			$temp1="NULL";
			$temp2="NULL";
			$index1=$index;
					
			}
		}//foreach		
					
		}//else
		
		$array=array("","NULL");
		return $array;
		
	}
	
	public function display_alignment_table()
	{
		echo "in func display<br/>";
		foreach($this->alignment_table as $key=>$val) {
			echo "<-->".$key."<--->".$val."<--><br/>";
		}
	}

	public function strpos_function($string,$pat)
	{ 
		if(strlen($string)==0 && strlen($pat)==0)
		return 0;
		else if(strlen($string)==0 ||strlen($pat)==0)
		return -1;
	//	echo "in strpos  $string----$pat<br/>";
		$start=0;
		$lasts=strlen($string)-1;
		$lastp=strlen($pat)-1;
		$endmatch=$lastp;
		$j=0;
		for($i=0;$endmatch<=$lasts;$endmatch++,$start++)
		{
		if($string[$endmatch]==$pat[$lastp])
			{for($j=0,$i=$start;$j<$lastp && $string[$i]==$pat[$j];$i++,$j++);
			}//for $j		
			if($j==$lastp)
			return $start;
		}//for $i
		return -1;
	}//function
}
	/*$alignments = new Multilingual_Aligner_SentenceAlignments();

	$en_sentence = "hello world.";
	$fr_sentence = "bonjour le monde12";
	$alignments->addSentencePair($en_sentence, 'en', $fr_sentence, 'fr');
	$en_array = array("hello","world.");
	$en_sentence="hello";
	$mix_array = $alignments->getSentenceInOtherLanguage($en_sentence, 'en',"",$en_array,0);
	//$key_value=$mix_array[0];
	$fr_sentence=$mix_array[1];
	echo "#".$fr_sentence."#";
	
*/
