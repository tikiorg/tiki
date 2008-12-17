<?php

if( ! isset( $_GET['keyword'] ) ) {
	$_GET['keyword'] = '';
}

if( isset($_GET['view']) ) {
	header( 'Location: tiki-index.php?page=' . rawurlencode($_GET['keyword']) );
	exit;
} elseif( isset($_GET['edit']) ) {
	header( 'Location: tiki-editpage.php?page=' . rawurlencode($_GET['keyword']) );
	exit;
} else {
	if( ! isset( $_GET['where'] ) ) {
		$_GET['where'] = 'pages';
	}

	header( 'Location: tiki-searchresults.php?highlight=' . rawurlencode($_GET['keyword']) . '&where=' . rawurlencode($_GET['where']) );
	exit;
}

?>
