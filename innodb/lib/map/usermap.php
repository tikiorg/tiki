<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class MapsLib extends TikiLib
{

	function makeusermap() {
		global $prefs, $tikidomain, $smarty;

		if (!isset($prefs['ogr2ogr']) || !is_executable($prefs['ogr2ogr'])) {
			return (tra("No valid ogr2ogr executable"));
		} 
		// User preferences screen
		if ($prefs['feature_userPreferences'] != 'y') {
			return (tra('This feature is disabled').': '.$prefs['feature_userPreferences']);
		}
		
		$pres=100000; // Precision for lat lon convertion
		$tdo = "user";
		if ($tikidomain) $tdo = "$tikidomain.user";
		$miffile=$prefs['map_path']."data/$tdo.mif";
		$midfile=$prefs['map_path']."data/$tdo.mid";
		
		$symbol="Symbol (34,16711680,9)";
			  
  	$fdmif=@fopen($miffile,"w");
  	if (!$fdmif) {
		  return (tra("Could not create \$tdo.mif in data directory"));
		}	
		$fdmid=@fopen($midfile,"w");
		if (!$fdmid) {
		  return (tra("Could not create \$tdo.mid in data directory"));
		}
		
		fwrite($fdmif,"Version 300\n");
		fwrite($fdmif,"Charset \"WindowsLatin1\"\n");
		fwrite($fdmif,"Delimiter \",\"\n");
		fwrite($fdmif,"CoordSys Earth Projection 1, 104\n");
		fwrite($fdmif,"Columns 7\n");
		fwrite($fdmif,"  user Char(20)\n");
  	fwrite($fdmif,"  realName Char(100)\n");
  	fwrite($fdmif,"  gender Char(20)\n");
  	fwrite($fdmif,"  country Char(100)\n");
  	fwrite($fdmif,"  avatar Char(250)\n");
		fwrite($fdmif,"  Lon float\n");
		fwrite($fdmif,"  Lat float\n");
		fwrite($fdmif,"Data\n");

  	// Prepare the data
  	$query = "SELECT login,avatarType,avatarLibName,prefName,value FROM `users_users`,`tiki_user_preferences` WHERE `user`=`login` ";
		$result = $this->query($query, array());
		$res = $result->fetchRow();
		while ($res) {
			$login=substr($res["login"],0,20);
			unset($lat);
			unset($lon);
			
			// get the avatar
			$image = $tikilib->get_user_avatar( $res );
			$style = "style='float:left;margin-right:5px;'";
			$gender=tra("unknown");
			$country=tra("Other");
			while ($login==substr($res["login"],0,20) && $res) {
				if (!isset($res["value"]) or is_null($res["value"])) {
					$value="";
				} else {
				  $value=str_replace("\n","",$res["value"]);
				  $value=str_replace("\"","",$value);
				}
				if ($res["prefName"]=="lat") {
					$lat=$value;
				}
				if ($res["prefName"]=="lon") {
					$lon=$value;
				}
				if ($res["prefName"]=="realName") {
					$realName=$value;
				}
				if ($res["prefName"]=="gender") {
					$gender=substr(tra($value),0,20);
				}
				if ($res["prefName"]=="country") {
					$country=substr(tra($value),0,100);
				}
				$res = $result->fetchRow();
			}
			$realName=substr($realName,0,100);
			if (isset($lat) && isset($lon)) {
				if ($lat>=0) {
					$lat=(($lat*$pres) % (90*$pres))/$pres;
				} else {
					$lat=-((-$lat*$pres) % (90*$pres))/$pres;
				}
				if ($lon>=0) {
					$lon=(($lon*$pres) % (360*$pres))/$pres;
					if($lon>180) {$lon=$lon-360;}
				} else {
					$lon=-((-$lon*$pres) % (360*$pres))/$pres;
					if($lon<-180) {$lon=$lon+360;}
				}
				if ($prefs['mapzone']==360 && $lon<0) {
					$lon=360+$lon;
				}
				fwrite($fdmid,"\"".$login."\",");
				fwrite($fdmid,"\"".$realName."\",");
				fwrite($fdmid,"\"".$gender."\",");
				fwrite($fdmid,"\"".$country."\",");				
				fwrite($fdmid,"\"".$image."\",");
  			fwrite($fdmid,$lon.",".$lat."\n");
  			
				fwrite($fdmif,"Point ".$lon." ".$lat."\n");
				fwrite($fdmif,"   ".$symbol."\n");
			}
		}
		fclose($fdmid);
		fclose($fdmif);

		if (is_file($prefs['map_path']."/data/$tdo.dbf")) {
			unlink($prefs['map_path']."/data/$tdo.dbf");
		}
		if (is_file($prefs['map_path']."/data/$tdo.prj")) {
			unlink($prefs['map_path']."/data/$tdo.prj");
		}
		if (is_file($prefs['map_path']."/data/$tdo.shp")) {
			unlink($prefs['map_path']."/data/$tdo.shp");
		}
		if (is_file($prefs['map_path']."/data/$tdo.shx")) {
			unlink($prefs['map_path']."/data/$tdo.shx");
		}
	
		$ret=exec($prefs['ogr2ogr']." -f \"ESRI Shapefile\" ".$prefs['map_path']."data/$tdo.shp ".$prefs['map_path']."data/$tdo.mif");
		return (tra("User Map Generated in:").$prefs['map_path']."data/".$tdo.".shp");
	}

	function makeimagemap($tdo,$galleryId) {
		global $prefs, $tikidomain, $smarty;

		if (!isset($prefs['ogr2ogr']) || !is_executable($prefs['ogr2ogr'])) {
			return (tra("No valid ogr2ogr executable"));
		} 
		// User preferences screen
		if ($prefs['feature_userPreferences'] != 'y') {
			return (tra('This feature is disabled').': '.$prefs['feature_userPreferences']);
		}
		$miffile=$prefs['map_path']."data/$tdo.mif";
		$midfile=$prefs['map_path']."data/$tdo.mid";
		
		$symbol="Symbol (34,16711680,9)";
			  
  	$fdmif=@fopen($miffile,"w");
  	if (!$fdmif) {
		  return (tra("Could not create \$tdo.mif in data directory"));
		}	
		$fdmid=@fopen($midfile,"w");
		if (!$fdmid) {
		  return (tra("Could not create \$tdo.mid in data directory"));
		}
		
		fwrite($fdmif,"Version 300\n");
		fwrite($fdmif,"Charset \"WindowsLatin1\"\n");
		fwrite($fdmif,"Delimiter \",\"\n");
		fwrite($fdmif,"CoordSys Earth Projection 1, 104\n");
		fwrite($fdmif,"Columns 5\n");
		fwrite($fdmif,"  name Char(200)\n");
  	fwrite($fdmif,"  description Char(250)\n");
  	fwrite($fdmif,"  image Char(250)\n");
		fwrite($fdmif,"  Lon float\n");
		fwrite($fdmif,"  Lat float\n");
		fwrite($fdmif,"Data\n");			  

	  $query = "select * from `tiki_images` Where `galleryID`=?";
	  $result = $this->query($query, array($galleryId));
		$pres=100000; // Precision for lat lon convertion
		while ($res = $result->fetchRow()) {
			$name=substr($res["name"],0,20);
			$description=substr($res["description"],0,250);
			$lat=$res["lat"];
			$lon=$res["lon"];
			$link="<img src='show_image.php?id=".$res["imageId"]."' />";

			if (isset($lat) && isset($lon)) {
				if ($lat>=0) {
					$lat=(($lat*$pres) % (90*$pres))/$pres;
				} else {
					$lat=-((-$lat*$pres) % (90*$pres))/$pres;
				}
				if ($lon>=0) {
					$lon=(($lon*$pres) % (360*$pres))/$pres;
					if($lon>180) {$lon=$lon-360;}
				} else {
					$lon=-((-$lon*$pres) % (360*$pres))/$pres;
					if($lon<-180) {$lon=$lon+360;}
				}
				if ($prefs['mapzone']==360 && $lon<0) {
					$lon=360+$lon;
				}
				fwrite($fdmid,"\"".$name."\",");
				fwrite($fdmid,"\"".$description."\",");
				fwrite($fdmid,"\"".$link."\",");
	  		fwrite($fdmid,$lon.",".$lat."\n");
  		
				fwrite($fdmif,"Point ".$lon." ".$lat."\n");
				fwrite($fdmif,"   ".$symbol."\n");
			}
		}
		fclose($fdmid);
		fclose($fdmif);

		if (is_file($prefs['map_path']."/data/$tdo.dbf")) {
			unlink($prefs['map_path']."/data/$tdo.dbf");
		}
		if (is_file($prefs['map_path']."/data/$tdo.prj")) {
			unlink($prefs['map_path']."/data/$tdo.prj");
		}
		if (is_file($prefs['map_path']."/data/$tdo.shp")) {
			unlink($prefs['map_path']."/data/$tdo.shp");
		}
		if (is_file($prefs['map_path']."/data/$tdo.shx")) {
			unlink($prefs['map_path']."/data/$tdo.shx");
		}
					
		$ret=exec($prefs['ogr2ogr']." -f \"ESRI Shapefile\" ".$prefs['map_path']."data/$tdo.shp ".$prefs['map_path']."data/$tdo.mif");
		return (tra("Image Map Generated in:").$prefs['map_path']."data/".$tdo.".shp");
	}
}
global $mapslib;
$mapslib = new MapsLib;
