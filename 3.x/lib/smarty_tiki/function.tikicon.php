<?php
function get_first_match($dir,$filename)
{
  if(!is_dir($dir))
  {
     //printf("Could not find dir: %s<br />",$dir);
     return false;
  }
  $dh=opendir($dir);
  $pattern = '^'.$filename.'.*';
  while(false !== ($curFile = readdir($dh)))
  {
  	//print_r($pattern);
  	if(eregi($pattern,$curFile) && is_file($dir.$curFile))
  	{
  		return $dir.$curFile;
  	}
  	//else
  	//{
  	//	printf(" %s != %s @ %s<br />",$curFile, $pattern, $dir.$curFile);
  	//}
  }
  return false;
}

function output_icon($params, $file)
{
	$outstr="<img src=\"".$file."\"";
	if(isset($params["iexplain"]))
	{
		$outstr=$outstr." alt=\"".$params["iexplain"]."\"";
	}
	foreach ($params as $name => $val) 
	{ 
		if($name != "igroup" && $name != "iname" && $name != "iexplain")
		{
		$outstr = $outstr." ".$name."='".$val."'";
		}
	}
	$outstr = $outstr." />";
	return $outstr;
}

function smarty_function_tikicon($params, &$smarty)
{
	global $style, $style_base,  $icon_style;

	//print_r($params);		
	//print("<br />");
	//print_r($style);
	//print("<br />");
	//print_r($style_base);
	//print("<br />");
	//print_r($icon_style);
	//print("<br />");
	//if we have icon styles, first look there
	if (isset($icon_style)) 
	{
		if (false !== ($matchFile = get_first_match("img/icons/$icon_style/".$params['igroup']."/",$params['iname']))) 
		{
			return output_icon($params, $matchFile);		 
		}
	}
	
	//if we have site styles, look there
	if (isset($style) && isset($style_base)) 
	{
		if (false !== ($matchFile = get_first_match("styles/$style_base/icons/".$params['igroup']."/",$params['iname']))) 
		{
			return output_icon($params, $matchFile);		 
		}
	}
	
	//Well, then lets look in the default location
	if (false !== ($matchFile = get_first_match("img/icons/".$params['igroup']."/",$params['iname']))) 
	{
		return output_icon($params, $matchFile);		 
	}
	
	//OK! FINE! For now TEMPORARILY we'll look in the icons folder.
	if (false !== ($matchFile = get_first_match("img/icons/",$params['iname']))) 
	{
		return output_icon($params, $matchFile);		 
	}
	
	//Still didn't find it! Well lets output something
	return output_icon($params, "broken.".$params['igroup']."/".$params['iname']);		 
}
?>
