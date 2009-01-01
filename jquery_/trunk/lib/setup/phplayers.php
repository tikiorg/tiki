<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$phplayers_headers = <<<EOS
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu.css" type="text/css" />
<link rel="StyleSheet" href="lib/phplayers/layerstreemenu-hidden.css" type="text/css" />
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var numl;var toBeHidden;
EOS;

$phplayers_headers .= file_get_contents('lib/phplayers/libjs/layersmenu-browser_detection.js');

$phplayers_headers .= <<<EOS
//--><!]]>
</script>
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu-library.js"></script>
<script type="text/javascript" src="lib/phplayers/libjs/layersmenu.js"></script>
<script type="text/javascript" src="lib/phplayers/libjs/layerstreemenu-cookies.js"></script>
EOS;

global $LayersMenu, $TreeMenu, $PHPTreeMenu, $PlainMenu;
$smarty->assign_by_ref('phplayers_LayersMenu', $LayersMenu);
$smarty->assign('phplayers_headers', $phplayers_headers);
