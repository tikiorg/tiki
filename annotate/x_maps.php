<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

//setting up xajax
include_once('lib/map/map_query.php');
require_once("lib/ajax/xajax/xajax_core/xajaxAIO.inc.php");
$xajax = new xajax("x_maps.php");
$xajax->setFlag('debug',true);
$xajax->configure('statusMessages',true);

if($prefs['feature_maps'] != 'y' || $prefs['ajax_xajax'] != 'y') {
  $objResponse = new xajaxResponse();
  $objResponse->alert(tra("Feature disabled"));
  return $objResponse;
}

if($tiki_p_map_view != 'y') {
  $objResponse = new xajaxResponse();
  $objResponse->alert(tra("You do not have permissions to view the maps"));
  return $objResponse;
}
  
  function map_redraw($mapfile,$corx,$cory,$minx,$maxx,$miny,$maxy,$xsize,$ysize,$layers,$labels,$zoom,$changeleg=false,$corx2=0,$cory2=0) {
  	global $prefs;
  	$objResponse = new xajaxResponse();
  	
  	if (strstr($mapfile, '..')) {
	    $msg = tra("You do not have permission to do that");
	    $objResponse->alert($msg);
	    return $objResponse;
		}

		$prefs['map_path'] = preg_replace("/\/?$/","/",$prefs['map_path']);

		//checking the mapfile
		if (!is_file($prefs['map_path'].$mapfile) || preg_match("/(\/\.)/", $prefs['map_path'].$mapfile)) {
	  	$msg = tra("invalid mapfile name");
	  	$objResponse->alert($msg);
	  	return $objResponse;
	  }

  	$map = ms_newMapObj($prefs['map_path'].$mapfile);
  	for ($j=0;$j<$map->numlayers;$j++)
  	{
    	$my_layer=$map->getLayer($j);
    	if ($layers[$j]=="true") {
	    	$my_layer->set("status",MS_ON);
	    } else {
    		$my_layer->set("status",MS_OFF);
    	}
    	if ($labels[$j]=="false") {
	     	$my_layer->set("labelmaxscale",0);
	    }
    }

    $map->Set("width",$xsize);
		$map->Set("height",$ysize);
		
		$my_point = ms_newpointObj();
		
		if ($zoom==3) {
			$my_point->setXY(($map->width)/2,($map->height)/2);
		} else {
			$my_point->setXY(floor($corx),floor($cory));
		}
		
		$my_extent = ms_newrectObj();
    $my_extent->setextent($minx,$miny,$maxx,$maxy);
        
    $result="";
    if ($zoom==3) {
			$map->zoompoint(1,$my_point,$map->width,$map->height,$my_extent); 
	  	$result=map_query($map,$corx,$cory); 		
  	} elseif ($zoom==5) {
  	  $my_rect= ms_newrectObj();
  	  $my_rect->setextent($corx,$cory,$corx2,$cory2);
  		$map->zoomrectangle($my_rect,$map->width,$map->height,$my_extent);
	  	$objResponse->assign("minx", "value",$map->extent->minx);
	  	$objResponse->assign("miny", "value",$map->extent->miny);
	  	$objResponse->assign("maxx", "value",$map->extent->maxx);
	  	$objResponse->assign("maxy", "value",$map->extent->maxy);
	  	$objResponse->assign("map", "style.cursor","default");
	  	$objResponse->assign("zoomselect", "style.visibility","hidden");
	  	$objResponse->script("xMoveTo(xGetElementById('map'),0,0);");
	  	$objResponse->script("minx=".$map->extent->minx.";");
	  	$objResponse->script("miny=".$map->extent->miny.";");
	  	$objResponse->script("maxx=".$map->extent->maxx.";");
	  	$objResponse->script("maxy=".$map->extent->maxy.";");
  	} else {
	  	$map->zoompoint(1,$my_point,$map->width,$map->height,$my_extent);
	  	$objResponse->assign("minx", "value",$map->extent->minx);
	  	$objResponse->assign("miny", "value",$map->extent->miny);
	  	$objResponse->assign("maxx", "value",$map->extent->maxx);
	  	$objResponse->assign("maxy", "value",$map->extent->maxy);
	  	$objResponse->assign("map", "style.cursor","move");
	  	$objResponse->script("xMoveTo(xGetElementById('map'),0,0);");
	  	$objResponse->script("minx=".$map->extent->minx.";");
	  	$objResponse->script("miny=".$map->extent->miny.";");
	  	$objResponse->script("maxx=".$map->extent->maxx.";");
	  	$objResponse->script("maxy=".$map->extent->maxy.";");
	  }
  	$image = $map->drawquery();
		$image_url = $image->saveWebImage();
		if ($zoom!=3) {
			$image_ref = $map->drawReferenceMap();
			$image_ref_url = $image_ref->saveWebImage();
			$image_ref->free();
		}
		
		if ($changeleg) {
			$image_leg = $map->drawLegend();
			$image_leg_url = $image_leg->saveWebImage();
			$image_leg->free();
		}

		if ($zoom==2 || $zoom==5) {
			$image_scale = $map->drawScaleBar();
			$image_scale_url = $image_scale->saveWebImage();
			$image_scale->free();
		}
		
		$image->free();		
		
		
  	$objResponse->assign("innerBoxContent","innerHTML", $result);
  	$objResponse->assign("resultBox","innerHTML", $result);
  	$objResponse->assign("map", "src", $image_url);
  	if ($zoom!=3) {
  		$objResponse->assign("ref", "src", $image_ref_url);
  	}
  	if ($zoom==2 || $zoom==5) {
  		$objResponse->assign("scale", "src", $image_scale_url);
  	}
  	if ($changeleg) {
  		$objResponse->assign("leg", "src", $image_leg_url);
  	}
  	return $objResponse;
  }
  
  $xajax->register(XAJAX_FUNCTION,"map_redraw");
  $xajax->processRequest();
