<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/map/usermap.php,v 1.5 2004-10-08 10:00:00 damosoft Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function makemap($name,$datastruct,&$data,$cols=0,$symbol="Symbol (34,16711680,9)") {

	global $tikilib;
	global $map_path;
	global $tikidomain;
	global $ogr2ogr;
	global $smarty;
	
	$tdo = $name;

	$miffile=$map_path."data/$tdo.mif";
	$midfile=$map_path."data/$tdo.mid";
	
	$pres=1000000;	//precision for clamping lat/lon data
	
	if (!(isset($ogr2ogr) && is_executable($ogr2ogr))) {
	  $smarty->assign('msg',tra("No valid ogr2ogr executable"));
	  $smarty->display("error.tpl");
	  die;
	}

	if (count($data[0])<=2) {
	  $smarty->assign('msg',tra("not enough fields in data"));
	  $smarty->display("error.tpl");
	  die;	
	}

	$fdmif=@fopen($miffile,"w");
	if (!$fdmif) {
	  $smarty->assign('msg',tra("Could not create \$tdo.mif in data directory"));
	  $smarty->display("error.tpl");
	  die;
	}	
	$fdmid=@fopen($midfile,"w");
	if (!$fdmid) {
	  $smarty->assign('msg',tra("Could not create \$tdo.mid in data directory"));
	  $smarty->display("error.tpl");
	  die;
	}
	fwrite($fdmif,"Version 300\n");
	fwrite($fdmif,"Charset \"WindowsLatin1\"\n");
	fwrite($fdmif,"Delimiter \",\"\n");
	fwrite($fdmif,"CoordSys Earth Projection 1, 104\n");
	fwrite($fdmif,"Columns ".strval($cols+2)."\n");
	fwrite($fdmif,$datastruct);
	fwrite($fdmif,"  Lat float\n");
	fwrite($fdmif,"  Lon float\n");
	fwrite($fdmif,"Data\n");

	$count=count($data);
	for ($i=0;$i<$count;$i++) {
		if (isset($data[$i][0]) && isset($data[$i][1]) && $data[$i][0] && $data[$i][1]) {
			if ($data[$i][0]>=0) {
				$data[$i][0]=(($data[$i][0]*$pres) % (90*$pres))/$pres;
			} else {
				$data[$i][0]=-((-$data[$i][0]*$pres) % (90*$pres))/$pres;
			}
			if ($data[$i][1]>=0) {
				$data[$i][1]=(($data[$i][1]*$pres) % (180*$pres))/$pres;
			} else {
				$data[$i][1]=-((-$data[$i][1]*$pres) % (180*$pres))/$pres;
			}
			
			$count2=count($data[$i]);
			$j=2;
			while($j<$count2) {
				fwrite($fdmid,"\"".$data[$i][$j]."\",");
				$j++;
			}
			fwrite($fdmid,$data[$i][0].",".$data[$i][1]."\n");
			fwrite($fdmif,"Point ".$data[$i][1]." ".$data[$i][0]."\n");
			fwrite($fdmif,"   ".$symbol."\n");
		}
	}

	fclose($fdmid);
	fclose($fdmif);

	if (is_file($map_path."/data/$tdo.dbf")) {
		unlink($map_path."/data/$tdo.dbf");
	}
	if (is_file($map_path."/data/$tdo.prj")) {
		unlink($map_path."/data/$tdo.prj");
	}
	if (is_file($map_path."/data/$tdo.shp")) {
		unlink($map_path."/data/$tdo.shp");
	}
	if (is_file($map_path."/data/$tdo.shx")) {
		unlink($map_path."/data/$tdo.shx");
	}
	
	$ret=exec($ogr2ogr." -f \"ESRI Shapefile\" ".$map_path."data/$tdo.shp ".$map_path."data/$tdo.mif");

}	
?>
