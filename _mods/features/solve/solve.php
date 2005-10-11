<?php
/**
 * @version $Id: solve.php,v 1.5 2005-10-11 13:10:44 michael_davey Exp $
 * @package TikiWiki
 * @subpackage Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

// Initialization
require_once ('tiki-setup.php');
require_once( 'lib/db/tikitable.php' );

require_once( 'lib/solve/solvelib.php' );
require_once( 'lib/solve/configuration.php' );
require_once( 'lib/solve/presentation.php' );

/** which section are we in? */
$option = solve_get_param($_REQUEST, 'option');

/** SolveLib - a helper class */
$sh = new SolveLib( $dbTiki, $option, '.' );

if ($option == '' ) {
	require_once('tiki-login_scr.php');
	exit;
	die;
}

$crumbs[] = new Breadcrumb("Solve $option",
            '$info["description"]',
            "solve/$option",
            '',
            '');

$headtitle = breadcrumb_buildHeadTitle($crumbs);
$smarty->assign_by_ref('headtitle', $headtitle);
$smarty->assign('trail', $crumbs);

// gets template for page
$cur_template = "styles/napkin";

        if ($path = $sh->_getPath( $option, '.' )) {
            $access->check_page($user, 'feature_crm');
            require_once( $path );
        } else {
            header ("Status: 402 Found"); /* PHP3 */
            header ("HTTP/1.0 402 Found"); /* PHP4 */
            header("Location: $base_url/tiki-index.php?page=$option");
            die('found');
            exit;
        }

$smarty->assign('show_page', 'n');
// ask_ticket('solve');

//add a hit
// $statslib->stats_hit($page,"solve/$option");

// Display the Solve Template
$smarty->assign('print_page','y');
$smarty->assign('show_page_bar','n');

// xdebug_dump_function_profile(XDEBUG_PROFILER_CPU);
// debug: print all objects

if (! headers_sent() ) {
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' );
}

$smarty->display("tiki.tpl");
echo "<!-- ".time()." -->";

?>
