<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_scroll_info()
{
	return array(
		'name' => tra('Scroll'),
		'documentation' => 'PluginScroll',
		'description' => tra('Show animated text that scrolls up or down'),
		'prefs' => array('wikiplugin_scroll'),
		'body' => tra('text'),
		'iconname' => 'sort-down',
		'introduced' => 5,
		'tags' => array( 'basic' ),
		'params' => array(
			'width' => array(
				'required' => true,
				'name' => tra('Width'),
				'description' => tr('Width in pixels. Example: %0.', '<code>600px</code>'),
				'since' => '5.0',
				'accepted' => tra('Number of pixels followed by "px".'),
				'filter' => 'text',
				'default' => '',
			),
			'height' => array(
				'required' => true,
				'name' => tra('Height'),
				'description' => tr('Height in pixels. Example: %0.', '<code>450px</code>'),
				'since' => '5.0',
				'accepted' => tra('Number of pixels followed by "px".'),
				'filter' => 'text',
				'default' => '',
			),
			'speed' => array(
				'required' => false,
				'name' => tra('Speed'),
				'description' => tr('Scroll speed in number of seconds (default is %0)', '<code>8</code>'),
				'since' => '5.0',
				'filter' => 'digits',
				'default' => 8,
			),
		)
	);
}

function wikiplugin_scroll($data, $params)
{
	extract($params, EXTR_SKIP);
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
	if (substr($width, -1) == "x") {
		$width_w=substr($width, 0, -2);
		$height_h=substr($height, 0, -2); 
	} else if (substr($width, -1) == "%") {
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
