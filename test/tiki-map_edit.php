<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-map_edit.php,v 1.30.2.1 2008-02-27 00:09:14 franck Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/stats/statslib.php');
include_once ('lib/map/maplib.php');

if(!isset($prefs['feature_maps']) or $prefs['feature_maps'] != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("error.tpl");
  die;
}

if($tiki_p_map_view != 'y') {
  $smarty->assign('errortype', 401);
  $smarty->assign('msg',tra("You do not have permissions to view the maps"));
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["mode"])) {
	$mode = 'listing';
} else {
	$mode = $_REQUEST['mode'];
}

// Validate to prevent editing any file
if (isset($_REQUEST["mapfile"])) {
	if (strstr($_REQUEST["mapfile"], '..')) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to do that"));
		$smarty->display('error.tpl');
		die;
	}
}
if (!isset($prefs['map_path']) or !$prefs['map_path']) {
	$smarty->assign('msg', tra("Maps feature is not correctly setup : Maps path is missing."));
	$smarty->display('error.tpl');
	die;
}

if (!is_dir($prefs['map_path'])) {
	$smarty->assign('msg', tra('Please create a directory named '.$prefs['map_path'].' to hold your map files.'));
	$smarty->display('error.tpl');
	die;							
}

$smarty->assign('tiki_p_map_create', $tiki_p_map_create);

$smarty->assign('title',tra("Mapfiles"));

if (isset($_REQUEST["create"]) && ($tiki_p_map_create == 'y')) {
	$newmapfile = $prefs['map_path'].$_REQUEST["newmapfile"];

	if (!preg_match('/\.map$/i', $newmapfile) || preg_match('/\.\./', $_REQUEST["newmapfile"])) {
		$smarty->assign('msg', tra("mapfile name incorrect"));

		$smarty->display("error.tpl");
		die;
	}
  ini_set("display_errors","0");
	$fp = @fopen($newmapfile, "r");
  ini_set("display_errors","1");
	if ($fp) {
		$smarty->assign('msg', tra("This mapfile already exists"));

		$smarty->display("error.tpl");
		fclose ($fp);
		die;
	}
  ini_set("display_errors","0");
	$fp = fopen($newmapfile, "w");
  ini_set("display_errors","1");
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to write to the mapfile"));

		$smarty->display("error.tpl");
		die;
	}
	
	//Create standard header
	fwrite ($fp,"##TIKIMAPS HEADER: DO NOT MODIFY##\n");
	fwrite ($fp,"#Mapfile created by tikiwiki\n");
	fwrite ($fp,"#\n");
	fwrite ($fp,"#Modified by: ".$user."\n");
	fwrite ($fp,"#GMT Date: ".gmdate("Ymd His")."\n");
	fwrite ($fp,"#IP: ".$_REQUEST["REMOTE_ADDR"]."\n");
	fwrite ($fp,"#\n");
	fwrite ($fp,"##TIKIMAPS HEADER: END##\n");
	fwrite ($fp,"\n");
	fwrite ($fp,"MAP\n");
	fwrite ($fp,"\n");
	fwrite ($fp,"END\n");
	fclose ($fp);
}

$smarty->assign('tiki_p_map_delete', $tiki_p_map_delete);
if ((isset($_REQUEST["delete"])) && ($tiki_p_map_delete == 'y')) {
	$area = 'delmap';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		key_check($area);
		if(!unlink($prefs['map_path'].$_REQUEST["mapfile"])) {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra("You do not have permission to delete the mapfile"));
			$smarty->display("error.tpl");  
			die;
		}
		$mode='listing';
	} else {
		key_get($area);
	}
}

// Save the mapfile
if (isset($_REQUEST["save"])) {
if ($tiki_p_map_edit != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

	if (!preg_match('/\.map$/i', $_REQUEST["mapfile"])) {
		$smarty->assign('msg', tra("mapfile name incorrect"));
		$smarty->display("error.tpl");
		die;
	}
	

  
  //Get the revision number
  // Get mapfiles from the mapfiles directory
	$files=$maplib->listMapsWithRev($prefs['map_path']);
	
	for ($i=0;$i<count($files);$i++) {
  	if (substr($files[$i],0,strlen($_REQUEST["mapfile"]))==$_REQUEST["mapfile"]) {
  		$suffix=substr($files[$i],strlen($_REQUEST["mapfile"]));
  		$revision=".".sprintf("%04d",intval(substr($suffix,1))+1);
  	}
  }
	
	ini_set("display_errors","0");
  if (!copy($prefs['map_path'].$_REQUEST["mapfile"],$prefs['map_path'].$_REQUEST["mapfile"].$revision)) {
		$smarty->assign('msg', tra("I could not make a copy"));
		$smarty->display("error.tpl");
		die;  
  }
	$fp = fopen($prefs['map_path'].$_REQUEST["mapfile"], "w");
  ini_set("display_errors","1");
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to write to the mapfile"));
		$smarty->display("error.tpl");
		die;
	}

	//Create standard header
	fwrite ($fp,"##TIKIMAPS HEADER: DO NOT MODIFY##\n");
	fwrite ($fp,"#Mapfile created by tikiwiki\n");
	fwrite ($fp,"#\n");
	fwrite ($fp,"#Modified by: ".$user."\n");
	fwrite ($fp,"#GMT Date: ".gmdate("Ymd His")."\n");
	fwrite ($fp,"#IP: ".$_REQUEST["REMOTE_ADDR"]."\n");
	fwrite ($fp,"#\n");
	$mapfiledata=strstr($_REQUEST["pagedata"],"##TIKIMAPS HEADER: END##");
	// if the header is not found
	if ($mapfiledata=="") {
		$mapfiledata="##TIKIMAPS HEADER: END##\n\n".$_REQUEST["pagedata"];
	}
	fwrite($fp, $mapfiledata);
	fclose ($fp);
	
	if ($prefs['feature_user_watches'] == 'y') {
	  $nots = $tikilib->get_event_watches('map_changed', $_REQUEST["mapfile"]);

	  foreach ($nots as $not) {

		     $smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);

		     $smarty->assign('mail_page', $_REQUEST["mapfile"]);
		     $smarty->assign('mail_date', date("U"));
		     $smarty->assign('mail_user', $user);
		     $smarty->assign('mail_hash', $not['hash']);
		     $foo = parse_url($_SERVER["REQUEST_URI"]);
		     $machine = $tikilib->httpPrefix(). $foo["path"];
		     $smarty->assign('mail_machine', $machine);
		     $parts = explode('/', $foo['path']);

		     if (count($parts) > 1)
			      unset ($parts[count($parts) - 1]);

		     $smarty->assign('mail_machine_raw', $tikilib->httpPrefix(). implode('/', $parts));
		     $mail_data = $smarty->fetch('mail/user_watch_map_changed.tpl');
		     @mail($not['email'], tra('Map'). ' ' . $_REQUEST["mapfile"] . ' ' . tra('changed'), $mail_data, "From: ".$prefs['sender_email']."\r\nContent-type: text/plain;charset=utf-8\r\n");
	  }
	}
	
}

if ((isset($_REQUEST["mapfile"])) && ($mode=='editing')) {
if ($tiki_p_map_edit != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
 $mapfile = $prefs['map_path'] .$_REQUEST["mapfile"];
  ini_set("display_errors","0"); 
	$fp = fopen($mapfile, "r");
  ini_set("display_errors","1");
	if (!$fp) {
		$smarty->assign('errortype', 401);
		$smarty->assign('msg', tra("You do not have permission to read the mapfile"));
		$smarty->display("error.tpl");
		die;
	}
	if (filesize($mapfile)>0) {
		$pagedata = file_get_contents($mapfile,FALSE);
		fclose ($fp);
	} else {
		$pagedata="";
	}
	$smarty->assign('pagedata', $pagedata);
	$smarty->assign('mapfile', $_REQUEST["mapfile"]);
}

$smarty->assign('mode', $mode);

// Get mapfiles from the mapfiles directory
$files=$maplib->listMaps($prefs['map_path']);

$mapstats = array();
for ($i=0;$i<count($files);$i++) {
	$mapstats[] = $statslib->object_hits($files[$i],"map");
	$mapstats7days[] = $statslib->object_hits($files[$i],"map",7);
}
		
$smarty->assign('files', $files);
$smarty->assign('mapstats', $mapstats);
$smarty->assign('mapstats7days', $mapstats7days);
$smarty->assign('tiki_p_map_edit', $tiki_p_map_edit);

// Watches
if($prefs['feature_user_watches'] == 'y') {
	if($user && isset($_REQUEST['watch_event'])) {
	  if($_REQUEST['watch_action']=='add') {
	    $tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'Map',$_REQUEST['watch_object'],"tiki-map.php?mapfile=".$_REQUEST['watch_object']);
	  } else {
	    $tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object']);
	  }
	}

 $user_watching_map = array();
 foreach ($files as $key => $value) {
	  $user_watching_map[$key]='n';
	  if($user && $tikilib->user_watches($user,'map_changed',$value,'Map')) { 
		  $user_watching_map[$key]='y';
	  }
	}
	$smarty->assign('user_watching_map',$user_watching_map);
}	


$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-map_edit.php", "tiki-map.php", $foo["path"]);
$smarty->assign('url_browse', $tikilib->httpPrefix(). $foo1);


include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','','maps');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
$smarty->assign('quicktagscant', $quicktags["cant"]);

ask_ticket('edit-map');

$section = 'maps';
include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

if (isset($_REQUEST["mapfile"])) {
	$smarty->assign('title',tra("map edit")." ".$_REQUEST["mapfile"]);
}

// Get templates from the templates/modules directory
$smarty->assign('mid', 'map/tiki-map_edit.tpl');
$smarty->display("tiki.tpl");

?>
