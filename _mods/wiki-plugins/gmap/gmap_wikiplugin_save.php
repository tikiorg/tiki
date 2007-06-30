<?php

/*

Name:			Save metadata to database for "Google Map page plugin for Tiki Wiki/CMS/Groupware."
Description:	Creates a Google Map to map a geo-coded location of a wiki page, backlinked pages, or pages in a structure.
				See notes below for requirements and additional instructions.
Author:			Nelson Ko (nelson@wordmaster.org)
License:		LGPL
Version:		1.0 ( 2007-06-30)

Refer to main file wikiplugin_gmap.php in <tikiroot>/lib/wiki-plugins/ for more info
This file is needed for that, and should be deployed to <tikiroot>/

*/

require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');
include_once('lib/wiki-plugins/gmapwikipluginlib.php');

if ($feature_gmap != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').": feature_gmap");
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["page_ref_id"])) {
    // If a structure page has been requested
    $page_ref_id = $_REQUEST["page_ref_id"];
} else {
    // else check if page is the head of a structure 
    $page_ref_id = $structlib->get_struct_ref_if_head($page);
}

if ($page_ref_id) {   
    $page_info = $structlib->s_get_page_info($page_ref_id); 
    $navigation_info = $structlib->get_navigation_info($page_ref_id);    
    $page = $page_info["pageName"];
} elseif (isset($_REQUEST["page"])) {
	  $page = $_REQUEST["page"];	  
} else {
		$smarty->assign('msg', tra("Please provide a name for the page."));
		$smarty->display("error.tpl");
		die;
}
    
if (!$tikilib->user_has_perm_on_object($user, $page,'wiki page', 'tiki_p_edit')) {
			$smarty->assign('msg', tra("Permission denied you cannot edit this page"));
			$smarty->display("error.tpl");
			die;
}			

// Save Google Maps point for this page
if (isset($_POST['pointx']) && trim($_POST['pointx']) != ""
	&& isset($_POST['pointy']) && trim($_POST['pointy']) != "" 
	&& isset($_POST['pointz']) && trim($_POST['pointz']) != "") {
	if (isset($_POST['remove_geocode'])) {
		$gmapwikipluginlib->set_page_preference($page,'lon','');
		$gmapwikipluginlib->set_page_preference($page,'lat','');
		$gmapwikipluginlib->set_page_preference($page,'zoom','');
		$gmapwikipluginlib->set_page_preference($page,'gmapinfowindow','');
	} elseif (isset($_POST['save_gmap'])) {
		$gmapwikipluginlib->set_page_preference($page,'lon',$_POST['pointx']);
		$gmapwikipluginlib->set_page_preference($page,'lat',$_POST['pointy']);
		$gmapwikipluginlib->set_page_preference($page,'zoom',$_POST['pointz']);			
		$gmapinfowindow = preg_replace("((\n|\r|\r\n)+)", "<br />", trim($_POST['gmapinfowindow']));
		$gmapwikipluginlib->set_page_preference($page,'gmapinfowindow',$gmapinfowindow);
	}
}

// Save Google Maps view for the backlink map for this page
if (isset($_POST['save_backlinkgmap']) && isset($_POST['backlinkgmapx']) && trim($_POST['backlinkgmapx']) != ""
	&& isset($_POST['backlinkgmapy']) && trim($_POST['backlinkgmapy']) != "" 
	&& isset($_POST['backlinkgmapz']) && trim($_POST['backlinkgmapz']) != "") {
	$gmapwikipluginlib->set_page_preference($page,'backlinkgmaplon',$_POST['backlinkgmapx']);
	$gmapwikipluginlib->set_page_preference($page,'backlinkgmaplat',$_POST['backlinkgmapy']);
	$gmapwikipluginlib->set_page_preference($page,'backlinkgmapzoom',$_POST['backlinkgmapz']);				
}

// Save Google Maps view for the groute map for this page
if (isset($_POST['save_groute']) && isset($_POST['groutex']) && trim($_POST['groutex']) != ""
	&& isset($_POST['groutey']) && trim($_POST['groutey']) != "" 
	&& isset($_POST['groutez']) && trim($_POST['groutez']) != "") {		
	$gmapwikipluginlib->set_page_preference($navigation_info["home"]["pageName"],'groutelon',$_POST['groutex']);
	$gmapwikipluginlib->set_page_preference($navigation_info["home"]["pageName"],'groutelat',$_POST['groutey']);
	$gmapwikipluginlib->set_page_preference($navigation_info["home"]["pageName"],'groutezoom',$_POST['groutez']);				
}

if ($page_ref_id) {
      header ("location: tiki-index.php?page_ref_id=$page_ref_id");
    } else {
      header ("location: tiki-index.php?page=$page");
    }
    
?>
