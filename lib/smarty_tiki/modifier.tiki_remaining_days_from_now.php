<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

function smarty_modifier_tiki_remaining_days_from_now($time, $format) {
	global $prefs, $tikilib;
	
	$iNbDayBetween = round( ( $time - $tikilib->now ) / ( 60 * 60 * 24 ) );
	if ( $iNbDayBetween > 0 ) {
		return sprintf( ( $iNbDayBetween > 1 ? tra('in %s days, the %s') : tra('in %s day, the %s') ), '<b>' . $iNbDayBetween . '</b>', $tikilib->date_format($format, $time) );
	} else {
		return '-';
	}
}
