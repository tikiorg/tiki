<?php

chdir("../..");
require_once('tiki-setup.php');
require_once('lib/cpaint/cpaint2.inc.php');
include_once('lib/map/map_query.php');

	$cp = new cpaint();
  $cp->register('cp_map_query');
  $cp->start();
  $cp->return_data();
  
  function cp_map_query($mapfile,$corx,$cory,$minx,$maxx,$miny,$maxy,$xsize,$ysize,$layers,$labels) {
  	global $cp;
  	global $map_path;

  	if (strstr($mapfile, '..')) {
	    $msg = tra("You do not have permission to do that");
		}

		$map_path = preg_replace("/\/?$/","/",$map_path);

		//checking the mapfile
		if (!is_file($map_path.$mapfile) || preg_match("/(\/\.)/", $map_path.$mapfile)) {
	  	$msg = tra("invalid mapfile name");
	  }
  	
  	$map = ms_newMapObj($map_path.$mapfile);
  	for ($j=0;$j<$map->numlayers;$j++)
  	{
    	$my_layer=$map->getLayer($j);
    	if ($layers[$j]) {
	    	$my_layer->Set("status",MS_ON);
	    } else {
    		$my_layer->Set("status",MS_OFF);
    	}
    	if (!$labels[$j]) {
	     	$my_layer->Set("labelmaxscale",0);
	    }
    }

    $map->Set("width",$xsize);
		$map->Set("height",$ysize);
		
		$my_point = ms_newpointObj();
		$my_point->setXY(($map->width)/2,($map->height)/2);
		
		$my_extent = ms_newrectObj();
    $my_extent->setextent($minx,$miny,$maxx,$maxy);
    
    $map->zoompoint(1,$my_point,$map->width,$map->height,$my_extent);
    
  	$result=map_query($map,$corx,$cory);
  	$image = $map->drawquery();
		$image_url = $image->saveWebImage();

		$result=$image_url."\n".$result;
		
  	$cp->set_data($result);
  }

?>