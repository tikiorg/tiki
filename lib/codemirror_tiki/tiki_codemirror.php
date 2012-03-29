<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function codemirrorModes()
{
	$js = '';
	$css = '';

	if (TikiLib::lib("cache")->isCached("codemirror_js") == false || TikiLib::lib("cache")->isCached("codemirror_css") == false) {
		foreach(glob('lib/codemirror/mode/*', GLOB_ONLYDIR) as $dir) {
			foreach(glob($dir.'/*.js') as $jsFile) {
				$js .= @file_get_contents($jsFile);
			}
			foreach(glob($dir.'/*.css') as $cssFile) {
				$css .= @file_get_contents($cssFile);
			}
		}

		$js .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.js");
		$css .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.css");

		TikiLib::lib("cache")->cacheItem("codemirror_js", $js);
		TikiLib::lib("cache")->cacheItem("codemirror_css", $css);
	} else {
		$js = TikiLib::lib("cache")->getCached("codemirror_js");
		$css = TikiLib::lib("cache")->getCached("codemirror_css");
	}

	TikiLib::lib("header")
		->add_js($js)
		->add_css($css);
}