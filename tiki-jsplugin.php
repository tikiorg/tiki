<?php
// $Id$
/*
 * This is included in the html generated for each wiki page. It is included for each plugin used on a wiki page.
 * The include is of the form <script type="text/javascript" src="tiki-jsplugin.php?plugin=googledoc"></script>
 * If no plugin name is given, a list of all the plugins is used instead
 * The java script generated defines tiki_plugins["pluginname"] with meta data for the parameters of the plugin.
 * This is then used to allow a nice way for the editor of the page to use a form to edit the plug-in when they
 * click the little edit icon next to the plug-ins generated html.
 *
 */

header('content-type: application/x-javascript');

$all = !isset( $_GET['plugin'] );

$files = array();

if( $all )
{
	$cache = "temp/cache/wikiplugin_ALL";

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
	$plugin = basename( $_GET['plugin'] );

	$cache = "temp/cache/wikiplugin_$plugin";

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
if( typeof tiki_plugins == 'undefined' ) { tiki_plugins = {}; }
<?php foreach( $plugins as $plugin ):
	if( ! $info = $tikilib->plugin_info( $plugin ) )
		continue;
?>
tiki_plugins.<?php echo $plugin ?> = <?php echo json_encode( $info ) ?>;
<?php endforeach;

$content = ob_get_contents();
file_put_contents( $cache, $content );
ob_end_flush();
