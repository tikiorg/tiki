<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_code.php,v 1.10 2003-09-08 14:52:23 sylvieg Exp $
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
	$help = tra("Displays a snippet of code").":<br />~np~{CODE(ln=>1,colors=>php|highlights|phpcode)}".tra("code")."{CODE}~/np~ - ''".tra("note: those parameters are exclusive")."''";
	return tra($help);
}

function decodeHTML($string) {
    $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
    $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
    return $string;
}

function wikiplugin_code($data, $params) {
	$code = $data;

	extract ($params);

	if (isset($colors) and ($colors == 'php')) {
		$data = "<div class='codelisting'>".highlight_string(decodeHTML($code),1)."</div>";
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
			$code = rtrim($code);
		}
		$data = "<div class='codelisting'><pre>" . $code . "</pre></div>";
	}
	return $data;
}

?>
