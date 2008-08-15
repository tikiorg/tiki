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

	foreach( glob( 'lib/wiki-plugins/wikiplugin_*.php' ) as $file )
	{
		$base = basename( $file );
		$plugin = substr( $base, 11, -4 );

		$files[$plugin] = $file;
	}
}
else
{
	$plugin = basename( $_GET['plugin'] );
	$file = 'lib/wiki-plugins/wikiplugin_' . $plugin . '.php';
	$cache = "temp/cache/wikiplugin_$plugin";

	if( file_exists( $cache ) )
	{
		readfile( $cache );
		exit;
	}

	$files[$plugin] = $file;
}

include 'tiki-setup.php';

ob_start();

?>

if( ! tiki_plugins )
	var tiki_plugins = {};

<?php foreach( $files as $plugin => $file ):
	$info = "wikiplugin_{$plugin}_info";

	if( ! file_exists( $file ) )
		continue;

	require_once $file;
	if( ! function_exists( $info ) )
		continue;
?>

tiki_plugins[<?php echo json_encode( $plugin ) ?>] = <?php echo json_encode( $info() ) ?>;

<?php endforeach;

$content = ob_get_contents();
file_put_contents( $cache, $content );
ob_end_flush();
	
?>
