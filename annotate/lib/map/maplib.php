<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class MapLib
{

	function listMaps($mappath) {
		if (!isset($mappath) or !$mappath or !is_dir($mappath)) {
			return ('');
		}
		$files = array();
		$h = opendir($mappath);

		while (($file = readdir($h)) !== false) {
			if (preg_match('/\.map$/i', $file)) {
					$files[] = $file;
			}
		}

		closedir ($h);

		sort ($files);
		
		return ($files);

	}
	
	function listMapsWithRev($mappath) {
		if (!isset($mappath) or !$mappath or !is_dir($mappath)) {
			return ("");
		}
	
	  $files = array();
    $h = opendir($mappath);

    while (($file = readdir($h)) !== false) {
      if (preg_match('/\.map/i', $file)) {
          $files[] = $file;
      }
    }
    closedir ($h);
    sort ($files);
		return ($files);
	}
	
	function listKaMaps($mappath) {
		$files = $this->listMaps($mappath);
		$kamaps = array();
		foreach ($files as $mapfile) {
			$pagedata = file($mappath.$mapfile);
			for ($i=0, $icount_pagedata = count($pagedata); $i < $icount_pagedata; $i++) {
				$key = trim($pagedata[$i]);
				if (strncasecmp($key, "WEB", 3) == 0) {
					//looking for METADATA before the END
					while(strncasecmp($key, "END", 3) != 0) {
						$i++;					
						$key = trim($pagedata[$i]);					
						if (strncasecmp($key, "METADATA", 8) == 0) {
							while(strncasecmp($key, "END", 3) != 0) {
								$i++;					
								$key = trim($pagedata[$i]);
								if (strncasecmp($key, "KAMAP", 5) == 0) {
									$key = preg_replace('/#.*$/', '', $key);
									list($name,$value) = explode('"', $key);
									$scale = explode(",", $value);
									$title = $scale[0];
									$scale = array_slice($scale,1);
									$kmap = array();
									$kmap["title"] = $title;
									$kmap["path"] = $mappath . $mapfile;
									$kmap["scales"] = $scale;
									$kmap["format"] = "PNG24";
									$kamaps[substr($mapfile, 0, -4)] = $kmap;
								}
							}														
						}
					}
				}
			}
		}
		return ($kamaps);
	}
	
}
$maplib = new MapLib;
