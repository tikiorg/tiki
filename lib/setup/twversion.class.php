<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

// Class written by Mike Kerr (kerrnel22, tiki-kerrnel@kerris.com)
// March 2008

// This script may only be included - so its better to die if called directly.
// Should generally be instatiated from tiki-setup.php

class TWVersion {
	var $branch;		// Development cycle
	var $version;		// This version
	var $release;		// Current release in same version tree
	var $releases;		// Array of all releases from website
	var $ridx;			// Associative array pointing versions to release index
	var $vidx;			// Index where this version appears in releases array
	var $star;			// Star being used for this version tree
	var $cvs;			// Is this a cvs version or a package?

	function TWVersion() {
		// Set the development branch.  Valid are:
		//   stable   : Represents officially supported releases.
		//   unstable : Represents candidate and test/development releases.
		//   head     : Represents next generation development version.
		$this->branch 	= 'unstable';

		// Set everything else, including defaults.
		$this->version 	= '2.0b2';
		$this->star		= 'Arcturus';
		$this->release 	= $this->version;
		$this->releases	= array();
		$this->ridx 	= array();
		$this->vidx 	= 0;

		// Check for CVS or not
		$this->cvs	= is_dir('CVS') ? 'y' : 'n';
	}

	// Pulls the list of releases in the current branch of Tikiwiki from
	// a central site.
	function pollVersion() {
		$fp = fsockopen("tikiwiki.org", 80, $errno, $errstr, 10);
		if ($fp) {
			$send = "GET /" . $this->branch . ".version HTTP/1.1\r\n";
			$send .= "Host: tikiwiki.org\r\n";
			$send .= "Connection: Close\r\n\r\n";

			fputs ($fp, $send);

			// The last line of response will be the version.
			$payload = 0;
			while ($instr = rtrim(fgets($fp))) {
				// Get to the text of the file, and ignoring the blank line
				// between the content-type coding and the actual payload data.
				if (substr($instr, 14, 10) == 'text/plain') {
					$instr = fgets($fp);
					$instr = fgets($fp);
					$payload = 1;
				}

				// If we've reached the actual text of the file we're
				// trying to retrieve, then proceed.
				if ($payload) {
					$count = array_push($this->releases, $instr);
					$this->ridx[$instr] = $count - 1;
					if ($instr == $this->version) {
						$this->vidx = $this->ridx[$instr];
					}
				}
			}
		}
	}


	// Compare this version to the list and see if there are any releases after
	// it.  Distinguish between upgrades and major releases and return flags
	// for both.
	function newVersionAvailable() {
		if (count($this->releases) == 0) {
			$upgrade = 0;
		} else {
			// Start at the VIDX index, and go through the rest of the
			// ->releases.  Ignore releases that have a bigger a or b
			// number (eg. A.B.C = 1.9.3; ignore 1.10 or 2.x)
			// Store the newest version in the tree in ->release.
			$upgrade = 0;
			$major = 0;
			$velements = explode('.', $this->version);

			for ($idx = $this->vidx; $idx < count($this->releases); $idx++) {
				$relements = explode('.', $this->releases[$idx]);
				if ($relements[0] > $velements[0]
						|| $relements[1] > $velements[1]) {
					$major = 1;
				} else if ($relements[0] == $velements[0]
						&& $relements[1] == $velements[1]) {
					$this->release = $this->releases[$idx];
					if ($idx != $this->vidx) {
						$upgrade = 1;
					}
				}
			}
		}

		return array($upgrade, $major);
	}
}

