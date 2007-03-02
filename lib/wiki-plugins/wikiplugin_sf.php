<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_sf.php,v 1.7 2007-03-02 19:49:06 luciash Exp $
 *
 * Tikiwiki SF auto-links.
 * 
 * Syntax:
 * 
 *  {SF([groupid=>groupid,][atid=>atid,][tag=>tag,]aid=>aid)}{SF}
 # for tikiwiki, the groupid is 64258
 # bugtracker atid is 506846
 # rfe atid is 506849
 # support atid is 506847
 # patches atid is 506848
 # 
 */
define('SF_CACHE',48); # in hours
define('DEFAULT_TAG','bugs');

function wikiplugin_sf_help() {
	return tra("Automatically creates a link to the appropriate SourceForge object").":<br />~np~{SF(aid=>,adit=>,groupid=>)}".tra("text")."{SF}~/np~";
}

function get_artifact_label($gid,$atid,$aid,$reload=false) {
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$cachefile = "temp/sftrackers.cache.$gid.$atid.$aid";
	$cachelimit = time() - 60*60*SF_CACHE;
	$url = "http://sourceforge.net/tracker/index.php?func=detail&amp;aid=$aid&amp;group_id=$gid&amp;atid=$atid";
	if (!is_file($cachefile)) $reload = true;
	$back = false;
	if ($reload or (filemtime($cachefile) < $cachelimit)) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$buffer = curl_exec ($ch);
		curl_close ($ch);
		if (preg_match("/<title>[^-]*-([^<]*)<\/title>/i",$buffer,$match)) {
			$fp = fopen($cachefile,"wb");
			fputs($fp,$match[1]);
			fclose($fp);
		} elseif (is_file($cachefile)) {
			$fp = fopen($cachefile,"rb");
			$back = fgets($fp);
			fclose($fp);
		}
	} else {
		$fp = fopen($cachefile,"rb");
		$back = fgets($fp,4096);
		fclose($fp);
	}
	return $back;
}

function wikiplugin_sf($data, $params) {
	if (function_exists("curl_init")) {
	# customize that (or extract it in a db)
	$sftags['bugs'] = array('64258','506846');
	$sftags['rfe'] = array('64258','506849');
	$sftags['patches'] = array('64258','506848');
	$sftags['support'] = array('64258','506847');
	$sftags['jgbugs'] = array('43118','435210');
	$sftags['jgsupport'] = array('43118','435211');
	$sftags['jgrfe'] = array('43118','435213');

	extract ($params,EXTR_SKIP);
	
	if (isset($tag) and isset($sftags["$tag"]) and is_array($sftags["$tag"])) {
		list($sf_group_id,$sf_atid) = $sftags["$tag"];
	} else {
		$sf_group_id = (isset($groupid)) ? "$groupid" : $sftags[DEFAULT_TAG][0];
		$sf_atid = (isset($atid)) ? "$atid" : $sftags[DEFAULT_TAG][1];
		$tag = DEFAULT_TAG;
	}
	if (!isset($aid)) {
		//return "__please use (aid=>xxx) as parameters__";
		return "<b>please use (aid=>xxx) as parameters</b>";
	}
	$label = get_artifact_label($sf_group_id,$sf_atid,$aid);
	//$back = "[http://sf.net/tracker/index.php?func=detail&amp;aid=$aid&amp;group_id=$sf_group_id&amp;atid=$sf_atid|$tag:#$aid: $label|nocache]";
	$back = "<a href='http://sf.net/tracker/index.php?func=detail&amp;aid=$aid&amp;group_id=$sf_group_id&amp;atid=$sf_atid' target='_blank' title='$tag:#$aid' class='wiki'>$label</a>";
	} else {
		$back = "SF plugin : You need php-curl module to be loaded to use that feature.";
	}

	return $back;
}

?>
