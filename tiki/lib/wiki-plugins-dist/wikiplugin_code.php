<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins-dist/wikiplugin_code.php,v 1.6 2003-08-07 04:34:17 rossta Exp $
// Displays a snippet of code
// Parameters: ln => line numbering (default false)
// Example:
// {CODE()}
//  print("foo");
// {CODE}
function wikiplugin_code_help() {
	return "Displays a snippet of code.\nSet optional paramater -+ln+- to 1 if you need line numbering feature.";
}

function wikiplugin_code($data, $params) {
	$code = htmlspecialchars($data);

	extract ($params);

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
			} else
				$ae .= "\n" . sprintf("%3d", $i). ':  ' . $line;

			$i++;
		}

		$code = rtrim($code);
	}                    //else $code=$data;

	// Wrap listing into div
	$data = "<div class='codelisting'><pre>" . $code . "</pre></div>";

	return $data;
}

?>