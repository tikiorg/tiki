<?php

/* 
 * issue: should we include tiki-setup.php here?
 * the problem is that tiki-ajax.php will be called with a frequence much
 * higher than other files, and for things that we don't need whole tiki environment
 * (we don't need $smarty for example), and I'm afraid we'll spend too much cpu and memory
 * with tiki-setup.php.
 *
 * batawata
 *
 */
require_once("tiki-setup.php");

require_once('lib/cpaint/cpaint2.inc.php');
require_once('lib/cpaint/ajaxlib.php');

$ajax = new TikiAjax();

$ajax->start();
$ajax->return_data();


?>






