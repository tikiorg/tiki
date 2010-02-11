<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
  
// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');
$access->check_feature('feature_wysiwyg');

include_once 'lib/toolbars/toolbarslib.php';

global $tikilib;
$smarty->assign('fckstyle',$tikilib->get_style_path('', '', $prefs['style']));
$smarty->assign('fckstyleoption',$tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']));

$section = isset($_GET['section']) ? $_GET['section'] : 'wiki page';

$toolbars = ToolbarsList::fromPreference( $section );
//file_put_contents('temp/cache/foo', print_r($toolbars->getWysiwygArray(), true));
$smarty->assign('toolbar', $toolbars->getWysiwygArray() );

$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
