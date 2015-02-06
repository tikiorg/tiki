#!/usr/bin/php
<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

dl('mysql.so');
include "/usr/local/tikiwiki/db/local.php";
$db = mysql_connect($host_tiki, $user_tiki, $pass_tiki);
mysql_select_db($dbs_tiki);

$pre = "usermap_". gmdate("Y-m-d");
$mif = "mif_$pre.mif";
$mid = "mif_$pre.mid";

if (file_exists("maps/$mif")) @ unlink("maps/$mif");
if (file_exists("maps/$mid")) @ unlink("maps/$mid");

$fmif = fopen("maps/$mif", "w");
$fmid = fopen("maps/$mid", "w");

fputs($fmif, "Version 300\n");
fputs($fmif, "Charset \"WindowsLatin1\"\n");
fputs($fmif, "Delimiter \",\"\n");
fputs($fmif, "CoordSys Earth Projection 1, 104\n");
fputs($fmif, "Columns 3\n");
fputs($fmif, "  user Char(40)\n");
fputs($fmif, "  Lon float\n");
fputs($fmif, "  Lat float\n");
fputs($fmif, "Data\n");

$query = "select login from users_users";
$results = mysql_query($query);
while ($r = mysql_fetch_row($results)) {
	$retlat = mysql_query("select value from tiki_user_preferences where prefName='lat' and user='". addslashes($r[0]) ."'");
	$lat = number_format(@ mysql_result($retlat, 0), 5);
	$retlon = mysql_query("select value from tiki_user_preferences where prefName='lon' and user='". addslashes($r[0]) ."'");
	$lon = number_format(@ mysql_result($retlon, 0), 5);
	if ($lon != 0 and $lat != 0 and !strpos($lon . $lat, ',')) {
		fputs($fmif, "Point " . $lon . " " . $lat . "\n");
		fputs($fmif, "   Symbol (34,16711680,9)\n");
		fputs($fmid, "\"" . $r[0] . "\",$lon,$lat\n");
	}
}
fclose($fmif);
fclose($fmid);
