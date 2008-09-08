<?php

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

	include 'tiki-setup.php';

	$plugins = array( $plugin );
}

ob_start();

?>

if( ! tiki_plugins )
	var tiki_plugins = {};

<?php foreach( $plugins as $plugin ):
	if( ! $info = $tikilib->plugin_info( $plugin ) )
		continue;
?>

tiki_plugins[<?php echo json_encode( $plugin ) ?>] = <?php echo json_encode( $info ) ?>;

<?php endforeach;

$content = ob_get_contents();
file_put_contents( $cache, $content );
ob_end_flush();
	
?>
