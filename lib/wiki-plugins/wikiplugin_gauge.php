<?php

// Displays a graphical GAUGE
// Usage:
// {GAUGE(params)}description{GAUGE}
// Description is optional and will be displayed below the gauge if present
// Parameters:
//   color	bar color
//   bgcolor	background color
//   max	maximum possible value (default to for percentages 100)
//   value	current value (REQUIRED)
//   size	Bar size 
//   perc	If true then a percentage is displayed
//   height	Bar height
// EXAMPLE:
//
// {GAUGE(perc=>true,value=>35,bgcolor=>#EEEEEE,height=>20)}happy users over total{GAUGE}

function wikiplugin_gauge($data,$params) {
  
  extract($params);
  
  
  if(!isset($max)) {$max=100;}
  if(!isset($value)) {
    return ("<b>missing value parameter for plugin</b><br/>");  	
  }
  
  if(!isset($size)) {$size=150;}
  if(!isset($bgcolor)) {$bgcolor='#0000FF';}
  if(!isset($color)) {$color='#FF0000';}
  if(!isset($perc)) {$perc=false;}
  if($perc) {
    $perc = number_format($value/$max*100,2);
    $perc = '&nbsp;&nbsp;'.$perc.'%';
  } else {
    $perc='';
  }
  $h_size =  floor($value/$max*$size);
    
  if(!isset($height)) {$height=14;}
  $html="<table border='0' cellpadding='0' cellspacing='0'><tr><td><table border='0' height='$height' cellpadding='0' cellspacing='0' width='$size' style='background-color:$bgcolor;'><tr><td style='background-color:$color;' width='$h_size'>&nbsp;</td><td>&nbsp;</td></tr></table></td><td>$perc</td></tr>";
  if(!empty($data)) {
    $html.="<tr><td colspan='2'><small>$data</small></td></tr>";
  } 
  $html.="</table>"; 
  return $html;

}


?>
