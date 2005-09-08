<?php
/* Tiki-Wiki plugin scroll 
 *
 * This is an example plugin to let you know how to create
 * a plugin. Plugins are called using the syntax
 * {NAME(params)}content{NAME}
 * Name must be in uppercase!
 * params is in the form: name=>value,name2=>value2 (don't use quotes!)
 * If the plugin doesn't use params use {NAME()}content{NAME}
 *
 * The function will receive the plugin content in $data and the params
 * in the asociative array $params (using extract to pull the arguments
 * as in the example is a good practice)
 * The function returns some text that will replace the content in the
 * wiki page.
 */
function wikiplugin_svg_help() {
	return tra("Svg").":<br />~np~{SVG(width=> height=> src=> )}".tra("text")."{SVG}~/np~";
}
function wikiplugin_svg($data, $params) {
	extract ($params, EXTR_SKIP);
//minimum parameters
	if (!isset($width)) {
		return ("<b>missing  width parameter for plugin</b><br/>");
	}

	if (!isset($height)) {
		return ("<b>missing height parameter for plugin</b><br/>");
	}
	if (!isset($src)) {                return ("<b>missing src parameter for plugin</b><br/>");        }
        if (substr($src,-3) == "svg" || substr($src,-4) == "svgz") {
	} else {
	 return ("<b> ".substr($src,-3,-1)."src parameter should finish by '.svg' or by '.svgz'</b><br/>");
	}
   $margin=40;
	 if (substr($width,-1) == "x") {
	 $width_w=substr($width,0,-2);
	 $height_h=substr($height,0,-2); 
	 } else if (substr($width,-1) == "%") {
	 return ("<b>Warning the value of the height parameters must be set using px  (ex: 600px ) for plugin</b><br/>");
	 } else {
   return ("<b>Warning the value of the width parameters must be set using px  (ex: 600px ) for plugin</b><br/>");  
	}
   $margin =20;
   $i_width=$width_w - $margin;
	 $c_width=$width_w - 10;
	 $cx_width=$i_width."px";
	 $z_width_w=$width_w+10;
	 $z_width=$z_width_w."px";
	 $ret= "~np~<object data=\"".$src."\" type=\"image/svg+xml\" style=\"width: ".$width."; height:".$height."\">
	     <img src=\"".$src."\" alt=\"Alt text\" width=\"".$width_w."\" height=\"".$height_h."\" />
	       </object>
	 ~/np~";
	return $ret;
}

?>
