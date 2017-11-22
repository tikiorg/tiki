<?php

// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');

$access = TikiLib::lib('access');
$access->checkAuthenticity();
$access->check_feature('feature_sefurl_routes');
$access->check_permission(['tiki_p_admin_routes']);

$auto_query_args = [];
$cookietab = 1;

$routeLib = TikiLib::lib('custom_route');
$controller = new Tiki\CustomRoute\Controller();
if ((isset($_POST['new_route']) || (isset($_POST['editroute']) && isset($_POST['route'])) && empty($_POST['load_options']))
	&& $access->ticketMatch()) {
	// If route saved, it redirects to the routes page, cleaning the add/edit route form.
	$route = $controller->saveRequest($_POST);
	$cookietab = 2;
} elseif (isset($_REQUEST['route']) && $_REQUEST['route'] && empty($_POST['load_options'])) {
	$item = Tiki\CustomRoute\Item::load($_REQUEST['route']);
	$route = $item->toArray();
	$cookietab = '2';
} else {
	$item = $controller->populateFromRequest($_POST);
	$route = $item->toArray();

	if (! isset($_POST['load_options']) && isset($_REQUEST['route'])) {
		unset($route['id']);
		$_REQUEST['route'] = 0;
	}
}

$routes = $routeLib->getRoute();
$smarty->assign_by_ref('routes', $routes);

if (isset($_REQUEST['add']) || ! empty($_REQUEST['router_type'])) {
	$cookietab = '2';
}

$smarty->assign('route', $route);
$smarty->assign('routeId', $_REQUEST['route']);
$smarty->assign(
	'routerTypes',
	[
		'Direct' => 'Redirect to another URL',
		'Object' => 'Redirect to tiki object',
		'TrackerField' => 'To tracker item by field value',
	]
);


// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_routes.tpl');
$smarty->display('tiki.tpl');
