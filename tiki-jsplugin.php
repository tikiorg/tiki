<?php

if( !isset( $_GET['plugin'] ) )
	exit;

$plugin = basename( $_GET['plugin'] );
$file = 'lib/wiki-plugins/wikiplugin_' . $plugin . '.php';
$info = "wikiplugin_{$plugin}_info";

if( file_exists( "temp/cache/wikiplugin_$plugin" ) )
{
	readfile( "temp/cache/wikiplugin_$plugin" );
	exit;
}

if( ! file_exists( $file ) )
	exit;

include 'tiki-setup.php';
include $file;

if( ! function_exists( $info ) )
	exit;

ob_start();

?>

if( ! tiki_plugins )
	var tiki_plugins = {};

tiki_plugins[<?php echo json_encode( $plugin ) ?>] = <?php echo json_encode( $info() ) ?>;

<?php

$content = ob_get_contents();
file_put_contents( "temp/cache/wikiplugin_$plugin", $content );
ob_end_flush();
	
?>
