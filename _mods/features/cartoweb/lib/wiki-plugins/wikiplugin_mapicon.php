<?php

// Displays an inline map icon list
// Use:
// {CARTOWEB()}
//  (project=>) 
//  (extents=>)


function wikiplugin_mapicon_help() {
	return tra("Displays a mapicon map").":<br />~np~{MAPICON(project=>,mapId=>,symbol=>) /}~/np~";
}

function wikiplugin_mapicon($data, $params) {
	global $tikilib;
	global $feature_maps;

	extract ($params,EXTR_SKIP);

	$mapdata="";
	if (!isset($project)) {
		$project="Sigfreed";
	}
	if (!isset($imapId)) {
		$mapId=1;
	}

	$sizedata="";
	if (isset($size)) {
		$sizedata="size=".intval($size)."&";
	}
	$widthdata="";
	if (isset($width)) {
		$widthdata='width="'.intval($width).'"';
	}
	$heightdata="";
	if (isset($height)) {
		$heightdata='height="'.intval($height).'"';
	}	
	if(@$feature_maps != 'y') {
		$map=tra("Feature disabled");
	} else {
		require_once ("lib/map/layer.php");
		$filepath="../projects/".$project."/server_conf/World/symbols.txt";
		$icon_list=$layerlib->getmapicon($filepath);
		$data="<table width='100%'>";
		foreach($icon_list as $icon) {
		  $data=$data."<tr><td><a href='".$icon[1]."'><img src='".$icon[1]."' /></a></td>";
		  $data=$data."<td>".$icon[0]."</td>";
		  $data=$data."</tr>";
		}
		$data=$data."</table>";
		$map='mapicon<br/>'.$data;

	}
	return $map;
}

?>
