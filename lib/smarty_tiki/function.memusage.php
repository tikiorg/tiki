<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_function_memusage($params, $smarty)
{
	if (function_exists('memory_get_peak_usage')) {
		// PHP 5.2+
		$memusage = memory_get_peak_usage();
	} elseif (function_exists('memory_get_usage')) {
		//PHP 4 >= 4.3.2, PHP 5
		$memusage = memory_get_usage();
	} else {
		$memusage = 0;
	}

	if ($memusage > 0) {
		$memunit = "B";
		if ($memusage > 1024) {
			$memusage = $memusage/1024;
			$memunit = "kB";
		}
		if ($memusage>1024) {
			$memusage = $memusage/1024;
			$memunit = "MB";
		}
		if ($memusage>1024) {
			$memusage = $memusage/1024;
			$memunit = "GB";
		}
		print(number_format($memusage, 2) . $memunit);
	} else {
		print (tra("Unknown"));
	}
}
