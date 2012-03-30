<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function codemirrorModes($minify = true)
{
	$js = '';
	$css = '';

	if (TikiLib::lib("cache")->isCached("codemirror_js" . ($minify ? "min" : "")) == false || TikiLib::lib("cache")->isCached("codemirror_css" . ($minify ? "min" : "")) == false) {
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

		if ($minify) {
			require_once("lib/minify/JSMin.php");
			$js = JSMin::minify($js);

			require_once('lib/pear/Minify/CSS/Compressor.php');
			$css = Minify_CSS_Compressor::process($css);
		}

		TikiLib::lib("cache")->cacheItem("codemirror_js" . ($minify ? "min" : ""), $js);
		TikiLib::lib("cache")->cacheItem("codemirror_css" . ($minify ? "min" : ""), $css);
	} else {
		$js = TikiLib::lib("cache")->getCached("codemirror_js" . ($minify ? "min" : ""));
		$css = TikiLib::lib("cache")->getCached("codemirror_css" . ($minify ? "min" : ""));
	}

	TikiLib::lib("header")
		->add_js($js)
		->add_css($css);
}