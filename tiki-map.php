<?php
$_SERVER["SCRIPT_NAME"]=basename(__FILE__);
require_once('tiki-setup.php');
include_once ('lib/stats/statslib.php');
include_once('lib/map/map_query.php');

if (!function_exists('ms_newMapObj')) {
  $msg = tra("You must first setup MapServer");
  $access->display_error(basename(__FILE__), $msg);
}

if ($prefs['feature_maps'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_maps');
	$smarty->display('error.tpl');
	die;
}
if ($prefs['feature_ajax'] != 'y') {
 $smarty->assign('msg', tra('Feature Ajax Disabled. Please ask your site administrator to enable').': feature_ajax');
	$smarty->display('error.tpl');
	die;
}
if ($tiki_p_map_view != 'y') {
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}

//setting up xajax
require_once("lib/ajax/xajax.inc.php");
$xajax = new xajax("x_maps.php");
//$xajax->debugOn();
//$xajax->statusMessagesOn();
$xajax->registerFunction("map_redraw");
$smarty->assign('xajax_javascript', $xajax->getJavascript("lib/ajax"));

//handling the layer manager menu: visible by default if never used before
if (getCookie('layermenu', "menu") != 'o' && getCookie('layermenu', "menu") != 'c') {
	$smarty->assign('mnu_layermenu', 'display:block;');
}

$smarty->assign('tiki_p_map_edit',$tiki_p_map_edit);

if (isset($_REQUEST['mapfile'])) {
  // Validate to prevent displaying any file
  if (strstr($_REQUEST["mapfile"], '..')) {
    $msg = tra("You do not have permission to do that");
    $access->display_error(basename(__FILE__), $msg);
  }
  $mapfile = $_REQUEST['mapfile'];
} else {
  $mapfile = $prefs['default_map'];
}

$map_path = preg_replace("/\/?$/","/",$prefs['map_path']);

//checking the mapfile
if (!is_file($map_path.$mapfile) || preg_match("/(\/\.)/", $map_path.$mapfile)) {
  $msg = tra("invalid mapfile name").$map_path.$mapfile;
  $access->display_error(basename(__FILE__), $msg);
}
		      
// user defined error handling function to handle errors in loading mapfile
function userErrorHandler ($errno, $errmsg, $filename, $linenum, $vars)
{
  global $smarty;
  global $style_base;
  global $map_path;
  global $mapfile;
  global $tiki_p_map_edit;
  $msg=tra("An error as occured with the mapfile: ").$mapfile."<br /><br />".$errmsg."<br />";
  
  $pos=strpos($errmsg,":(");
  if ($errmsg[$pos+2]=='l')
  {
    $line=substr($errmsg,$pos+6);
  } else {
    $line=substr($errmsg,$pos+2);
  }
  $pos=strpos($line,")");
  $line=substr($line,0,$pos);
  
  if ($errmsg[$pos+2]!='l') $line++;
  
  $fcontents = file($map_path.$mapfile);

  $msg .="<pre>";
  
  if ($line>=4) {
    $msg.="   ".$fcontents[$line-4];
    $msg.="   ".$fcontents[$line-3];
    $msg.="   ".$fcontents[$line-2];
  }
  $msg.="-->".$fcontents[$line-1];
  if ($line+3<count($fcontents)) {
    $msg.="   ".$fcontents[$line+0];
    $msg.="   ".$fcontents[$line+1];
    $msg.="   ".$fcontents[$line+2];
    $msg.="   ".$fcontents[$line+3];
  }
  $msg.="<pre/><br />";
  
  if ($tiki_p_map_edit == 'y')
  {
     $msg.='<a class="link" href="tiki-map_edit.php?mapfile='.$mapfile.'&mode=editing">';
     $msg.='<img src="pics/icons/wrench.png" border="0" alt="'.tra("edit").'" title="'.tra("edit").'" width="16" height="16" />';
     $msg.='</a>';
  }
  
  $access->display_error(basename(__FILE__), $msg);
}

$old_error_handler = set_error_handler("userErrorHandler");

$map = ms_newMapObj($map_path.$mapfile);

restore_error_handler();

$page=$map->name." Map";
$smarty->assign("page",$page);

if($tikilib->page_exists($page))
{
 	$pagelink='<a href="tiki-index.php?page='.$page.'">'.$page.'</a>';
} else
{
  $pagelink=$page.'<a href="tiki-editpage.php?page='.$page.'">?</a>';
}
$smarty->assign("pagelink",$pagelink);
 
$layerbol=FALSE;
if (isset($_REQUEST['oldsize'])) {
	$layerbol=TRUE;
}

if(isset($_REQUEST['size'])&&is_numeric($_REQUEST['size'])&&($_REQUEST['size']>0)&&($_REQUEST['size']<2000))
{
    $xsize=floor($_REQUEST['size']);
    $ysize=floor(($_REQUEST['size']*$map->height/$map->width));
    $size = $_REQUEST['size'];
} else {
    $xsize = $map->width;
    $ysize = $map->height;
    $size = $map->width;
}
$map->Set("width",$xsize);
$map->Set("height",$ysize);
$map->scalebar->Set("width",$xsize);

//Mapview. Get first view, to check if view is !/empty

$map_view=$map->getMetaData("VIEW1");
$smarty->assign('map_view', $map_view);

$number_of_views=30;
$counter=0;
$view_name[]="";
$view_values[]=0;


for($i=1;$i<$number_of_views;$i++)
{
	$viewdata=$map->getMetaData("VIEW".$i);
  if(!empty($viewdata)){
	
		$v_data=explode(',',$viewdata,5);
		$view_name[$i]=$v_data[0];
		$v_minx=$v_data[1];
		$v_miny=$v_data[2];
		$v_maxx=$v_data[3];
		$v_maxy=$v_data[4];
	
     $view_values[$i]=array($v_minx,$v_miny,$v_maxx,$v_maxy);
     $counter++;
  }
}
$smarty->assign('view_name',$view_name);
$smarty->assign('view_values',$view_values);

if (isset($_REQUEST['minx']) && isset($_REQUEST['miny'])&& isset($_REQUEST['maxx']) && isset($_REQUEST['maxy']))
{	 			 

	if(isset($_REQUEST['Go']))
	{
         if(empty($map_view)||!(in_array($_REQUEST['view'],$view_name))||empty($_REQUEST['view'])){
                      $my_extent = ms_newrectObj();
                      $my_extent->setextent($map->extent->minx,$map->extent->miny,$map->extent->maxx,$map->extent->maxy);
	       } else {
	         //set view extents
                 for($view_count=1;$view_count<$counter+1;$view_count++){
                    if($_REQUEST['view']==$view_name[$view_count]){
                            $j=$view_count;
                            
			    									$view_minx=$view_values[$j][0];
                            $view_miny=$view_values[$j][1];
                            $view_maxx=$view_values[$j][2];
                            $view_maxy=$view_values[$j][3];
                            $my_extent=ms_newrectObj();
                            $my_extent->setextent($view_minx,$view_miny,$view_maxx,$view_maxy);
                             $view=$_REQUEST['view'];
                      }
                   }//end of for loop
          }//end of if else loop
    } else {
		$my_extent = ms_newrectObj();
		$my_extent->setextent($_REQUEST['minx'],$_REQUEST['miny'],$_REQUEST['maxx'],$_REQUEST['maxy']);	 
	}
} else {
	if(isset($_REQUEST['view'])&& in_array($_REQUEST['view'],$view_name)){
		//set view extents
		for($view_count=1;$view_count<$counter+1;$view_count++){
		   if($_REQUEST['view']==$view_name[$view_count]){
			// $view=$_REQUEST['view'];
			$j=$view_count;
			$view_minx=$view_values[$j][0];
			$view_miny=$view_values[$j][1];
			$view_maxx=$view_values[$j][2];
			$view_maxy=$view_values[$j][3];
			$my_extent=ms_newrectObj();
			$my_extent->setextent($view_minx,$view_miny,$view_maxx,$view_maxy);
			$view=$_REQUEST['view'];
		  }
		}//end of for loop
	} else {
    $my_extent = ms_newrectObj();
    $my_extent->setextent($map->extent->minx,$map->extent->miny,$map->extent->maxx,$map->extent->maxy);
	}
}

$query=FALSE;

if (isset($_REQUEST['zoom']))
{
  $zoom=$_REQUEST['zoom'];
  $my_point = ms_newpointObj();
  
  if (isset($_REQUEST['x']) && isset($_REQUEST['y']) && $zoom!=0)
  { 
  	if (isset($_REQUEST['oldsize'])) {
		 	$my_point->setXY(floor($_REQUEST['x']*$size/$_REQUEST['oldsize']),floor($_REQUEST['y']*$size/$_REQUEST['oldsize']));
		} else {
		 	$my_point->setXY(floor($_REQUEST['x']),floor($_REQUEST['y']));	
		}
  } else{
		$my_point->setXY(($map->width)/2,($map->height)/2);
  }

  if($zoom==0){  // It is a query 
    
  	$map->zoompoint(1,$my_point,$map->width,$map->height,$my_extent);
    
   	if (isset($_REQUEST['x']) && isset($_REQUEST['y']))
   	{
      //get the real world coordinates
      $x=$_REQUEST['x'];
      $y=$_REQUEST['y'];
      $width=$map->width-1;
      $height=$map->height-1;
      $minx=$map->extent->minx;
      $maxx=$map->extent->maxx;
      $miny=$map->extent->miny;
      $maxy=$map->extent->maxy;
      $dx=$maxx-$minx;
      $dy=$maxy-$miny;
      $factorx=$dx/$width;
      $factory=$dy/$height;
      $corx=$x*$factorx+$minx;
      $cory=$maxy-$y*$factory;
      $query=TRUE;
   	}
  }	else {
    $map->zoompoint($zoom,$my_point,$map->width,$map->height,$my_extent);
  }
} else {
  $zoom=1;
}
$count_group=0;

//define which layer is visible, has labels and can be queried
for ($j=0;$j<$map->numlayers;$j++)
{
    
  $my_layer=$map->getLayer($j);
    
  if (isset($_REQUEST[str_replace(" ","_",$my_layer->name)]))
  {
    if ($_REQUEST[str_replace(" ","_",$my_layer->name)]==1)
    {
      $my_layer->Set("status",MS_ON);
    }
  }else
  {
    // test to see if we are not on the first drawing
    if ($layerbol)
    {
    	$my_layer->Set("status",MS_OFF);
    }
  }

  //Display labels
  if ($my_layer->labelitem!="")
  {
  	$layer_label[$j]="On";
  	if (isset($_REQUEST[str_replace(" ","_",$my_layer->name)."_label"]))
		{
  	   $my_layers_label_checked[$j]="checked";
  	} else
  	{
           $my_layers_label_checked[$j]="";
  	   $my_layer->Set("labelmaxscale",0);
  	}
  } else
  {
    $layer_label[$j]="Off";
  }
  
  if ($my_layer->tolerance!=-1)
  {
    $layer_query[$j]="On";
  } else {
    $layer_query[$j]="Off";
  }
} 

// We have a query so let's get the results.
if ($query) {
	$map_querymsg= map_query($map,$corx,$cory);
	$smarty->assign('map_querymsg',$map_querymsg);
}


if (@$_REQUEST['maponly']=='frame') {
  $scalebar=$map->scalebar;
  $scalebar->set("status",MS_EMBED);
  $scalebar->set("transparent",TRUE);
}

$image = $map->drawquery();
$image_url = $image->saveWebImage();

//the user wants an image only
if (isset($_REQUEST['maponly']) && $_REQUEST['maponly']=='yes')
{
  header("Content-type: image/png");
  $image_file=str_replace($image->imageurl,$image->imagepath,$image_url);
  readfile($image_file);
  $image->free();
  die;
}

if (@$_REQUEST['maponly']!='frame') {
	$image_ref = $map->drawReferenceMap();
	$image_ref_url = $image_ref->saveWebImage();
	$image_leg = $map->drawLegend();
	$image_leg_url = $image_leg->saveWebImage();
	$image_scale = $map->drawScaleBar();
	$image_scale_url = $image_scale->saveWebImage();
	$image_leg->free();
	$image_ref->free();
	$image_scale->free();
}
$image->free();

$minx=$map->extent->minx;
$miny=$map->extent->miny;
$maxx=$map->extent->maxx;
$maxy=$map->extent->maxy;

$smarty->assign('mapfile',$mapfile);

$smarty->assign('image_url',$image_url);
if (@$_REQUEST['maponly']!='frame') {
	$smarty->assign('image_ref_url',$image_ref_url);
	$smarty->assign('image_leg_url',$image_leg_url);
	$smarty->assign('image_scale_url',$image_scale_url);
} 
$smarty->assign('minx',$minx);
$smarty->assign('miny',$miny);
$smarty->assign('maxx',$maxx);
$smarty->assign('maxy',$maxy);

$smarty->assign('size',$size);
	
$smarty->assign('xsize',$xsize);
$smarty->assign('ysize',$ysize);
$smarty->assign('possiblesizes',array(200,300,400,600,800,1000));
$smarty->assign('displaysizes',array(200,300,400,600,800,1000));

$smarty->assign_by_ref('layer_label',$layer_label);
$smarty->assign_by_ref('my_layers_label_checked',$my_layers_label_checked);
$smarty->assign_by_ref('layer_query',$layer_query);

// is the user registered?

if(isset($_REQUEST['view_user'])) {
  $userwatch = $_REQUEST['view_user'];
} else {
    if($user) {
       	$userwatch = $user;
    } else {
	$userwatch ="";
  }
}  

  for ($j=0;$j<$map->numlayers;$j++)
  {
    $my_layers[$j]=$map->getLayer($j);

	//getting layer by group
	if(!empty($my_layers[$j]->group)){
	$my_layer_group[$j]=$my_layers[$j]->group;
	$smarty->assign('unique_layer_group', array_unique($my_layer_group));
	}

    //Metadata handling
    $link=$my_layers[$j]->getMetadata("WIKI");
    if ($link!="")
    {
      if($tikilib->page_exists($link))
      {
        $layer_wiki[$j]='<a href="tiki-index.php?page='.$link.'"><small>'.$my_layers[$j]->name.'</small></a>';
      } else
      {
        $layer_wiki[$j]='<small>'.$my_layers[$j]->name.'<a href="tiki-editpage.php?page='.$link.'">?</a></small>';
      }
    } else
    {
      $layer_wiki[$j]='<small>'.$my_layers[$j]->name.'</small>';
    }
    if ($userwatch!="")
    {
      $layer_download[$j]=$my_layers[$j]->getMetadata("DOWNLOAD");
    } else
    {
    	$layer_download[$j]="";
    }
  
    //Layer display Handling
    if ($my_layers[$j]->status==MS_ON)
    {
      $my_layers_checked[$j]="checked";
      $bolcheck=1;
    } else
    {
      $my_layers_checked[$j]="";
      $bolcheck=0;
    }
  }

$smarty->assign_by_ref('my_layers',$my_layers);
$smarty->assign_by_ref('my_layers_checked',$my_layers_checked);
$smarty->assign_by_ref('layer_wiki',$layer_wiki);
$smarty->assign_by_ref('layer_download',$layer_download);
$smarty->assign('userwatch',$userwatch);

  for ($i=-4;$i<=4;$i++)
  {
     
  	  switch($i)
  	  {
  	  case "-4":
  	  case "-3":
  	  case "-2":
  	    $zoom_values[]=$i;
  	    $zoom_display[]='zoom out '.$i.'x';
		    break;
		  case "0":
		    	$zoom_values[]=$i;
		    	$zoom_display[]='query';
       break;
		  case "1":
		    $zoom_values[]=$i;
		    $zoom_display[]='pan';
       break;
  	  case "2":
  	  case "3":
  	  case "4":
  	    $zoom_values[]=$i;
  	    $zoom_display[]='zoom in '.$i.'x';
       break;
		  }
		}
$smarty->assign('zoom',$zoom);		
$smarty->assign_by_ref('zoom_values',$zoom_values);
$smarty->assign_by_ref('zoom_display',$zoom_display);

//add a hit
$statslib->stats_hit($mapfile,"map");

//get some stats info
$data=nl2br(file_get_contents($map_path.$mapfile));

$ip="";
$lastUser=tra("unknown");
$lastModif="";
if (strpos($data,"##TIKIMAPS HEADER: END##")!=FALSE) {
	$searchdata=substr($data,0,strpos($data,"##TIKIMAPS HEADER: END##"));
	if (strpos($searchdata,"#IP: ")!=FALSE) {
		$IP=substr($searchdata,strpos($searchdata,"#IP: ")+4);
		$ip=substr($IP,0,strpos($IP,"<br"));
	}
	if (strpos($searchdata,"#Modified by: ")!=FALSE) {
		$IP=substr($searchdata,strpos($searchdata,"#Modified by: ")+13);
		$lastUser=substr($IP,0,strpos($IP,"<br"));
	}
	if (strpos($searchdata,"#GMT Date: ")!=FALSE) {
		$IP=substr($searchdata,strpos($searchdata,"#GMT Date: ")+10);
		$IP=substr($IP,0,strpos($IP,"<br"));
		$lastModif=gmmktime(substr($IP,10,2),substr($IP,12,2),substr($IP,14,2),substr($IP,5,2),substr($IP,7,2),substr($IP,1,4));
	}
}
$smarty->assign('ip',$ip);
$smarty->assign('lastUser',$lastUser);
$smarty->assign('lastModif',$lastModif);

$mapstats = $statslib->object_hits($mapfile,"map");
$mapstats7days = $statslib->object_hits($mapfile,"map",7);

$smarty->assign('mapstats', $mapstats);
$smarty->assign('mapstats7days', $mapstats7days);

//display the template
if (@$_REQUEST['maponly']!='frame') {
	$section = 'maps';
	include_once ('tiki-section_options.php');
	$smarty->assign('mid','map/tiki-map.tpl');
	$smarty->display("tiki.tpl");
} else {
	$smarty->assign("maponly","frame");
	$smarty->display('map/tiki-map_frame.tpl');
}
?>