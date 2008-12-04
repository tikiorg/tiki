#!/usr/bin/php
<?php

if( isset( $_SERVER['REQUEST_METHOD'] ) ) die;
require_once("packer/class.JavaScriptPacker.php");

function compact_files($header, $srcdir, $files, $destination_packed, $destination) {
	
	$t1 = microtime(true);
	
	$content='';
	foreach($files as $src) {
		$src="$srcdir/$src";
		$content.=file_get_contents($src);
	}

	$packer = new JavaScriptPacker($content, 'Normal', true, false);
	$packed = $header.$packer->pack();

	$t2 = microtime(true);
	$time = sprintf('%.4f', ($t2 - $t1) );
	echo 'script ', $destination_packed, ' packed in ' , $out, ', in ', $time, ' s.', "\n";
	
	file_put_contents($destination_packed, $packed);
	file_put_contents($destination, $header.$content);
}

function compact_module($modulename) {
	switch($modulename) {
	case 'mootools':
		compact_files("//MooTools, My Object Oriented Javascript Tools. Copyright (c) 2006 Valerio Proietti, <http://mad4milk.net>, MIT Style License.\n\r\n\r",
					  "src",
					  array(
							"Core/Core.js",
							"Class/Class.js",
							"Class/Class.Extras.js",
							"Native/Array.js",
							"Native/String.js",
							"Native/Function.js",
							"Native/Number.js",
							"Native/Element.js",
							"Element/Element.Event.js",
							"Element/Element.Filters.js",
							"Element/Element.Selectors.js",
							"Element/Element.Form.js",
							"Element/Element.Dimensions.js",
							"Window/Window.DomReady.js",
							"Window/Window.Size.js",
							"Effects/Fx.Base.js",
							"Effects/Fx.CSS.js",
							"Effects/Fx.Style.js",
							"Effects/Fx.Styles.js",
							"Effects/Fx.Elements.js",
							"Effects/Fx.Scroll.js",
							"Effects/Fx.Slide.js",
							"Effects/Fx.Transitions.js",
							"Drag/Drag.Base.js",
							"Drag/Drag.Move.js",
							"Remote/XHR.js",
							"Remote/Ajax.js",
							"Remote/Cookie.js",
							"Remote/Json.js",
							"Remote/Json.Remote.js",
							"Remote/Assets.js",
							"Plugins/Hash.js",
							"Plugins/Hash.Cookie.js",
							"Plugins/Color.js",
							"Plugins/Scroller.js",
							"Plugins/Slider.js",
							"Plugins/SmoothScroll.js",
							"Plugins/Sortables.js",
							"Plugins/Tips.js",
							"Plugins/Group.js",
							"Plugins/Accordion.js"),
					  "mootools_packed.js",
					  "mootools.js");
		break;
		
	case 'windoo':
		compact_files("//Windoo: Mootools window class <http://code.google.com/p/windoo>. Copyright (c) 2007 Yevgen Gorshkov, MIT Style License.\n\r\n\r",
					  "extensions/windoo/src",
					  array(
							"Effects/Fx.Overlay.js",
							"Windoo/Windoo.Core.js",
							"Windoo/Windoo.Manager.js",
							"Windoo/Windoo.Drag.js",
							"Windoo/Windoo.Dialog.js",
							"Windoo/Windoo.Panel.js",
							"Windoo/Windoo.Ajax.js",
							"Windoo/Windoo.Themes.js",
							"Drag/Drag.Multi.js",
							"Drag/Drag.Resize.js",
							"Drag/Drag.ResizeImage.js"),
					  "extensions/windoo/windoo_packed.js",
					  "extensions/windoo/windoo.js");
		break;
		
	case 'moorainbow':		
		compact_files("//mooRainbow: Mootools color picker, from: http://w00fz.altervista.org/mooRainbow/\n\r\n\r",
					  "extensions/mooRainbow",
					  array("mooRainbow.js"),
					  "extensions/mooRainbow/mooRainbow_packed.js",
					  "extensions/mooRainbow/mooRainbow.js");
		break;
	}
}

function show_help() {
	global $modules;
	echo "Usage: \n";
	echo "php make-js.php [-h|-a|MODULES..]\n";
	echo "  -a: make all\n";
	echo "  -h: show this help\n";
	echo "  MODULES: list of modules to compact\n";
	echo "available modules are: ".implode(', ', $modules)."\n";
}

$modules=array('mootools', 'windoo', 'moorainbow');

unset($argv[0]);
if (count($argv) == 0) {
	show_help();
	exit(0);
}
foreach($argv as $arg) {
	switch($arg) {
	case '-h':
	case '--help':
		show_help();
		break;
		
	case '-a':
		foreach($modules as $module) compact_module($module);
		break;
		
	default:
		if (in_array($arg, $modules)) compact_module($arg);
		else echo "Warning: skiping unknow option/module: $arg\n";
		break;
	}
}

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>
