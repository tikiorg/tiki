<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
// Live Support CHAT server.
// This PHP script handles all the messaging between the clients
// and the server (this script). Messaging is done using a REST
// model based on HTTP GET requests.
// Clients use Javascript to send messages to this server.
//   clients of this server are:
//   * The operator console
//   * The operator chat window
//   * The client chat window
// Long includes and heavy operations should be avoided to maximize the
// response time of this script which is critical.

include "tiki-setup.php";
header('Content-Type: text/xml');
if ($prefs['feature_live_support'] != 'y') {
	die;
}
include_once ('lib/live_support/lslib.php');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
// A user requests a chat
if (isset($_REQUEST['request_chat'])) {
	// The server receives a chat request
	// if then inserts a row in the support_requests table
	// with the name of the user (or anonymous) and the reason for
	// the request.
	// Operator windows polling this server will be notified of current
	// 'active' chat requests
	header("Content-type: text/plain");
	$id = $lslib->new_user_request($_REQUEST['user'], $_REQUEST['tiki_user'], $_REQUEST['email'], $_REQUEST['reason']);
	print ($id);
}
if (isset($_REQUEST['set_operator_status'])) {
	$lslib->set_operator_status($_REQUEST['set_operator_status'], $_REQUEST['status']);
}
if (isset($_REQUEST['operators_online'])) {
	if ($lslib->operators_online()) {
		header("Content-type: image/png");
		readfile('img/icons/live-support-on.png');
	} else {
		header("Content-type: image/png");
		readfile('img/icons/live-support-off.png');
	}
}
if (isset($_REQUEST['write'])) {
	if ($_REQUEST['role'] == 'operator') {
		$color = 'blue';
	}
	if ($_REQUEST['role'] == 'user') {
		$color = 'black';
	}
	if ($_REQUEST['role'] == 'observer') {
		$color = 'grey';
	}
	if (!strstr($_REQUEST['msg'], 'has left')) {
		$_REQUEST['msg'] = '<span style="color:' . $color . ';">(' . $_REQUEST['name'] . ')' . ' ' . $_REQUEST['msg'] . '</span>';
	}
	$lslib->put_message($_REQUEST['write'], $_REQUEST['msg'], $_REQUEST['senderId']);
}
if (isset($_REQUEST['get_last_event'])) {
	header("Content-type: text/plain");
	echo $lslib->get_last_event($_REQUEST['get_last_event'], $_REQUEST['senderId']);
}
if (isset($_REQUEST['get_event'])) {
	header("Content-type: text/plain");
	echo $lslib->get_event($_REQUEST['get_event'], $_REQUEST['last'], $_REQUEST['senderId']);
}
// A client closes its window
if (isset($_REQUEST['client_close'])) {
	$lslib->user_close_request($_REQUEST['client_close']);
}
// An operator closes its window
if (isset($_REQUEST['operator_close'])) {
	$lslib->operator_close_request($_REQUEST['operator_close']);
}
// A client polls for a connection
if (isset($_REQUEST['get_status'])) {
	header("Content-type: text/plain");
	echo $lslib->get_request_status($_REQUEST['get_status']);
}
// Operator console gets requests
if (isset($_REQUEST['poll_requests'])) {
	// Now we can format this as XML
	header("Content-type: text/plain");
	echo $lslib->get_last_request();
}
