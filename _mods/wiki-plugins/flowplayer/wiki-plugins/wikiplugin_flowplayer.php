<?php

function wikiplugin_flowplayer_help() {
        return tra("Displays a FlowPlayer based Flash Video Player on the wiki page").":<br />~np~{FLOWPLAYER(videofile=url_to_flv_video,configfile=url_to_flowplayer_js_configfile,player=standard|longplay|skinnable,autoplay=true|false,title=text,width=pixel,height=pixel)}{FLASH}~/np~";
}

function wikiplugin_flowplayer($data, $params) {

	extract ($params,EXTR_SKIP);

	if ((!isset($videofile)) && (!isset($configfile))) return "Specify videofile or configfile parameter to use and setup flowplayer";

	if (!isset($title)) $title='FlowPlayer Video';
	if (!isset($autoplay)) $autoplay='true';
	if (!isset($autoload)) $autoload='true';
	if (!isset($player)) $player='normal';
	if (!isset($width)) $width=320;
	if (!isset($height)) $height=263;

	if ((isset($player)) && ($player=='longplay')) {
	if (!isset($engine)) $engine='/lib/flowplayer/FlowPlayerLP.swf';
	} elseif ((isset($player)) && ($player=='skinnable')) {
	if (!isset($engine)) $engine='/lib/flowplayer/FlowPlayerLight.swf';
	}else {
	if (!isset($engine)) $engine='/lib/flowplayer/FlowPlayer.swf';
	}

	if (!isset($configfile)) $configfile='/lib/flowplayer/flowPlayer.js';


	$flowplayer = "<OBJECT WIDTH=\"$width\" HEIGHT=\"$height\" DATA=\"$engine\" TYPE=\"application/x-shockwave-flash\">";
	$flowplayer .= "<param name=\"allowScriptAccess\" value=\"sameDomain\" />";
	$flowplayer .= "<param name=\"movie\" value=\"$engine\" />";
	$flowplayer .= "<param name=\"quality\" value=\"high\" />";
	$flowplayer .= "<param name=\"scale\" value=\"noScale\" />";
	$flowplayer .= "<param name=\"wmode\" value=\"transparent\" />";
	if (isset($videofile)) {
	$flowplayer .= "<param name=\"flashvars\" value=\"videoFile=$videofile\"></param></OBJECT>"; 
	}
	else {
	$flowplayer .= "<param name=\"flashvars\" value=\"configFileName=$configfile\"></param></OBJECT>"; 
	}

	return $flowplayer;
}

?>
