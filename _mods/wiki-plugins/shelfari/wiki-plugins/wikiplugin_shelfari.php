<?php

// Wiki plugin to display a Shelfari widget
// By Rick Sapir (www.keycontent.org) for TikiWiki
// 
// Usage:
// 	{SHELFARI(username=XXXX,width=####,height=####,list=isread|nowreading|reading|isowned|wish|top,size=small|large,bgcolor=######) /}
//
// Required:
//       username = The ID (web address) of your Shelfari widget. This is shown on the http://www.shelfari.com/myaccount page.
//
// Optional:
//			width = Width in pixels (Default 325).
//			height = Height in pixels (Default 355).
// 			list = Shelfari listType: isread|nowreading|reading|top|wish|isowned. (Default isread).
//			size = Booksize: small|large (Default small);
//			bgcolor = RGB Background color (Default = FFFFFF). If not used, background will be transparent.
//			amazon = Your Amazon associate ID.



function wikiplugin_shelfari_help() {
        return tra("Displays the Shelfari widget").":<br />~np~{SHELFARI(username=XXXX, width=####, height=####, list=isread|nowreading|reading|isowned|wish|top, size=small|large, bgcolor=######) /}~/np~";
}

function wikiplugin_shelfari($data, $params) {

	extract ($params,EXTR_SKIP);

	if (empty($username)) {
		return tra('Missing Shelfari username. This is shown on your http://www.shelfari.com/myaccount page.');
	}

	if (empty($amazon)){
		$associate=""; 
		} else {
		$associate=",AmazonAssociate=$amazon";
	}


// set defaults
   if (empty($width)){
   	$width=325;
   }

// minium height (for small books) is 135
   if (empty($height)){
   	$height=355;
   } else { 
   	if ($height < 135) { $height=135; }
   }
   if (empty($list)){
   	$list="isread";
   }

// set booksize. default is small.
// if large, minimum height is 225
   if (empty($size) or (($size !='small') and $size != 'large')) {
   	$size="small";
   } else {
   if (($size == 'large') and ($height < 225)) { $height=225; }
   }


	if (empty($bgcolor)){
		$bgcolor="FFFFFF";
		$alpha="0";
		} else { 
		$alpha = "1"; 
	}


	$asetup .= "<div id=\"shelfariFlashcontent\">This plugin requires <a href=\"http://www.adobe.com/go/getflashplayer\">Adobe Flash 9</a>.</div>";
	$asetup .= "<script src=\"http://www.shelfari.com/ws/flash=shelf.swf,username=$username,width=$width,height=$height,shelftype=user,listtype=$list".$associate.",booksize=$size,alpha=$alpha,tag=zombie,bgcolor=$bgcolor/flashwidget.js\" type=\"text/javascript\" language=\"javascript\"></script>";

	return $asetup;

}
?>

