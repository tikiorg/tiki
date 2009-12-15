<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-editdrawing.php,v 1.20 2007-03-06 19:29:48 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


require_once ("tiki-setup.php");

if (($tiki_p_admin_drawings != 'y') && ($tiki_p_edit_drawings != 'y')) {
	die;
}
?>
<html>
	<head>
		<title>JGraphpad Loader Window</title>
<?
if (isset($_REQUEST["close"])) {
	print ("<script type='text/javascript'>window.opener.location.reload();</script>");

	print ("<script type='text/javascript'>window.close();</script>");
	die;
}

if (isset($_REQUEST['page'])) {
  $tikilib->invalidate_cache($_REQUEST['page']);
}  
$name = $_REQUEST["drawing"];
$path = $_REQUEST["path"];

?>

	</head>

	<body>
		<applet archive="lib/jgraphpad/jgraphpad.jar" code="org.jgraph.JGraphpad.class" height="40">
			<param name="drawpath" value="<?php echo $path?>/img/wiki/<?php echo ($tikidomain)?"$tikidomain/$name":$name; ?>.pad_xml" />

			<param name="gifpath" value="<?php echo $path?>/img/wiki/<?php echo ($tikidomain)?"$tikidomain/$name":$name; ?>.gif" />

			<param name="savepath" value="<?php echo $path?>/jhot.php" />

			<param name="viewpath" value="tiki-editdrawing.php?close=1" />
		</applet>
	</body>
</html>
