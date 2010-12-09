<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Displays a snippet of code

function wikiplugin_code_info() {
	return array(
		'name' => tra('Code'),
		'documentation' => tra('PluginCode'),
		'description' => tra('Displays a snippet of code'),
		'prefs' => array('wikiplugin_code'),
		'body' => tra('Code'),
		'icon' => 'pics/icons/page_white_code.png',
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Code snippet label.'),
			),
			'wrap' => array(
				'required' => false,
				'name' => tra('Word Wrap'),
				'description' => tra('Enable word wrapping on the code to avoid breaking the layout. May not be used with line numbers if Geshi version 1.0.8.9+'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Syntax highlighting to use. GeSHi - Generic Syntax Highlighter must be installed for languages other than php. Without GeSHi, the php tag must be included at the 
									beginning of the displayed code for the highlighting to work. May not be used with line numbers if Geshi version < 1.0.8.9. Available: php, html, sql, javascript, css, java, c, doxygen, delphi, rsplus...'),
				'advanced' => true,
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line Numbers'),
				'description' => tra('Show line numbers for each line of code. May not be used with colors unless GeSHI is installed.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'advanced' => true,
			),
			'wiki' => array(
				'required' => false,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Parse wiki syntax within the code snippet (not parsed by default)'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'rtl' => array(
				'required' => false,
				'name' => tra('Right to Left'),
				'description' => tra('Switch the text display from left to right to right to left  (left to right by default)'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'advanced' => true,
			),
			'ishtml' => array(
				'required' => false,
				'name' => tra('Content is HTML'),
				'description' => tra('When set to 1 (Yes), HTML will still be processed (presented as is by default)'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Show HTML'), 'value' => '0'),
					array('text' => tra('Interpret HTML'), 'value' => '1'),
				),
			),
			'cpy' => array(
				'required' => false,
				'name' => tra('Copy To Clipboard'),
				'description' => tra('Copy the contents of the code box to the clipboard  (not copied to clipboard by default)'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'advanced' => true,
			),
		),
	);
}

function wikiplugin_code($data, $params) {
	static $code_count;
	$default = array('cpy' => 0);
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	$code = trim($data);

	$parse_wiki = ( isset($wiki) && $wiki == 1 );
	$escape_html = ( ! isset($ishtml) || $ishtml != 1 );
	$id = 'codebox'.$code_count;
	$boxid = " id=\"$id\" ";

	// Detect if GeSHI (Generic Syntax Highlighter) is available
	$geshi_paths = array(
		'lib/geshi/class.geshi.php', // Tiki manual (or mod) install of GeSHI v1.2 in lib/geshi/
		'lib/geshi/geshi.php', // Tiki manual (or mod) install of GeSHI v1.0 in lib/geshi/
		'/usr/share/php-geshi/geshi.php' // php-geshi package v1.0
	);
	foreach ( $geshi_paths as $gp ) {
		if ( file_exists($gp) ) {
			require_once($gp);
			break;
		}
	}

	// If 'color' is specified and GeSHI installed, use syntax highlighting with GeSHi
	if ( isset($colors) && $colors != 'highlights' && class_exists('GeSHI') ) {

		$geshi = new GeSHi($code, $colors);

		if ( version_compare(GESHI_VERSION, 1.1) == -1) { // Old API
			if ( isset($ln) && $ln > 0 ) {
				$geshi->set_code_style('background: #f5f5f5;'); //improves line spacing and fancy numbers
				$geshi->set_header_type(GESHI_HEADER_PRE_TABLE); //allows user to select code from screen without line numbers for copying and pasting
				$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS); //highlights every 5th line number
				$geshi->start_line_numbers_at($ln);
			}
			$geshi->set_link_target('_blank');
			$out = $geshi->parse_code();
		} else { // New API
			$out = $geshi->parseCode();
		}

		// Remove first <pre> tag
		if ( $out != '' ) {
			$out = preg_replace('/^<pre[^>]*>(.*)<\/pre>$/', '\\1', $out);
			$out = trim($out);
		}

		if ( ! $escape_html ) $out = TikiLib::htmldecode($out);

	} elseif ( isset($colors) && ( $colors == 'highlights' || $colors == 'php' ) ) {

		$out = highlight_string($code, true);

		// Convert &nbsp; into spaces and <br /> tags into real line breaks, since it will be displayed in a <pre> tag
		$out = str_replace('&nbsp;', ' ', $out);
		$out = preg_replace('/<br[^>]+>/i', "\n", $out);

		// Remove first <code> tag
		$out = preg_replace("#^\s*<code[^>]*>(.*)</code>$#i", '\\1', $out);

		// Remove spaces after the first tag and before the start of the code
		$out = preg_replace("/^\s*(<[^>]+>)\n/", '\\1', $out);
		$out = trim($out);

		if ( ! $escape_html ) $out = TikiLib::htmldecode($out);

	} else {

		$out = trim($code);
		if ( isset($ln) && $ln == 1) {
			$out = '';
			$lines = explode("\n", $code);
			$i = 1; 
			foreach ( $lines as $line ) {
				$out .= sprintf('% 3d', $i).' . '.$line."\n";
				$i++;
			}
		} else {
			$out = $code;
		}

		if ( $escape_html ) $out = htmlentities($out,ENT_COMPAT,"utf-8");
	}

	if ( isset($wrap) && $wrap == 1 ) {
		// Force wrapping in <pre> tag through a CSS hack
		$pre_style = 'white-space:pre-wrap;'
			.' white-space:-moz-pre-wrap !important;'
			.' white-space:-pre-wrap;'
			.' white-space:-o-pre-wrap;'
			.' word-wrap:break-word;';
	} else {
		// If there is no wrapping, display a scrollbar (only if needed) to avoid truncating the text
		$pre_style = 'overflow:auto;';
	}

	$out = '<pre class="codelisting" dir="'.( (isset($rtl) && $rtl == 1) ? 'rtl' : 'ltr').'" style="'.$pre_style.'"'.$boxid.'>'
		.(( $parse_wiki ) ? '' : '~np~')
		.$out
		.(( $parse_wiki ) ? '' : '~/np~')
		.'</pre>'
		.(($cpy && ($code_count < 1)) ? '<script type="text/javascript" src="lib/ZeroClipboard.js"></script>' : '')
		.(( $cpy ) ? '<script language="JavaScript">var clip = new ZeroClipboard.Client();var elem = document.getElementById ("'.$id.'");clip.setText( elem.innerText || elem.textContent );clip.glue( \'d_clip_button'.$id.'\' );clip.addEventListener( \'complete\', function(client, text) {alert("The code has been copied to the clipboard.");} );</script>' : '');

		$out = '<div class="plugincode">'.((isset($caption)) ? '<div class="codecaption">'.$caption.'</div>' : '').(( $cpy ) ? '<div class="codecaption" id="d_clip_button'.$id.'">Copy To Clipboard</div>' : '').$out.'</div>';
	$code_count++;
	return $out;
}
