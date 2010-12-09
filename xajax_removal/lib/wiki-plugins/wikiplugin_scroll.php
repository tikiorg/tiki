<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
function wikiplugin_scroll_help() {
	return tra("Scroll").":<br />~np~{SCROLL(width=> height=> speed=>)}".tra("text")."{SCROLL}~/np~";
}
function wikiplugin_scroll_info() {
	return array(
		'name' => tra('Scroll'),
		'documentation' => tra('PluginScroll'),
		'description' => tra(''),
		'prefs' => array('wikiplugin_scroll'),
		'body' => tra('text'),
		'params' => array(
			'width' => array(
				'required' => true,
				'name' => tra('Width'),
				'description' => tra('Width in pixels. Example: 600px.'),
				'accepted' => tra('Number of pixels followed by "px". Example: 600px.'),
				'filter' => 'striptags',
				'default' => '',
			),
			'height' => array(
				'required' => true,
				'name' => tra('Height'),
				'description' => tra('Height in pixels. Example: 450px'),
				'accepted' => tra('Number of pixels followed by "px". Example: 450px.'),
				'filter' => 'striptags',
				'default' => '',
			),
			'speed' => array(
				'required' => false,
				'name' => tra('Speed'),
				'description' => tra('Scroll speed in number of seconds (default is 8 seconds)'),
				'filter' => 'int',
				'default' => 8,
			),
		)
	);
}

function wikiplugin_scroll($data, $params) {
	extract ($params, EXTR_SKIP);
//minimum parameters
	if (!isset($width)) {
		return ('<b>missing width parameter for plugin</b><br/>');
	}

	if (!isset($height)) {
		return ('<b>missing height parameter for plugin</b><br/>');
	}
	
//optional parameters
	if (!isset($speed)) {
		$speed=8;
	}


// margin requierd for ilayer scrolling on mozilla
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
	 $ret= "~np~<div style=\"background-color:#FFFFDD;width:$width\"><center><a href=\"javascript:movedown()\">Down</a>  <a href=\"javascript:moveup()\">Up</a> 
	 <a href=\"javascript:stopscroll()\">Stop</a>  <a href=\"javascript:movetop()\">Top</a></center>
	 </div>
	 
	 <SCRIPT language=\"JavaScript1.2\">
	 
	 //specify speed of scroll (greater=faster)
	 var speed=$speed
	 
	 iens6=document.all||document.getElementById
	 ns4=document.layers
	 
	 if (iens6){
	 document.write('<div id=\"container\" style=\"position:relative;width:$width;height:$height;overflow:hidden;border:2px ridge white\">')
	 document.write('<div id=\"content\" style=\"position:absolute;width:$width;left:0px;top:0px\">')
	 }
	 </script>
	 
	 <ilayer name=\"nscontainer\" width=width_w height=height_h clip=\"0,0,width_w,height_h\">
	 <layer name=\"nscontent\" width=width_w height=height_h visibility=hidden>
	 
	 <!--INSERT CONTENT HERE-->~/np~
					$data
					~np~
<!--END CONTENT-->
</layer>
</ilayer>

<script language=\"JavaScript1.2\">
if (iens6){
document.write('</div></div>')
var crossobj=document.getElementById? document.getElementById(\"content\") : document.all.content
var contentheight=crossobj.offsetHeight
}
else if (ns4){
var crossobj=document.nscontainer.document.nscontent
var contentheight=crossobj.clip.height
}

function movedown(){
if (window.moveupvar) clearTimeout(moveupvar)
if (iens6&&parseInt(crossobj.style.top)>=(contentheight*(-1)+100))
crossobj.style.top=parseInt(crossobj.style.top)-speed+\"px\"
else if (ns4&&crossobj.top>=(contentheight*(-1)+100))
crossobj.top-=speed
movedownvar=setTimeout(\"movedown()\",20)
}

function moveup(){
if (window.movedownvar) clearTimeout(movedownvar)
if (iens6&&parseInt(crossobj.style.top)<=0)
crossobj.style.top=parseInt(crossobj.style.top)+speed+\"px\"
else if (ns4&&crossobj.top<=0)
crossobj.top+=speed
moveupvar=setTimeout(\"moveup()\",20)
}

function stopscroll(){
if (window.moveupvar) clearTimeout(moveupvar)
if (window.movedownvar) clearTimeout(movedownvar)
}

function movetop(){
stopscroll()
if (iens6)
crossobj.style.top=0+\"px\"
else if (ns4)
crossobj.top=0
}

function getcontent_height(){
if (iens6)
contentheight=crossobj.offsetHeight
else if (ns4)
document.nscontainer.document.nscontent.visibility=\"show\"
}
window.onload=getcontent_height
</script>
~/np~";
	return $ret;
}
