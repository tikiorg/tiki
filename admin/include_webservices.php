<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
require_once 'lib/ointegratelib.php';
require_once 'lib/webservicelib.php';

//this script may only be included - so its better to die if called directly.
if (basename($_SERVER['SCRIPT_NAME']) == basename(__FILE__)) {
	header('Location: index.php');
	exit;
}

if (isset($_REQUEST['name']) && $webservice = Tiki_Webservice::getService($_REQUEST['name'])) {
	if (isset($_REQUEST['delete']) && empty($_REQUEST['delete'])) {
		$webservice->delete();
		$webservice = new Tiki_Webservice;
		$url = '';
		$storedTemplates = array();
	} else {
		$url = $webservice->url;
		$storedTemplates = $webservice->getTemplates();
	}
} else {
	$url = '';
	$body = '';
	$wstype = '';
	$operation = '';
	if (isset($_REQUEST['url'])) {
		$url = $_REQUEST['url'];
	}
	if (isset($_REQUEST['wstype'])) {
		$wstype = $_REQUEST['wstype'];
	}
	if (isset($_REQUEST['operation'])) {
		$operation = $_REQUEST['operation'];
	}
	if (isset($_REQUEST['postbody'])) {
		$body = $_REQUEST['postbody'];
	}
	$webservice = new Tiki_Webservice;
	$webservice->url = $url;
	$webservice->wstype = $wstype;
	$webservice->body = $body;
	$webservice->operation = $operation;
	$storedTemplates = array();
}

if (!isset($_REQUEST['params'])) {
	$_REQUEST['params'] = array();
}

if (!isset($_REQUEST['parse']) && $response = $webservice->performRequest($_REQUEST['params'])) {
	$data = $response->data;
	if (is_array($data)) {
		unset($data['_template']);
		unset($data['_version']);
	}
	$templates = $response->getTemplates(
		array(
			'smarty/tikiwiki',
			'smarty/html',
			'javascript/html',
		)
	);

	$smarty->assign('data', print_r($data, true));
	$smarty->assign('templates', $templates);
	$smarty->assign('response', $response);
	if (isset($_REQUEST['delete']) && $webservice->getTemplate($_REQUEST['delete'])) {
		$webservice->removeTemplate($_REQUEST['delete']);
		unset($storedTemplates[$_REQUEST['delete']]);
	}

	// Load template data in the form for modification
	if (isset($_REQUEST['loadtemplate'])) {
		$template = $webservice->getTemplate($_REQUEST['loadtemplate']);
		$smarty->assign('nt_name', $template->name);
		$smarty->assign('nt_engine', $template->engine);
		$smarty->assign('nt_output', $template->output);
		$smarty->assign('nt_content', $template->content);
	}

	if (isset($_REQUEST['add'])) {
		$pos = key($_REQUEST['add']);
		if (isset($templates[$pos])) {
			$template = $templates[$pos];
			$smarty->assign('nt_engine', $template['engine']);
			$smarty->assign('nt_output', $template['output']);
			$smarty->assign('nt_content', $template['content']);
		}
	}

	// Create new registered service
	if (isset($_REQUEST['new_name'])) {
		$name = $_REQUEST['new_name'];
		if (!empty($name) && !Tiki_Webservice::getService($name)) {
			if ($service = Tiki_Webservice::create($name)) {
				$service->url = $url;
				$service->wstype = $wstype;
				$service->body = $body;
				$service->operation = $operation;
				$service->schemaDocumentation = $response->schemaDocumentation;
				$service->schemaVersion = $response->schemaVersion;
				$service->save();
				$webservice = $service;
			} else {
				TikiLib::lib('errorreport')->report(tr('Webservice error "%0" not saved (alpha characters only)', $name));
				$webservice = new Tiki_Webservice;
				$webservice->url = $url;
				$webservice->wstype = $wstype;
				$webservice->body = $body;
				$webservice->operation = $operation;
				$storedTemplates = array();
			}
		}
	}

	// Save template modification
	if (isset($_REQUEST['nt_name'])) {
		$name = $_REQUEST['nt_name'];
		if (($template = $webservice->getTemplate($name)) || ($template = $webservice->addTemplate($name))) {
			$template->engine = $_REQUEST['nt_engine'];
			$template->output = $_REQUEST['nt_output'];
			$template->content = $_REQUEST['nt_content'];
			$template->save();
			$storedTemplates = $webservice->getTemplates();
		}
	}

	if (isset($_REQUEST['preview']) && $template = $webservice->getTemplate($_REQUEST['preview'])) {
		$output = $template->render($response, 'html');
		$smarty->assign('preview', $_REQUEST['preview']);
		$smarty->assign('preview_output', $output);
	}
}

$headerlib->add_jsfile('lib/soap/tiki-admin_webservices.js');

$smarty->assign('webservicesTypes', Tiki_Webservice::getTypes());
$smarty->assign('webservices', Tiki_Webservice::getList());
$smarty->assign('storedName', $webservice->getName());
$smarty->assign('storedTemplates', $storedTemplates);
$smarty->assign('url', $webservice->url);
$smarty->assign('postbody', $webservice->body);
$smarty->assign('operation', $webservice->operation);
$smarty->assign('wstype', $webservice->wstype);
$smarty->assign('params', $webservice->getParameterMap($_REQUEST['params']));
