<?php

// Displays an inline map
// Use:
// {CARTOWEB()}
//  (project=>) 
//  (extents=>)


function wikiplugin_cartoweb_help() {
	return tra("Displays a cartoweb map").":<br />~np~{CARTOWEB(project=>,display=>,extents=>,size=>,width=>,height=>) /}~/np~";
}

function wikiplugin_cartoweb($data, $params) {
	global $tikilib;
	global $feature_maps;

	extract ($params,EXTR_SKIP);

	$mapdata="";
	if (isset($project)) {
		$mapdata='project='.$project.'&';
	}
	if (!isset($display)) {
		$display="maponly";
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
		$map='<object border="0" hspace="0" vspace="0" type="text/html" data="'.$project.'.php?display='.$display.'" '.$widthdata.' '.$heightdata.'><a href=""><img src="Sigfreed.php?'.$mapdata.$extdata.$sizedata.'display="/></a></object>';

	}
	return $map;
}

?>
