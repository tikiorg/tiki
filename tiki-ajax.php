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

$cp = new cpaint();

$cp->register('handle');
$cp->register('handleContent');

$cp->start();
$cp->return_data();

function handle() {

    $arguments = func_get_args();
    
    $function = (string) array_shift($arguments);

    if (!_loadFunction($function)) return false;

    $metadata = array('function' => $function);

    $result = _call($function, $arguments);

    if (is_array($result) && isset($result['data']) && isset($result['cant']) && sizeof($result) == 2) {
	$metadata['cant'] = $result['cant'];
	$result = $result['data'];
    }

    _send('tikiResult', $result);
    _send('metaData',   $metadata);
}

function handleContent() {
    $arguments = func_get_args();
    
    $containerId = (string) array_shift($arguments);
    $function = (string) array_shift($arguments);

    if (!_loadFunction($function)) return false;

    $metadata = array('function' => $function,
		      'containerId' => $containerId);

    $result = (string) _call($function, $arguments);

    _send('tikiResult', $result);
    _send('metaData',   $metadata);
}

function _loadFunction($function) {
    $filename = "ajax/".$function.".php";

    if (!file_exists($filename)) return false;

    $function = 'ajax_' . $function;

    require_once($filename);

    if (!function_exists($function)) { return false; }

    return true;
}

function _call($function, $arguments) {
    return call_user_func_array('ajax_' . $function, $arguments);
}

function _send($name, $value) {
    global $cp;
    $node =& $cp->add_node($name);
    _send_item($node, $value);
}

function _send_item(&$node, $var) {

    if (!is_array($var) && !is_object($var)) {

	$node->set_data($var);
	$node->set_attribute('type','scalar');

    } elseif (is_array($var)) {

	$node->set_attribute('type','array');

	foreach ($var as $key => $value) {
	    $subNode =& $node->add_node('item');
	    $subNode->set_attribute('key',$key);
	    _send_item($subNode, $value);		
	}
	
    } elseif (is_object($var)) {
	// not supported yet
    }
}

?>






