<?php

// Wiki plugin to share music from last.fm
// By Rick Sapir (www.keycontent.org) for TikiWiki
// 
// Usage:
// 	{LASTFM(artist=XXXX|tag=XXXX, color=red|blue|black|grey, size=regular|mini, autostart=yes|no ) /}
//
//
// Required:
//			artist = Musical artist to seed the music.
//				or
//			tag = Genre or tag to seed the music.
//			NOTE: If both are included, only artist will be used.
//			SPECIAL: Use tag=tikiwiki to listen to the TikiWiki Member Radio Station (http://www.last.fm/group/Tikiwiki)
//
// Optional:
//			color = Color of the widget (Default is red).
//			size = Size of the widget (Default is regular).
// 		autostart = Automatically start playing the music when the page loads (Default is n).


function wikiplugin_lastfm_help() {
        return tra("Displays the last.fm widget to play music").":<br />~np~{LASTFM(artist=XXXX|tag=XXXX, color=red|blue|black|grey, size=regular|mini, autostart=yes|no ) /}";
}

function wikiplugin_lastfm($data, $params) {
	
	extract ($params,EXTR_SKIP);

if ((empty($artist)) && (empty($tag))){
	return tra("Missing an ARTIST or a TAG.");
}

if (empty($artist)) {
	if (empty($tag)){
		return tra("Missing an ARTIST or a TAG.");
	} 

	if ($tag == 'tikiwiki') {
		$tunestitle = 'Tikiwiki Member Radio';
		$tunes = 'group/Tikiwiki';
	} else {
	$tunestitle = 'tagged ' .$tag;
	$tunes = 'globaltags/' . $tag;
 	}	

	} else {
	$tunestitle = 'like '. $artist;
	$tunes = 'artist/'.$artist.'/similarartists';
	}

// get defaults
	if (empty($color)) {
		$color = 'red';
	}

   if ($size == 'mini') {
   	$size = 'mini';
   	$width = '110';
   	
   	} else {

	if ((empty($size)) or ($size == 'regular')) {
		$size = 'regular';
		$width = '184';
		}
	}

   if (($autostart == 'y') or ($autostart == 'yes')) {
   	$autostart = 'autostart=1';
   	} else {
   	$autostart = 'autosart=';
   	}

$asetup = "<style type=\"text/css\">table.lfmWidget td {margin:0 !important;padding:0 !important;border:0 !important;}table.lfmWidget tr.lfmHead a:hover {background:url(http://cdn.last.fm/widgets/images/en/header/radio/".$size."_".$color.".png) no-repeat 0 0 !important;}table.lfmWidget tr.lfmEmbed object {float:left;}table.lfmWidget tr.lfmFoot td.lfmConfig a:hover {background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat 0px 0 !important;;}table.lfmWidget tr.lfmFoot td.lfmView a:hover {background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat -85px 0 !important;}table.lfmWidget tr.lfmFoot td.lfmPopup a:hover {background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat -159px 0 !important;}</style>";

$asetup .= "<table class=\"lfmWidget\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"width:".$width."px;\"><tr class=\"lfmHead\"><td><a title=\"Music $tunestitle\" href=\"http://www.last.fm/listen/$tunes\" target=\"_blank\" style=\"display:block;overflow:hidden;height:20px;width:".$width."px;background:url(http://cdn.last.fm/widgets/images/en/header/radio/".$size."_$color.png) no-repeat 0 -20px;text-decoration:none;border:0;\"></a></td></tr><tr class=\"lfmEmbed\"><td><object type=\"application/x-shockwave-flash\" data=\"http://cdn.last.fm/widgets/radio/19.swf\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"$width\" height=\"140\" > <param name=\"movie\" value=\"http://cdn.last.fm/widgets/radio/19.swf\" /> <param name=\"flashvars\" value=\"lfmMode=radio&amp;radioURL=$tunes&amp;title=Music+$tunestitle&amp;theme=$color&amp;$autostart=1&amp;lang=en&amp;widget_id=\" /> <param name=\"bgcolor\" value=\"d01f3c\" /> <param name=\"quality\" value=\"high\" /> <param name=\"allowScriptAccess\" value=\"always\" /> <param name=\"allowNetworking\" value=\"all\" /> </object></td></tr>";

$asetup .= "<tr class=\"lfmFoot\"><td style=\"background:url(http://cdn.last.fm/widgets/images/footer_bg/$color.png) repeat-x 0 0;text-align:right;\"><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"width:".$width."px;\"><tr><td class=\"lfmConfig\"><a href=\"http://www.last.fm/widgets/?url=$tunes&amp;colour=$color&amp;size=$size&amp;autostart=$autostart&amp;from=code&amp;widget=radio\" title=\"Get your own widget\" target=\"_blank\" style=\"display:block;overflow:hidden;width:85px;height:20px;float:right;background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat 0px -20px;text-decoration:none;border:0;\"></a></td>";

if ($size == 'regular') {
	$asetup .= "<td class=\"lfmView\" style=\"width:74px;\"><a href=\"http://www.last.fm/\" title=\"Visit Last.fm\" target=\"_blank\" style=\"display:block;overflow:hidden;width:74px;height:20px;background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat -85px -20px;text-decoration:none;border:0;\"></a></td>";
}

$asetup .= "<td class=\"lfmPopup\" style=\"width:25px;\"><a href=\"http://www.last.fm/widgets/popup/?url=$tunes&amp;colour=$color&amp;size=$size&amp;$autostart=1&amp;from=code&amp;widget=radio&amp;resize=1\" title=\"Load this radio in a pop up\" target=\"_blank\" style=\"display:block;overflow:hidden;width:25px;height:20px;background:url(http://cdn.last.fm/widgets/images/en/footer/".$color."_np.png) no-repeat -159px -20px;text-decoration:none;border:0;\" onclick=\"window.open(this.href + '&amp;resize=0','lfm_popup','height=240,width=240,resizable=yes,scrollbars=yes'); return false;\"></a></td></tr></table></td></tr></table>";

	return $asetup;
}



?>
