<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/versioning.class.php,v 1.1.2.1 2007-12-02 23:53:13 kerrnel22 Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

class Versioning {

	// Returns an array of all valid versions of Tikiwiki.
	function tikiVersions() {
		// These are all the valid release versions of Tiki.
		// Newest version goes at the end.
		// Release Managers should update this array before
		// release.
		return array(
			1=>'1.9.1',
			2=>'1.9.1.1',
			3=>'1.9.2',
			4=>'1.9.3.1',
			5=>'1.9.3.2',
			6=>'1.9.4',
			7=>'1.9.5',
			8=>'1.9.6',
			9=>'1.9.7',
			10=>'1.9.8',
			11=>'1.9.8.1',
			12=>'1.9.8.2',
			13=>'1.9.8.3'
		);
	}
	
	// Determines the currently-running version of Tikiwiki.
	function getVersion() {
		$versions = $this->tikiVersions();
		$version = $versions[count($versions)];

		return $version;
	}

	// Pulls the latest release version of Tikiwiki from
	// a central site.
	function pollVersion() {
		$fp = fsockopen("tikiwiki.org", 80, $errno, $errstr, 10);
		if (!$fp) {
			$version = '';
		} else {
			$send = "GET /stable.version HTTP/1.1\r\n";
			$send .= "Host: tikiwiki.org\r\n";
			$send .= "Connection: Close\r\n\r\n";

			fputs ($fp, $send);

			// The last line of response will be the version.
			while ($instr = fgets($fp)) {
				$version = $instr;
			}
		}

		return $version;
	}


	// Compare running version to latest release version and see
	// if an upgrade is needed.
	function newVersionAvailable($oldVer, $newVer) {
		if ($newVer == '') {
			$upgrade = 0;
		} else {
			$oN = split("\.", $oldVer);
			$nN = split("\.", $newVer);
			$upgrade = 0;
			for ($count = 0; isset($nN[$count]); $count++) {
				if (intval($nN[$count]) > intval($oN[$count])) {
					$upgrade = 1;
					break;
				}
			}
		}

		return $upgrade;
	}
}
