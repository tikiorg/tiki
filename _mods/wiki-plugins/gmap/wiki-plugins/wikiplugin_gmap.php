<?php
/*
Name:			Google Map page plugin for Tiki Wiki/CMS/Groupware. 
Description:	Creates a Google Map to map a geo-coded location of a wiki page, backlinked pages, or pages in a structure.
				See notes below for requirements and additional instructions.
Author:			Nelson Ko (nelson@wordmaster.org)
License:		LGPL
Version:		1.2 ( 2007-08-07 ) Critical bugfix to wrong URL when marker is clicked leading to 404.
Previous Versions: 1.0/1.1 ( 2007-06-30)

Syntax:

{GMAP()}{GMAP} or {GMAP()/}, with optional parameters separated by commas in brackets.

Possible parameters:

type=>page (default) : maps the current single page 
type=>backlinks: maps the backlinks of the current page
type=>structure: maps the pages in the structure that the current page is in
type=>route: maps the pages in the structure that the current page is in and draws a line between points as in a route
width=>number (default: 500)
height=>number (default: 400)
controller=>large (default): this contains pan/zoom buttons and clickable zoom scale
controller=>medium: this contains pan/zoom buttons but no clickable zoom scale
controller=>small: this contains zoom buttons only
controller=>none: the controller is disabled
changetype=>n (default: y): the buttons to change the mode of the map can be disabled
scale=>n (default: y): the display of the map scale can be disabled
mode=>normal (default): start the map in normal streetmap mode
mode=>satellite: start the map in satellite mode
mode=>hybrid: start the map in satellite/streetmap hybrid mode

Requirements and additional instructions:

These steps should be taken care off through the mods deployment process. If not the manual steps required are:

1. This file is to be deployed to <tikiroot>/lib/wiki-plugins/

2. Included gmapwikipluginlib.php should be deployed to <tikiroot>/lib/wiki-plugins/

3. Included gmap_wikiplugin_save.php should be deployed to <tikiroot>/

4. The following included smarty templates should be deployed to <tikiroot>/templates/wiki-plugins/
wikiplugin-backlinkgmap.tpl
wikiplugin-gmap.tpl
wikiplugin-routegmap.tpl
wikiplugin-structuregmap.tpl

5. The following SQL command is to be executed to create the necessary database table:
CREATE TABLE `wikiplugin_gmap` (
	`pageid` int(14) NOT NULL,
	`pref` varchar(40) NOT NULL,
	`value` varchar(250) default NULL,
	PRIMARY KEY  (`pageid`,`pref`)
) TYPE=MyISAM;

6. In order for route type to work and for the google maps polylines to work, the VML namespace
must be specified in the html tag in <tikiroot>/templates/header.tpl, like this:
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

7. When using the plugin, click on the "+" icon next to the text at the top to access the geocoding information.
The coordinates are automatically into the fields there through mouse clicks on the map.

8. Note that having multiple google maps on the same page is not recommended as it can cause unpredictable results.

*/

function wikiplugin_gmap_help() {
        return tra("Google map for wiki page").":<br />~np~{GMAP()}{GMAP} or {GMAP() /}~/np~";
}

function wikiplugin_gmap($data, $params) {

	extract ($params,EXTR_SKIP);
	global $gmapwikipluginlib;
	if (!is_object($gmapwikipluginlib)) {
			require_once('lib/wiki-plugins/gmapwikipluginlib.php');
	}

	$repl = '';
 	global $gmap_defaultx,$gmap_defaulty,$gmap_defaultz;
 	global $tikilib,$structlib,$smarty,$user;
 	global $page,$backlinks,$feature_backlinks,$page_ref_id;
 	
 	$smarty->assign('page',$page);
 	$gmap_defaultx = $tikilib->get_user_preference($user,'gmap_defx',$gmap_defaultx);
	$gmap_defaulty = $tikilib->get_user_preference($user,'gmap_defy',$gmap_defaulty);
	$gmap_defaultz = $tikilib->get_user_preference($user,'gmap_defz',$gmap_defaultz);
	$smarty->assign('gmap_defaultx',$gmap_defaultx);
	$smarty->assign('gmap_defaulty',$gmap_defaulty);
	$smarty->assign('gmap_defaultz',$gmap_defaultz);			
	
	if (isset($width)) {	
		$smarty->assign('width',$width);
	} else {
		$smarty->assign('width','500');
	}
	if (isset($height)) {	
		$smarty->assign('height',$height);		
	} else {
		$smarty->assign('height','400');		
	}	
	if (!isset($controller) || isset($controller) && $controller == 'large') {	
		$smarty->assign('controller','large');		
	} elseif ($controller == 'medium') {
		$smarty->assign('controller','medium');
	}	elseif ($controller == 'small') {
		$smarty->assign('controller','small');
	} else {
		$smarty->assign('controller','none');
	}
	if (!isset($mode) || isset($mode) && $mode == 'normal') {	
		$smarty->assign('mode','normal');		
	} elseif ($mode == 'satellite') {
		$smarty->assign('mode','satellite');		
	}	else {
		$smarty->assign('mode','hybrid');		
	} 	
		
	if (!isset($changetype) || isset($changetype) && $changetype == 'y') {	
		$smarty->assign('changetype','y');		
	} else {
		$smarty->assign('changetype','n');
	}	
	if (!isset($scale) || isset($scale) && $scale == 'y') {	
		$smarty->assign('scale','y');		
	} else {
		$smarty->assign('scale','n');
	}	
	$gmapinfowindow = $gmapwikipluginlib->get_page_preference($page,'gmapinfowindow','');
	$smarty->assign('gmapinfowindow',$gmapinfowindow);
		
	$pointx = $gmapwikipluginlib->get_page_preference($page,'lon','');
	$pointy = $gmapwikipluginlib->get_page_preference($page,'lat','');
	$pointz = $gmapwikipluginlib->get_page_preference($page,'zoom',$gmap_defaultz);
	$smarty->assign('pointx',$pointx);
	$smarty->assign('pointy',$pointy);
	$smarty->assign('pointz',$pointz);
	
	if (!isset($type) || $type == 'page') {						
 		$repl = $smarty->fetch("wiki-plugins/wikiplugin-gmap.tpl");
 	}
	
	if (isset($type) && $type == 'backlinks' && $feature_backlinks == 'y' && $backlinks) {		
		$backlinkgmapx = $gmapwikipluginlib->get_page_preference($page,'backlinkgmaplon','');
		$backlinkgmapy = $gmapwikipluginlib->get_page_preference($page,'backlinkgmaplat','');
		$backlinkgmapz = $gmapwikipluginlib->get_page_preference($page,'backlinkgmapzoom',$gmap_defaultz);			
		$smarty->assign('backlinkgmapx',$backlinkgmapx);
		$smarty->assign('backlinkgmapy',$backlinkgmapy);
		$smarty->assign('backlinkgmapz',$backlinkgmapz);			
		$out = array();			
		foreach ($backlinks as $bk) {						
			$t_lon = $gmapwikipluginlib->get_page_preference($bk["fromPage"],'lon','');
			$t_lat = $gmapwikipluginlib->get_page_preference($bk["fromPage"],'lat','');
			$t_desc = $gmapwikipluginlib->get_page_preference($bk["fromPage"],'gmapinfowindow','');					
			// now to add to out if longitude and latitude is properly set
			if ($t_lon and $t_lon < 180 and $t_lon > -180 and $t_lat and $t_lat < 180 and $t_lat > -180) {
				$t_lon = number_format($t_lon,5);
				$t_lat = number_format($t_lat,5);		
    		$image = '';
    		// no image here required, so leave it as blank in case it is needed later on.
    		$thetext = addslashes($image).tra('Page').': '.htmlspecialchars($bk["fromPage"],ENT_QUOTES).'<br />Lat: '.$t_lat.'&deg;<br /> Long: '.$t_lon.'&deg;<br />'.$t_desc;
    		$href = 'tiki-index.php?page='.urlencode($bk["fromPage"]);
				$out[] = array($t_lat,$t_lon,$thetext,$href);
			}
		}
		$smarty->assign('users',$out);			
	  $repl = $smarty->fetch("wiki-plugins/wikiplugin-backlinkgmap.tpl");	  	  
	}  	
	
	if (isset($type) && ($type == 'route' || $type == 'structure') && $structlib->page_is_in_structure($page)) {
  	$navigation_info = $structlib->get_navigation_info($page_ref_id);
  	$subtree = $structlib->get_subtree($navigation_info["home"]["page_ref_id"]);	  	  
  	$groute = array();
  	foreach ($subtree as $ts) {
  		if (!($ts["last"] && !$ts["first"])) {
  			// handle dummy last entry
  			$groute[] = $ts;
  		}
  	}		  	
		$groutex = $gmapwikipluginlib->get_page_preference($navigation_info["home"]["pageName"],'groutelon','');
		$groutey = $gmapwikipluginlib->get_page_preference($navigation_info["home"]["pageName"],'groutelat','');
		$groutez = $gmapwikipluginlib->get_page_preference($navigation_info["home"]["pageName"],'groutezoom',$gmap_defaultz);			
		$smarty->assign('groutex',$groutex);
		$smarty->assign('groutey',$groutey);
		$smarty->assign('groutez',$groutez);			
		$out = array();			
		foreach ($groute as $gr) {						
			$t_lon = $gmapwikipluginlib->get_page_preference($gr["pageName"],'lon','');
			$t_lat = $gmapwikipluginlib->get_page_preference($gr["pageName"],'lat','');
			$t_desc = $gmapwikipluginlib->get_page_preference($gr["pageName"],'gmapinfowindow','');					
			// now to add to out if longitude and latitude is properly set
			if ($t_lon and $t_lon < 180 and $t_lon > -180 and $t_lat and $t_lat < 180 and $t_lat > -180) {
				$t_lon = number_format($t_lon,5);
				$t_lat = number_format($t_lat,5);		
   			$image = '';
   			// no image here required, so leave it as blank in case it is needed later on.
   			$thetext = addslashes($image).tra('Page').': '.$gr["pos"].'. '.htmlspecialchars($gr["pageName"],ENT_QUOTES).'<br />Lat: '.$t_lat.'&deg;<br /> Long: '.$t_lon.'&deg;<br />'.$t_desc;
   			$href = 'tiki-index.php?page_ref_id='.$gr["page_ref_id"];
				$out[] = array($t_lat,$t_lon,$thetext,$href);
			}
		}
		$smarty->assign('users',$out);			
		if ($type == 'route') {
  		$repl = $smarty->fetch("wiki-plugins/wikiplugin-routegmap.tpl");
  	} elseif ($type == 'structure') {
  		$repl = $smarty->fetch("wiki-plugins/wikiplugin-structuregmap.tpl");
  	}
  }
	
	
	return '~pp~' . $repl . '~/pp~';

}

?>
