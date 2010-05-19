<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $headerlib;

$phplayers_headers = <<<EOS
<link rel="StyleSheet" href="{$headerlib->convert_cdn('lib/phplayers/layerstreemenu.css')}" type="text/css" />
<link rel="StyleSheet" href="{$headerlib->convert_cdn('lib/phplayers/layerstreemenu-hidden.css')}" type="text/css" />
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var numl;var toBeHidden;
EOS;

$phplayers_headers .= file_get_contents('lib/phplayers/libjs/layersmenu-browser_detection.js');

$phplayers_headers .= <<<EOS
//--><!]]>
</script>
EOS;

global $LayersMenu, $TreeMenu, $PHPTreeMenu, $PlainMenu;
$smarty->assign_by_ref('phplayers_LayersMenu', $LayersMenu);
$smarty->assign('phplayers_headers', $phplayers_headers);

$headerlib->add_jq_onready('phplayersmenu_loaded = 1;');
