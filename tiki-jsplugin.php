<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * This is included in the html generated for each wiki page. It is included for each plugin used on a wiki page.
 * The include is of the form <script type="text/javascript" src="tiki-jsplugin.php?plugin=googledoc"></script>
 * If no plugin name is given, a list of all the plugins is used instead
 * The java script generated defines tiki_plugins["pluginname"] with meta data for the parameters of the plugin.
 * This is then used to allow a nice way for the editor of the page to use a form to edit the plug-in when they
 * click the little edit icon next to the plug-ins generated html.
 * 
 * Cached by language to allow translations (tiki 5)
 */

header('content-type: application/x-javascript');
header('Cache-Control: no-cache, pre-check=0, post-check=0');
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+3600*24*365*10) . ' GMT');

require_once 'tiki-filter-base.php';

$filter = TikiFilter::get('xss');
$_REQUEST['plugin'] = isset($_GET['plugin']) ? $_GET['plugin'] = $filter->filter($_GET['plugin']) : '';
$filter = TikiFilter::get('alpha');
$_REQUEST['language'] = isset($_GET['language']) ? $_GET['language'] = $filter->filter($_GET['language']) : '';

$all = empty( $_REQUEST['plugin'] );

$files = array();

if( $all )
{
	$cache = "temp/cache/wikiplugin_ALL_".$_REQUEST['language'];

	if( file_exists( $cache ) )
	{
		readfile( $cache );
		exit;
	}

	include 'tiki-setup.php';

	$plugins = $tikilib->plugin_get_list();
}
else
{
	$plugin = basename( $_REQUEST['plugin'] );

	$cache = 'temp/cache/wikiplugin_'.$plugin.'_'.$_REQUEST['language'];

	if( file_exists( $cache ) )
	{
		readfile( $cache );
		exit;
	}

	$saveP=$plugin;
	include 'tiki-setup.php';
	$plugins = array( $saveP );
}

ob_start();

?>
if( typeof tiki_plugins == 'undefined' ) { var tiki_plugins = {}; }
<?php foreach( $plugins as $plugin ):
	if( ! $info = $tikilib->plugin_info( $plugin ) )
		continue;
?>
tiki_plugins.<?php echo $plugin ?> = <?php echo json_encode( $info ) ?>;
<?php endforeach;

$content = ob_get_contents();
file_put_contents( $cache, $content );
ob_end_flush();
