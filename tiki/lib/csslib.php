<?php

class cssLib extends TikiLib {
	function cssLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to TemplatesLib constructor");
		}

		$this->db = $db;
	}

	function list_css($path) {
		$back = array();

		$oldir = getcwd();
		chdir ($path);
		$handle = opendir('.');

		while ($file = readdir($handle)) {
			if ((substr($file, -4, 4) == ".css") and (ereg("^[-_a-zA-Z0-9\.]*$", $file))) {
				$back[] = substr($file, 0, -4);
			}
		}

		chdir ($oldir);
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
}

$csslib = new cssLib($dbTiki);

?>