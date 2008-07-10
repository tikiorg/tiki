<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class cssLib extends TikiLib {
	function cssLib($db) {
		$this->TikiLib($db);
	}

	function list_css($path) {
		$back = array();

		$oldir = getcwd();
		chdir ($path);
		$handle = opendir('.');

		while ($file = basename(readdir($handle))) {
			if ((substr($file, -4, 4) == ".css") and (ereg("^[-_a-zA-Z0-9\.]*$", $file))) {
				$back[] = substr($file, 0, -4);
			}
		}

		chdir ($oldir);
		sort($back);
		return $back;
	}

	function browse_css($path) {
		if (!is_file($path)) {
			return array("error" => "No such file : $path");
		}

		$meat = implode("", file($path));

		$find[0] = "/\}/";
		$repl[0] = "\n}\n";

		$find[1] = "/\{/";
		$repl[1] = "\n{\n";

		$find[2] = "/\/\*/";
		$repl[2] = "\n/*\n";

		$find[3] = "/\*\//";
		$repl[3] = "\n*/\n";

		$find[4] = "/;/";
		$repl[4] = ";\n";

		$find[5] = "/(W|w)hite/";
		$repl[5] = "#FFFFFF";

		$find[6] = "/(B|b)lack/";
		$repl[6] = "#000000";

		$res = preg_replace($find, $repl, $meat);
		return array(
			"error" => '',
			"content" => split("\n", $res)
		);
	}

	function parse_css($data) {
		$back = array();

		$index = 0;
		$type = '';

		foreach ($data as $line) {
			$line = trim($line);

			if ($line) {
				if (($type != "comment") and ($line == "/*")) {
					$type = "comment";

					$index++;
					$back["$index"]["comment"] = '';
					$back["$index"]["items"] = array();
					$back["$index"]["attributes"] = array();
				} elseif (($type == "comment") and ($line == "*/")) {
					$type = "";
				} elseif ($type == "comment") {
					$back["$index"]["comment"] .= "$line\n";
				} elseif (($type == "items") and ($line == "{")) {
					$type = "attributes";
				} elseif ($type == "items") {
					$li = split(",", $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]["items"][] = $l;
					}
				} elseif (($type == "attributes") and ($line == "}")) {
					$type = "";

					$index++;
					$back["$index"]["comment"] = '';
					$back["$index"]["items"] = array();
					$back["$index"]["attributes"] = array();
				} elseif ($type == "attributes") {
					$parts = split(":", str_replace(";", "", $line));

					if (isset($parts[0]) && isset($parts[1])) {
						$obj = trim($parts[0]);

						$back["$index"]["attributes"]["$obj"] = trim($parts[1]);
					}
				} else {
					$li = split(",", $line);

					foreach ($li as $l) {
						$l = trim($l);

						if ($l)
							$back["$index"]["items"][] = $l;
					}

					$type = "items";
				}

				$back["content"] = $line;
			}
		}

		return $back;
	}

	/**
	 *  Find the version of Tiki that a CSS is compatible with
	 *  @returns int the version number, eg 109 for v1.9, 110 for v1.10
	 *  or false if the version isn't specified
	 *
	 *  @TODO: cache the results
	 *  @TODO: only read the first 30 lines or so of the file
	 */
	function version_css($path) {
		if (!file_exists($path))
			return false;
		$data = implode("", file($path));
		$pos = strpos($data, "@version");
		if( $pos === false ) { return false; }
		// get version
		preg_match("/(@[V|v]ersion):?\s?([\d]+)\.([\d]+)/i",
		$data, $matches);
		$version = $matches[2].".".$matches[3];
		return $version;
	}

	/**
	 *  Work out which transition stylesheet to use with a given stylesheet
	 *  @returns path to transition stylesheet
	 *  or empty string if a transition style isn't required
	 *  @TODO: check that the transition stylesheet exists
	 *  @TODO: cache results
	 *  @TODO: return empty string if CSS file is /newer/ than db version?
	 */
	function transition_css($path, $default_ver='1.9') {
		global $dbversion_tiki;
		$cssversion = $this->version_css($path);
		// assume default_ver if no @version string
		$cssversion = $cssversion ? $cssversion : $default_ver;
		if( $dbversion_tiki == $cssversion || !$cssversion ) { return ''; }
		return $cssversion."to".$dbversion_tiki.".css";
	}
}
global $dbTiki;
$csslib = new cssLib($dbTiki);

?>
