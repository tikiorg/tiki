<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_code.php,v 1.15 2004-09-08 19:52:38 mose Exp $
// Displays a snippet of code
// Parameters: ln => line numbering (default false)
// Example:
// {CODE()}
//  print("foo");
// {CODE}
function wikiplugin_code_help() {
//	$help = "Displays a snippet of code.\n";
//	$help.= "Parameters\n";
//	$help.= "* ln=>1 provides line-numbering\n";
//	$help.= "* colors=>php highlights phpcode (other syntaxes to come)\n";
//	$help.= "( note : those parameters are exclusive for now )\n";
//	$help.= "* caption=>provides a caption for the code\n";
//	$help.= "* wrap=>allows line wrapping in the code\n";
//	$help.= "* wiki=>allow wiki interpolation of the code\n";
	$help = tra("Displays a snippet of code").":<br />~np~{CODE(ln=>1,colors=>php|highlights|phpcode),caption=>caption text,wrap=>1,wiki=>1}".tra("code")."{CODE}~/np~ - ''".tra("note: colors and ln are exclusive")."''";
	return tra($help);
}

function decodeHTML($string) {
    $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
    $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
    return $string;
}

function wikiplugin_code($data, $params) {
	if( is_array( $params ) )
	{
	    extract ($params);
	}

	$code = $data;
	$out = '';

	if (isset($caption)) {
		$out = '<div class="codecaption">'.$caption.'</div>';
	}

	if (isset($colors) and ($colors == 'php')) {
		$out.= "<div class='codelisting'>~np~".highlight_string(decodeHTML(trim($code)),1)."~/np~</div>";
	} else {
		if (isset($ln) && $ln == 1) {
			$lines = explode("\n", $code);

			$i = 1; // current line number
			$code = '';
			// Will skip leading and trailing empty lines
			// to make snippet look better :)
			$fl = 0;          // 'first code line printed' flag
			$ae = '';         // 

			foreach ($lines as $line) {
				$len = strlen($line);

				if (!($len || $fl))
					continue; // skip leading empty lines

				if ($len) {
					// OK len >0
					$code .= $ae . ($fl ? "\n" : '') . sprintf("%3d", $i). ':  ' . $line;

					$fl = 1; // first line already printed
					$ae = '';
				} else {
					$ae .= "\n" . sprintf("%3d", $i). ':  ' . $line;
				}
				$i++;
			}
			$code = trim($code);
		}
		if (isset($wrap) && $wrap == 1)
		{
		    if (isset($wiki) && $wiki == 1) {
			$out.= "<div class='codelisting'>\n".trim($code)."\n</div>";
		    } else {
			$out.= "<div class='codelisting'>~np~".trim($code)."~/np~</div>";
		    }
		} else {
		    if (isset($wiki) && $wiki == 1) {
			$out.= "<pre class='codelisting'>\n".trim($code)."\n</pre>";
		    } else {
			$out.= "<pre class='codelisting'>~np~".trim($code)."~/np~</pre>";
		    }
		    //$data = "<div class='codelisting'><pre>" . $code . "</pre></div>";
		}
	}
	return $out;
}

?>
