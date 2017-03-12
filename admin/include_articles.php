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

if (isset($_REQUEST['import'])) {
	if ($check === true) {
		$artlib = TikiLib::lib('art');
		$fname = $_FILES['csvlist']['tmp_name'];
		$msgs = array();
		$result = $artlib->import_csv($fname, $msgs);
		if ($result) {
			Feedback::success(tr('File %0 succesfully imported.', $_FILES['csvlist']['name']), 'session');
		} elseif (!empty($msgs)) {
			Feedback::error(['mes' => $msgs], 'session');
		}
	}
	$adminRedirect = true;
}
