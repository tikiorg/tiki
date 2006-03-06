<?php

/* 
 * issue: should we include tiki-setup.php here?
 * the problem is that tiki-ajax.php will be called with a frequence much
 * higher than other files, and for things that we don't need whole tiki environment
 * (we don't need $smarty for example), and I'm afraid we'll spend too much cpu and memory
 * with tiki-setup.php.
 *
 * I suggest that scripts in ajax/ dir should call tiki-setup.php if needed, but I'm not
 * sure of the consequences.
 *
 * batawata
 *
 */
include_once("lib/init/initlib.php");
require_once("tiki-setup_base.php");

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

    $metadata = array('function' => $function,
		      'type' => _getType($function));

    $result = _call($function, $arguments);

    if ($metadata['type'] == 'list' && isset($result['data'])) {
	if (isset($result['cant'])) {
	    $metadata['cant'] = $result['cant'];
	}
	$result = $result['data'];
    }

    call_user_func_array('_send_ajax_'.$metadata['type'], 
			 array('tikiResult',
			       $result));

    _send_ajax_item('metaData', $metadata);

    global $tikilib;
    //$tikilib->query('sasquatch');
}

function handleContent() {
    $arguments = func_get_args();
    
    $containerId = (string) array_shift($arguments);
    $function = (string) array_shift($arguments);

    if (!_loadFunction($function)) return false;

    $metadata = array('function' => $function,
		      'type' => 'scalar',
		      'containerId' => $containerId);

    $result = (string) _call($function, $arguments);

    call_user_func_array('_send_ajax_'.$metadata['type'], 
			 array('tikiResult',
			       $result));

    _send_ajax_item('metaData', $metadata);
}

function _loadFunction($function) {
    $filename = "ajax/".$function.".php";

    if (!file_exists($filename)) return false;

    $function = 'ajax_' . $function;

    require_once($filename);

    if (!function_exists($function) ||
	!function_exists($function.'_type')) { return false; }

    return true;
}

function _getType($function) {
    $valid_types = array('scalar','item','list');

    $result = call_user_func('ajax_' . $function . '_type');

    if (!in_array($result, $valid_types)) { return false; }

    return $result;
}

function _call($function, $arguments) {
    return call_user_func_array('ajax_' . $function, $arguments);
}

function _send_ajax_list($name, $list) {
    global $cp;

    for ($i=0; $i < sizeof($list); $i++) {
	$item = $list[$i];

	$ajaxObj =& $cp->add_node($name);

	foreach ($item as $field => $value) {
	    $r =& $ajaxObj->add_node($field);
	    $r->set_id($field . '_' . $i);
	    $r->set_data($value);
	}
    }
}

function _send_ajax_item($name, $item) {
    global $cp;

    _send_ajax_list($name, array($item));
}

function _send_ajax_scalar($name, $value) {
    global $cp;
    
    $ajaxObj =& $cp->add_node($name);
    $ajaxObj->set_data($value);
}

function _array_is_associative($ar) {
    return preg_match('/\D/',array_implode('',array_keys($ar)));
}

?>






