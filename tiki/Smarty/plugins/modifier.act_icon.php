<?php


function smarty_modifier_act_icon($type,$isInter='n')
{
  $md = $isInter == 'y' ? "_blue" : "";
  switch($type) {
  	case 'activity':
  		$ic = "mini".$md."_rectangle.gif";
  		break;
  	case 'switch':
  		$ic = "mini".$md."_diamond.gif";
  		break;
  	case 'start':
	  	$ic="mini".$md."_circle.gif";
  		break;
  	case 'end':
  	    $ic='mini'.$md.'_dbl_circle.gif';
  		break;
  	case 'split':
  		$ic='mini'.$md.'_triangle.gif';
  		break;
  	case 'join':
  		$ic='mini'.$md.'_inv_triangle.gif';
  		break;
  	case 'standalone':
  		$ic='mini'.$md.'_hexagon.gif';
  		break;
  }
  $img="<img src='lib/Galaxia/img/icons/$ic' alt='$type' title='$type' />";
  return $img;	
}

?>
