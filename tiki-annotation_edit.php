<?php
require 'tiki-setup.php';

if( ! isset( $_POST['page'], $_POST['content'], $_POST['annotation'], $_SERVER['HTTP_REFERER'] ) )
	die( 'Missing parameters' );

$page = $_POST['page'];

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
while( false !== $pos = strpos( $current, '{ANNOTATION(', $pos + 1 ) )
{
	++$count;

	if( $_POST['annotation'] == $count )
	{
		$endparam = strpos( $current, ')}', $pos );
		if( false === $endparam )
			die( 'oops.' );
		$body = $endparam + 2;
		$endbody = strpos( $current, '{ANNOTATION}', $endparam );
		if( false === $endbody )
			die( 'oops.' );

		$before = substr( $current, 0, $body );
		$after = substr( $current, $endbody );

		$content = $before . "\n" . $content . $after;

		$tikilib->update_page( $page, $content, tra('Image annotations changed.'), $user, $_SERVER['REMOTE_ADDR'] );

		header( "Location: {$_SERVER['HTTP_REFERER']}" );
		exit;
	}
}

?>
