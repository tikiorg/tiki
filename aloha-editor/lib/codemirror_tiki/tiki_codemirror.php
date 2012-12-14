<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function codemirrorModes($minify = true)
{
	global $prefs, $tikidomainslash;
	$js = '';
	$css = '';

	$target = 'temp/public/'.$tikidomainslash;
	$jsModes = $target . 'codemirror_modes.js';
	$cssModes = $target . 'codemirror_modes.css';

	if (!file_exists($jsModes) || !file_exists($cssModes)) {
		//codemirror theme
		$js .= 'window.codeMirrorTheme = "' . $prefs['feature_syntax_highlighter_theme'] .'";';
		//load modes first
		//tiki first, where are our priorities!
		$js .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.js");
		$css .= @file_get_contents("lib/codemirror_tiki/mode/tiki/tiki.css");

		foreach (glob('lib/codemirror/mode/*', GLOB_ONLYDIR) as $dir) {
			foreach (glob($dir.'/*.js') as $jsFile) {
				$js .= "//" . $jsFile . "\n";
				$js .= "try{" . @file_get_contents($jsFile) . "}catch(e){}";
			}
			foreach (glob($dir.'/*.css') as $cssFile) {
				$css .= "/*" . $cssFile . "*/\n";
				$css .= @file_get_contents($cssFile);
			}
		}

		//load themes
		foreach (glob('lib/codemirror/theme/*.css') as $cssFile) {
			$css .= @file_get_contents($cssFile);
		}

		file_put_contents($jsModes, $js);
		chmod($jsModes, 0644);

		file_put_contents($cssModes, $css);
		chmod($cssModes, 0644);
	}

	TikiLib::lib("header")
		->add_jsfile_dependancy($jsModes)
		->add_cssfile($cssModes);
}
