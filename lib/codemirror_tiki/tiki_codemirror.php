<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function codemirrorModes($minify = true)
{
	global $tikidomainslash;
	$js = '';
	$css = '';

	$target = 'temp/public/'.$tikidomainslash;
	$jsfile = $target . 'codemirror.js';
	$cssfile = $target . 'codemirror.css';

	if (!file_exists($jsfile) || !file_exists($cssfile)) {

		//tiki first, where are our priorities!
		$js .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.js");
		$css .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.css");

		foreach(glob('lib/codemirror/mode/*', GLOB_ONLYDIR) as $dir) {
			foreach(glob($dir.'/*.js') as $jsFile) {
				$js .= "try{" . @file_get_contents($jsFile) . "}catch(e){}";
			}
			foreach(glob($dir.'/*.css') as $cssFile) {
				$css .= @file_get_contents($cssFile);
			}
		}

		file_put_contents($jsfile, $js);
		chmod($jsfile, 0644);

		file_put_contents($cssfile, $css);
		chmod($cssfile, 0644);
	}

	TikiLib::lib("header")
		->add_jsfile_dependancy($jsfile)
		->add_cssfile($cssfile);
}