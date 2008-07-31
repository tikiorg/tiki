<?php
include 'tiki-setup.php';

if( !isset( $_GET['plugin'] ) )
	exit;

$plugin = basename( $_GET['plugin'] );
$file = 'lib/wiki-plugins/wikiplugin_' . $plugin . '.php';
$info = "wikiplugin_{$plugin}_info";

if( ! file_exists( $file ) )
	exit;

include $file;

if( ! function_exists( $info ) )
	exit;

?>

if( ! tiki_plugins )
	var tiki_plugins = {};

tiki_plugins[<?php echo json_encode( $plugin ) ?>] = <?php echo json_encode( $info() ) ?>;

