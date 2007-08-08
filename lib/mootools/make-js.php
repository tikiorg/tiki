#!/usr/bin/php
<?php

require_once("packer/class.JavaScriptPacker.php");

function compact_files($header, $srcdir, $files, $destination) {
	
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
	echo 'script ', $destination, ' packed in ' , $out, ', in ', $time, ' s.', "\n";
	
	file_put_contents($destination, $packed);
}


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
			  "mootools.js");


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
			  "extensions/windoo/windoo.js");

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>