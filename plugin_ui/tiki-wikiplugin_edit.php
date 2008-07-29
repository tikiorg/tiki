<?php
require 'tiki-setup.php';

if( ! isset( $_POST['page'], $_POST['content'], $_POST['index'], $_POST['type'], $_SERVER['HTTP_REFERER'] ) )
	die( 'Missing parameters' );

$page = $_POST['page'];

$plugin = basename( $_POST['type'] );
$type = strtoupper( $plugin );
$file = 'lib/wiki-plugins/wikiplugin_' . $plugin . '.php';
$info = "wikiplugin_{$plugin}_info";

if( ! file_exists( $file ) )
	exit;

include $file;

if( ! function_exists( $info ) )
	exit;

$meta = $info();

$info = $tikilib->get_page_info($page);
$tikilib->get_perm_object($page, 'wiki page', $info, true);
if ($tiki_p_edit != 'y') {
	header( "Location: {$_SERVER['HTTP_REFERER']}" );
	exit;
}
$content = $_POST['content'];
$current = $info['data'];

$pos = 0;
$count = 0;
while( false !== $pos = strpos( $current, "{{$type}(", $pos + 1 ) )
{
	++$count;

	if( $_POST['index'] == $count )
	{
		$endparam = strpos( $current, ')}', $pos );
		if( false === $endparam )
			die( 'oops.' );
		$body = $endparam + 2;
		$endbody = strpos( $current, "{{$type}}", $endparam );
		if( false === $endbody )
			die( 'oops.' );

		$before = substr( $current, 0, $body );
		$after = substr( $current, $endbody );

		// If parameters are provided, rebuild the parameter line
		if( isset( $_POST['params'] ) && is_array( $_POST['params'] ) )
		{
			$values = array_intersect_key( $_POST['params'], $meta['params'] );

			$parts = array();
			foreach( $values as $key => $value )
				if( ! empty( $value ) )
					$parts[] = "$key=>\"$value\"";

			$params = implode( ',', $parts );

			$before = substr( $before, 0, $pos )
				. "{{$type}($params)}";
		}

		// Replace the content
		$content = $before . $content . $after;

		$tikilib->update_page( $page, $content, tra('Image annotations changed.'), $user, $_SERVER['REMOTE_ADDR'] );
	}
}

header( "Location: {$_SERVER['HTTP_REFERER']}" );
exit;

?>
