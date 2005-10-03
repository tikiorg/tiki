<?php

// Displays the user Avatar
// Use:
// {MAP()}
//  (mapfile=>)         Avatar is a link to "some"
//  (extents=>)  Avatar is floated to left or right


function wikiplugin_map_help() {
	return tra("Displays a map").":<br />~np~{MAP(mapfile=>,extents=>,size=>) /}~/np~";
}

function wikiplugin_map($data, $params) {
	global $tikilib;
	global $feature_maps;

	extract ($params,EXTR_SKIP);

	$mapdata="";
	if (isset($mapfile)) {
		$mapdata='mapfile='.$mapfile.'&';
	}

	$extdata="";
	if (isset($extents)) {
		$dataext=explode("|",$extents);
		if (count($dataext)==4) {
			$minx=floatval($dataext[0]);
			$maxx=floatval($dataext[1]);
			$miny=floatval($dataext[2]);
			$maxy=floatval($dataext[3]);
			$extdata="minx=".$minx."&maxx=".$maxx."&miny=".$miny."&maxy=".$maxy."&zoom=1&";
		}
	}
	
	$sizedata="";
	if (isset($size)) {
		$sizedata="size=".intval($size)."&";
	}
	if(@$feature_maps != 'y') {
		$map=tra("Feature disabled");
	} else {
		$map='<a href="tiki-map.phtml?'.$mapdata.$extdata.$sizedata.'"><img src="tiki-map.phtml?'.$mapdata.$extdata.$sizedata.'maponly=yes"/></a>';
	}
	return $map;
}

?>