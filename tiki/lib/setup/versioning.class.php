<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/versioning.class.php,v 1.1.2.3 2008-03-02 19:37:27 lphuberdeau Exp $
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
	// This is a pre-candidate release of 1.10.0
	var $cvs_tree;
	var $cvs_branch;

	function Versioning() {
		// Set the development cycle.
		// If this is a RELEASE version (ie. being bundled for tarball
		// packaging), then set $cvs_tree to FALSE.  If this is a
		// POST-release version then set $cvs_tree to
		// TRUE, and set the $cvs_branch to be either the release
		// version, or the next version number for which a release
		// candidate is being prepared.  This value will be used as a
		// display value to accurately depict the version being used.
		// For example, current release is 1.9.8.3, but this version
		// is actually a pre-candidate 1.10-BRANCH version.  We can't
		// use 1.10 as a release value because it hasn't been released,
		// so we use a $dev_version of 1.10a (1.10 alpha).  For
		// versioning, the system compares 1.9.8.3 to the master release
		// version, but for display purposes, an "actual_version" variable
		// can now be used and display 1.10a instead.  Likewise, if we're 
		// using a 1.9.8.3 CVS version, we would set $cvs_tree to TRUE as 
		// soon as the 1.9.8.3 package is released and leave the 
		// $cvs_branch as 1.9.8.3.

		$this->cvs_tree = TRUE;
		$this->cvs_branch = '1.10a';
	}

	// Returns an array of all used Tiki stars.
	function tikiStars() {
		return array(
			1=>'Spica',
			2=>'Shaula',
			3=>'Ras Algheti',
			4=>'Capella',
			5=>'Antares',
			6=>'Pollux',
			7=>'Mira',
			8=>'Regulus',
			9=>'Tau Ceti',
			10=>'Era Carinae',
			11=>'Polaris',
			12=>'Sirius',
			13=>'Arcturus'
		);
	}

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
			13=>'1.9.8.3',
			14=>'1.9.9',
			15=>'1.9.10',
			16=>'1.9.10.1',
			17=>'1.10.0b1',
		);
	}
	
	// Gets the latest star used by Tiki.
	function getStar() {
		$stars = $this->tikiStars();
		$star = $stars[count($stars)];

		return $star;
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

	// Get the display version.
	function getDisplayVersion() {
		if ($this->cvs_tree) {
			$version = $this->cvs_branch . " (CVS)";
		} else {
			$version = $this->setVersion();
		}

		return $version;
	}
}

