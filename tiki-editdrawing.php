<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-editdrawing.php,v 1.13 2004-03-28 07:32:23 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-editdrawing.php,v 1.13 2004-03-28 07:32:23 mose Exp $
require_once ("tiki-setup.php");

if (($tiki_p_admin_drawings != 'y') && ($tiki_p_edit_drawings != 'y')) {
	die;
}

if (isset($_REQUEST["close"])) {
	print ("<script language='Javascript' type='text/javascript'>window.opener.location.reload();</script>");

	print ("<script language='Javascript' type='text/javascript'>window.close();</script>");
	die;
}

if (isset($_REQUEST['page'])) {
  $tikilib->invalidate_cache($_REQUEST['page']);
}  
$name = $_REQUEST["drawing"];
$path = $_REQUEST["path"];

?>

<html>
	<head>
	</head>

	<body>
		<applet archive = "lib/jgraphpad/jgraphpad.jar" code = "org.jgraph.JGraphpad.class"  height="40">
			<param name = "drawpath" value = "<?php echo $path?>/img/wiki/<?php echo $tikidomain.$name?>.pad_xml">

			<param name = "gifpath" value = "<?php echo $path?>/img/wiki/<?php echo $tikidomain.$name?>.gif">

			<param name = "savepath" value = "<?php echo $path?>/jhot.php">

			<param name = "viewpath" value = "tiki-editdrawing.php?close=1">
		</applet>
	</body>
</html>

<?php
/*

<!--
<applet code="CH.ifa.draw.twiki.TWikiDraw.class" archive="lib/jHotDraw/twikidraw.jar" width=100% height=40>
	<param name="drawpath" value="

< ?php

echo $path

? >

/img/wiki/

< ?php

echo $tikidomain . $name
 
? >

.draw">

<param name = "gifpath" value = "<?php echo $path?>/img/wiki/<?php echo $tikidomain.$name?>.gif">

<param name = "extracolors" value = "Aquamarine=#70DB93,New Tan=EBC79E,Sea Green=238E68,Motorola Blue=#3ff">

<param name = "savepath" value = "<?php echo $path?>/jhot.php">

<param name = "viewpath" value = "tiki-editdrawing.php?close=1">

<param name = "helppath" value = ".">

</applet>

-->
*/

?>
