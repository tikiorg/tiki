#!/usr/bin/php
<?php

require_once("packer/class.JavaScriptPacker.php");

function compact_files($header, $files, $destination) {

    $t1 = microtime(true);

    $content='';
    foreach($files as $src) {
	$src="src/$src";
	$content.=file_get_contents($src);
    }

    $packer = new JavaScriptPacker($content, 'Normal', true, false);
    $packed = $header.$packer->pack();

    $t2 = microtime(true);
    $time = sprintf('%.4f', ($t2 - $t1) );
    echo 'script ', $destination, ' packed in ' , $out, ', in ', $time, ' s.', "\n";
    
    file_put_contents($destination, $packed);
}


compact_files("//MooTools, My Object Oriented Javascript Tools. Copyright (c) 2006 Valerio Proietti, <http://mad4milk.net>, MIT Style License.\n\r\n\r",
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
	      "mootools.js");


?>