<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ExportLib extends TikiLib {
	function ExportLib($db) {
		$this->TikiLib($db);
	}

	function MakeWikiZip() {
		global $tikidomain;
		$zipname = "wikidb.zip";
		include_once ("lib/tar.class.php");
		$tar = new tar();
		$query = "select `pageName` from `tiki_pages` order by ".$this->convert_sortmode("pageName_asc");
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$page = $res["pageName"];
			$content = $this->export_wiki_page($page, 0);
			$tar->addData($page, $content, $this->now);
		}
		$dump = "dump";
		if ($tikidomain) { $dump.= "/$tikidomain"; }
		$tar->toTar("$dump/export.tar", FALSE);
		return '';
	}

	function export_wiki_page($pageName, $nversions = 1) {
		$head = '';
		$head .= "Date: " . $this->date_format("%a, %e %b %Y %H:%M:%S %O"). "\r\n";
		$head .= sprintf("Mime-Version: 1.0 (Produced by Tiki)\r\n");
		$iter = $this->get_page_history($pageName);
		$info = $this->get_page_info($pageName);
		$parts = array();
		$parts[] = MimeifyPageRevision($info);

		if ($nversions > 1 || $nversions == 0) {
			foreach ($iter as $revision) {
				$parts[] = MimeifyPageRevision($revision);

				if ($nversions > 0 && count($parts) >= $nversions)
					break;
			}
		}
		if (count($parts) > 1)
			return $head . MimeMultipart($parts);

		assert ($parts);
		return $head . $parts[0];
	}

	// Returns all the versions for this page
	// without the data itself
	function get_page_history($page) {
		$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment` from `tiki_history` where `pageName`=? order by ".$this->convert_sortmode("version_desc");
		$result = $this->query($query,array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			$aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			$ret[] = $aux;
		}
		return $ret;
	}
}
global $dbTiki;
$exportlib = new ExportLib($dbTiki);

?>
