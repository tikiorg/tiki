<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/init.scripts/00-include_path.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Init PHP include path
 */

$separator = '';

$current_path = ini_get('include_path');

if (strstr($current_path, ';')) $separator = ';';
else $separator = ':';

// Get TikiWiki root dir
ini_set('include_path', $tiki_root_dir . '/lib/pear/' . $separator . $current_path);

if ($api_tiki == 'adodb')
    ini_set('include_path', ini_get('include_path') . $separator . $tiki_root_dir . '/lib/adodb/');

?>
