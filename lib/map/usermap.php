<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/map/usermap.php,v 1.2 2004-08-12 22:31:42 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

	$miffile=$map_path."data/user.mif";
	$midfile=$map_path."data/user.mid";
	
	$symbol="Symbol (34,16711680,9)";
	$pres=1000000;	//precision for clamping lat/lon data

	$fdmif=@fopen($miffile,"w");
	if (!$fdmif) {
	  $smarty->assign('msg',tra("Could not create user.mif in data directory"));
	  $smarty->display("error.tpl");
	  die;
	}	
	$fdmid=@fopen($midfile,"w");
	if (!$fdmid) {
	  $smarty->assign('msg',tra("Could not create user.mid in data directory"));
	  $smarty->display("error.tpl");
	  die;
	}
	fwrite($fdmif,"Version 300\n");
	fwrite($fdmif,"Charset \"WindowsLatin1\"\n");
	fwrite($fdmif,"Delimiter \",\"\n");
	fwrite($fdmif,"CoordSys Earth Projection 1, 104\n");
	fwrite($fdmif,"Columns 2\n");
	fwrite($fdmif,"  user Char(20)\n");
  	fwrite($fdmif,"  realName Char(100)\n");
	fwrite($fdmif,"Data\n");

	$query = "select * from `users_users`";
	$result = $tikilib->query($query, array());
	while ($res = $result->fetchRow()) {
		$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'lat')";
		$lat = $tikilib->getOne($query,array($res["login"]));
		$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'lon')";
		$lon = $tikilib->getOne($query,array($res["login"]));
		$query = "select `value` from `tiki_user_preferences` where (`user` = ?) and (`prefName` = 'realName')";
		$realName = $tikilib->getOne($query,array($res["login"]));
		
		if (isset($lat) && isset($lon)) {
			if ($lat>=0) {
				$lat=(($lat*$pres) % (90*$pres))/$pres;
			} else {
				$lat=-((-$lat*$pres) % (90*$pres))/$pres;
			}
			if ($lon>=0) {
				$lon=(($lon*$pres) % (180*$pres))/$pres;
			} else {
				$lon=-((-$lon*$pres) % (180*$pres))/$pres;
			}
			if (!isset($realName)) {
				$realName="";
			}
			fwrite($fdmid,"\"".substr($res["login"],0,20)."\",\"".substr($realName,0,100)."\"\n");
			fwrite($fdmif,"Point ".$lon." ".$lat."\n");
			fwrite($fdmif,"   ".$symbol."\n");
		}
		
	}

	fclose($fdmid);
	fclose($fdmif);
	
	if (is_file($map_path."/data/user.dbf")) {
		unlink($map_path."/data/user.dbf");
	}
	if (is_file($map_path."/data/user.prj")) {
		unlink($map_path."/data/user.prj");
	}
	if (is_file($map_path."/data/user.shp")) {
		unlink($map_path."/data/user.shp");
	}
	if (is_file($map_path."/data/user.shx")) {
		unlink($map_path."/data/user.shx");
	}
	
	$ret=exec($ogr2ogr." -f \"ESRI Shapefile\" ".$map_path."data/user.shp ".$map_path."data/user.mif");
	
?>
