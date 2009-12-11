<?php
	include_once "lib/diff/Diff.php";
	include_once "lib/diff/difflib.php";
    include_once "lib/diff/Renderer.php";
    include_once "lib/diff/renderer_unified.php";
    include_once "SentenceAlignments.php";
    include_once "SentenceSegmentor.php";
	include_once "MockMTWrapper.php";
	
 
/*
 * Class used to update the modifications done in one version of page to the other version of same page.
 */

class Multilingual_Aligner_UpdateSentences1 {
	
	//$translation is 1 in case of source modification(H) and 0 in case of target modification(T"), final_diff is carrying end result
	public function DifferencebetweenOriginalFileandModifiedFile($unchangedSource_array,$changedSource_array,$alignments,$translator,$source_lng,$target_lng,$translation)
	{
	$changed_diff_unchanged=array();
	$changedSource_translated=array();
	
	$diff = &new Text_Diff($unchangedSource_array,$changedSource_array);
	$context=sizeof($unchangedSource_array);
	$renderer = &new Text_Diff_Renderer_unified($context);
	$arr=$renderer->render($diff);
	$k=0;
	$body=0;
	$del=0;
	$add=0;
	foreach($arr as $e)
	{
	if($k!=0)
	{
	foreach($e as $key=>$val)
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

	}//if $k
	$k=$k+1;


	}//foreach
	//both files are same
	if(count($changed_diff_unchanged)==0)
	{
	
	$changed_diff_unchanged=$changedSource_array;
	
	}
	
	foreach($changed_diff_unchanged as $val)
	{$num=0;
	$val=ereg_replace ('<span class="diffchar">' ,"", $val );
		$val=ereg_replace ('</span>' ,"", $val );
	$new=explode("<br />",$val);
	foreach($new as $n)
	{
	if($val!=""&&$val[0]=="-")
	{
		if($num==0)
		$changed_diff_unchanged_new[]=trim($n);
		else
		$changed_diff_unchanged_new[]="-".trim($n);
	
	}
	else if($val!=""&&$val[0]=="+")
	{
		if($num==0)
		$changed_diff_unchanged_new[]=trim($n);
		else
		$changed_diff_unchanged_new[]="+".trim($n);
	
	}
	else
		$changed_diff_unchanged_new[]=$n;
		$num++;
	}//foreach	
	}//foreach
	$changed_diff_unchanged=$changed_diff_unchanged_new;
	
	$i=0;
	foreach($changed_diff_unchanged as $value)
	{
		if(strcmp(substr($value,0,1),"-")==0)	//sentence is preceded by '-'
		{
		$temp="+".substr($value,1);
		$match=$this->array_search_function($temp,$changed_diff_unchanged);
		if($match!=-1) //sentence is shuffled
		{
		
		$changed_diff_unchanged[$i]="";//eliminating the -ve sentence
		$changed_diff_unchanged[$match]=substr($value,1);
		
		}
		else
		{
		
		$changed_diff_unchanged[$i]="*deleted*";
		
		}
		}//outer if
	
		if(strcmp(substr($value,0,1),"+")==0)	//sentence is preceded by '+'
		{
		$temp="-".substr($value,1);
		$match=$this->array_search_function($temp,$changed_diff_unchanged);
		if($match!=-1) //sentence is shuffled
		{
		
		$changed_diff_unchanged[$match]="*deleted*";//eliminating the -ve sentence
		$changed_diff_unchanged[$i]=substr($value,1);
		
		}
		else
		{
		//do nothing--sentence is added
		
		}
		
		
		
		}//outer if
			
	
	$i=$i+1;
	}//foreach
	
	
	//Converting sentences in Source Language to Target language
	if($translation==1)
	{
			
	$changedSource_translated=$this->changedSourceFileTranslatedIntoTargetLanguage($changed_diff_unchanged,$alignments,$translator,$source_lng,$target_lng);
	 
	$final_diff= $changedSource_translated;
	}//if
	else
	{
	$changed_final=array();
	$i=0;
	foreach($changed_diff_unchanged as $val)
	{
	if(strcmp($changed_diff_unchanged[$i],"*deleted*")!=0)
	$changed_final[]=trim($changed_diff_unchanged[$i]);
	$i=$i+1;
	}//foreach
	
	$final_diff=$changed_final;
	}//else
	
	return $final_diff;
	}//function ends
	
	
	////


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
	{	$value=$changed_diff_unchanged[0];
		$num++;
		{		$key_value="";
				
			$target_lng_array=$alignments->getSentenceInOtherLanguage($value, $source_lng,$key_value,$changed_diff_unchanged,$this->array_search_function($value,$changed_diff_unchanged));  //as two or more target sentences are being considered as one string, here instead of string arrays should be returned
			$key_value=$target_lng_array[0];
			$target_lng_sentence=$target_lng_array[1];
			if(strcmp($target_lng_sentence,"NULL")!=0)
			{
			$source_sent=$segmentor->segment(trim($key_value));
			$index=$this->array_search_function($value,$changed_diff_unchanged);
			$j=0;
			
			for($i=$index;$i<count($source_sent)+$index+$j;$i++)
			{if($changed_diff_unchanged[$i]=="" || $changed_diff_unchanged[$i][0]!="+")
			{		
			unset($changed_diff_unchanged[$i]);
			
			}
			else
			$j++;
			
			}	//for
			
				$sentences=$segmentor->segment(trim($target_lng_sentence));
			foreach($sentences as $item)
			{
			$changedSource_translated[]=trim($item);
			}
			
			}
			else  //Machine Translation is required
			{
				if($value!="" && $value!="+")
				{
				if($value[0]=="+")
				{$temp=substr($value,1);
				$translation = $translator->getTranslationInOtherLanguage($temp,$source_lng);
				if($translation!="NULL")
				{$changedSource_translated[]="+".trim($translation);
				}//if !NULL
				else
				{
				
				$changedSource_translated[]="+"."no translation is available in french for $temp";
				}
				}//if [0]=="+"
   	  			
   	  			else
   	  			{
   	  			$translation = $translator->getTranslationInOtherLanguage($value,$source_lng);
				if($translation!="NULL")
				{$changedSource_translated[]="+".trim($translation);
				}//if !NULL
   	  			else
   	  			$changedSource_translated[]="+"."no translation is available in french for $value";
				
   	  			}//else
				}//if
				else
				{
				$changedSource_translated[]=$value;
				}
				$index=$this->array_search_function($value,$changed_diff_unchanged);
				unset($changed_diff_unchanged[$index]);
			
				
			}
			$changed_diff_unchanged = array_values($changed_diff_unchanged);
			
		}//if
		
	}	//foreach
	return $changedSource_translated;
	
	

	
	}//function ends
	
	
	public function FinalUpdatedFileinTagetLanguage($Souce_Updated_Translated,$Target_Updated)
	{
	
	$diff = &new Text_Diff($Souce_Updated_Translated,$Target_Updated);
	$context=sizeof($Souce_Updated_Translated);
	
	$renderer = &new Text_Diff_Renderer_unified($context);
	$arr=$renderer->render($diff);
	$k=0;
	$body=0;
	$del=0;
	$add=0;
	foreach($arr as $e)
	{
	if($k!=0)
	{
	foreach($e as $key=>$val)
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
	$target_diff_source[]=$item;
	if($del==1)
	$target_diff_source[]="-".$item;
	if($add==1)
	$target_diff_source[]="+".$item;
	}//foreach

	
	$body=0;
	$del=0;
	$add=0;

	}//if

	}//foreach

	}//if $k
	$k=$k+1;


	}//foreach
	
	$target_diff_source_new=array();
	foreach($target_diff_source as $val)
	{$num=0;
	$val=ereg_replace ('<span class="diffchar">' ,"", $val );
		$val=ereg_replace ('</span>' ,"", $val );
	$new=explode("<br />",$val);
	foreach($new as $n)
	{
	if($val[0]=="-")
	{
		if($num==0)
		$target_diff_source_new[]=trim($n);
		else
		$target_diff_source_new[]="-".trim($n);
	
	}
	else if($val[0]=="+")
	{
		if($num==0)
		$target_diff_source_new[]=trim($n);
		else
		$target_diff_source_new[]="+".trim($n);
	
	}
	else
		$target_diff_source_new[]=$n;
		$num++;
	}//foreach	
	}//foreach
	
		//difference over
	
	//generation of three arrays
	$negative_array=array();
	$positive_array=array();
	$normal_array=array();
	$i=-1;
	foreach($target_diff_source_new as $value)
	{$i++;
	
		if(strcmp(substr($value,0,1),"-")==0)//sentence starts with '-'
			{
			$temp="+".substr($value,1);
			$match=$this->array_search_function($temp,$target_diff_source_new);
			if($match!=-1)
				{
				if($temp[1]=='+')//if same sentence is being added in to both source and target files
				{
				$target_diff_source_new[$i]="";
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
			$match=$this->array_search_function($temp,$target_diff_source_new);
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
		
	//generation of three arrays is complete	
	
	//Creating hash table to get the proper location for insertion
	$add_beginning=array();
	$sentence_location=array();
	foreach($negative_array as $item)
	{
		$index=$this->array_search_function($item,$target_diff_source_new);
		$get=0; // to check if there is any normal sentence before this negative sentence
		for($j=$index-1;$j>=0;$j--)
			{
			if($get==1)
			break;
			if($target_diff_source_new[$j][0]=="+" || $target_diff_source_new[$j][0]=="-")
			$temp=substr($target_diff_source_new[$j],1);
			else
			$temp=$target_diff_source_new[$j];
			$search_result=$this->array_search_function($temp,$normal_array);
			if($search_result!=-1)//found in normal array
				{$found=0; //to chack if already present in hash table
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
					{	$sentence_location[$temp]=array($item);
					}
				}//if search_result
				
				//search in positive_array is doubtful
			}//for $j
	
	if($get==0)
		{
		$add_beginning[]=$item;
		}
	
	}//foreach negative_array
	
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
							$b=2;
								while(is_numeric($item[$b]))
								{
								$b++;
								}
											
								$finalUpdatedTarget[]="Deleted_Target ".substr($item,$b+1);
							
								
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
				//if($item!="")
				{
				{
			//	if($item[0]=="<"&&$item[2]==">"&&is_numeric($item[1]))
				$b=2;
				while(is_numeric($item[$b]))
				{
								$b++;
				}
							
				$item=substr($item,$b+1);
				
				}
				
				}//if
				$temp="Deleted_Source ".$item;
				}
				$finalUpdatedTarget[]=$temp;		
		
			}//if present in positive_array
		
		else  //present in normal arrray	
			{$item1=$item;
			if($item!="")
				{
				{
				if($item[0]=="+")//if same sentence is added at same positions in both source and target
				$item1=substr($item,1);
				else{
						$b=2;
								while(is_numeric($item[$b]))
								{
								$b++;
								}
							
				$item1=substr($item,$b+1);
				}//else
				
				}
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
													
							$b=2;
								while(is_numeric($add[$b]))
								{
								$b++;
								}
											
								$add=substr($add,0,1).substr($add,$b+1);
				
				
								
							}//if
		
								$finalUpdatedTarget[]="Deleted_Target ".substr($add,1);
							}
						
						}
					break;
					}//if
				
				
				}//foreach
			}
		}//foreach $Target_Updated
	return $finalUpdatedTarget;	
	}//function ends
	
	public function array_search_function($temp,$array)
	{$i=0;
	foreach($array as $val)
	{
	if(strcmp($temp,$val)==0)
	{
	return $i;
	}
	$i++;
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
	}//function over
	
	
}

?>