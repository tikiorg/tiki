<?php

// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/xspf_player/wiki-plugins/wikiplugin_xspf.php,v 1.1 2006-11-29 12:07:33 ang23 Exp $

// Wiki plugin to display a SWF file
// damian aka damosoft 30 March 2004

function wikiplugin_xspf_help() {
        return tra("Displays an XSPF based Flash Music Player on the wiki page").":<br />~np~{XSPF(song=url_to_mp3,playlist=url_to_xspf_playlist,player=full|slim|button,autoplay=true|false,title=text)}{XSPF}~/np~";
}

function wikiplugin_xspf($data, $params) {

	extract ($params,EXTR_SKIP);

	if ((!isset($playlist)) && (!isset($song))) return "Specify playlist or song parameter url to use and setup xspf";

	if (!isset($title)) $title='XSPF Player';
	if (!isset($autoplay)) $autoplay='true';
	if (!isset($autoload)) $autoload='true';
	if (!isset($player)) $player='slim';

	if ($player=='full') {
	if (!isset($width)) $width=400;
	if (!isset($height)) $height=168;
	if (!isset($movie)) $movie='/lib/xspf_player/xspf_player.swf';
	} elseif ($player=='button') {
	if (!isset($width)) $width=18;
	if (!isset($height)) $height=18;
	if (!isset($movie)) $movie='/lib/xspf_player/musicplayer.swf';
	} else {
	if (!isset($width)) $width=400;
	if (!isset($height)) $height=16;
	if (!isset($movie)) $movie='/lib/xspf_player/xspf_player_slim.swf';
	}

	if (!isset($playlist)) $playlist='/lib/xspf_player/playlist.xspf';

	if (isset($song)) {
	    $xspf = "<OBJECT WIDTH=\"$width\" HEIGHT=\"$height\" DATA=\"$movie?song_url=$song&autoplay=$autoplay&autoload=$autoload&song_title='$title'\" TYPE=\"application/x-shockwave-flash\">";
	$xspf .= "<PARAM VALUE=\"$movie?song_url=$song&autoplay=$autoplay&autoload=$autoload&song_title='$title'\" NAME=\"movie\"></PARAM></OBJECT>"; }
	else {
	$xspf = "<OBJECT WIDTH=\"$width\" HEIGHT=\"$height\" DATA=\"$movie?playlist_url=$playlist&autoplay=$autoplay&autoload=$autoload&player_title='$title'\" TYPE=\"application/x-shockwave-flash\">";
	$xspf .= "<PARAM VALUE=\"$movie?playlist_url=$playlist&autoplay=$autoplay&autoload=$autoload&player_title='$title'\" NAME=\"movie\"></PARAM></OBJECT>";
	}

	return $xspf;
}

?>
