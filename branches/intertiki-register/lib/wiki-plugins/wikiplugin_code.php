<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_code.php,v 1.22.2.6 2007-11-25 18:21:21 nyloth Exp $
// Displays a snippet of code
function wikiplugin_code_help() {
	$help = tra("Displays a snippet of code").":<br />~np~{CODE(ln=>1,colors=>php|html|sql|javascript|css|java|c|doxygen|delphi|...,caption=>caption text,wrap=>1,wiki=>1,rtl=>1)}".tra("code")."{CODE}~/np~ - ''".tra("note: colors and ln are exclusive")."''";
	return tra($help);
}

function wikiplugin_code($data, $params) {
	if ( is_array($params) ) {
		extract($params, EXTR_SKIP);
	}
	$code = trim($data);
	$parse_wiki = ( isset($wiki) && $wiki == 1 );

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

		$geshi =& new GeSHi(TikiLib::htmldecode($code), $colors);

		if ( version_compare(GESHI_VERSION, 1.1) == -1) { // Old API
			if ( isset($ln) && $ln > 0 ) {
				$geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
				$geshi->start_line_numbers_at($ln);
			}
			$geshi->set_link_target('_blank');
			$out = $geshi->parse_code();
		} else { // New API
			$out = $geshi->parseCode();
		}

		// Remove first <pre> tag
		if ( $out != '' ) {
			$out = ereg_replace('^<pre[^>]*>(.*)</pre>$', '\\1', $out);
			$out = trim($out);
		}

	} elseif ( isset($colors) && ( $colors == 'highlights' || $colors == 'php' ) ) {

		$out = highlight_string(TikiLib::htmldecode($code), true);

		// Convert &nbsp; into spaces and <br /> tags into real line breaks, since it will be displayed in a <pre> tag
		$out = str_replace('&nbsp;', ' ', $out);
		$out = eregi_replace('<br[^>]+>', "\n", $out);

		// Remove first <code> tag
		$out = eregi_replace('^\s*<code[^>]*>(.*)</code>$', '\\1', $out);

		// Remove spaces after the first tag and before the start of the code
		$out = ereg_replace("^\s*(<[^>]+>)\n", '\\1', $out);
		$out = trim($out);

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

	$out = '<pre class="codelisting" dir="'.( (isset($rtl) && $rtl == 1) ? 'rtl' : 'ltr').'" style="'.$pre_style.'">'
		.(( $parse_wiki ) ? '' : '~np~')
		.$out
		.(( $parse_wiki ) ? '' : '~/np~')
		.'</pre>';

	if ( isset($caption) ) {
		$out = '<div class="codecaption">'.$caption.'</div>'.$out;
	}

	return $out;
}

?>
