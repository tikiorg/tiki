<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Broker
{
	private $controllerMap;
	
	function __construct(array $controllerMap)
	{
		$this->controllerMap = $controllerMap;
	}

	function process($controller, $action, JitFilter $request)
	{
		$access = TikiLib::lib('access');

		try {
			$output = $this->attemptProcess($controller, $action, $request);

			if ($access->is_serializable_request()) {
				echo $access->output_serialized($output);
			} else {
				echo $this->render($controller, $action, $output);
			}
		} catch (Services_Exception $e) {
			$access->display_error('', $e->getMessage(), $e->getCode());
		}
	}

	private function attemptProcess($controller, $action, $request)
	{
		if (isset($this->controllerMap[$controller])) {
			$controllerClass = $this->controllerMap[$controller];
			$handler = new $controllerClass;
			$method = 'action_' . $action;

			if (method_exists($handler, $method)) {
				return $handler->$method($request);
			} else {
				throw new Services_Exception(tr('Controller not found (%0)', $controller), 404);
			}
		} else {
			throw new Services_Exception(tr('Action not found (%0 in %1)', $action, $controller), 404);
		}
	}

	private function render($controller, $action, $output)
	{
		$template = "$controller/$action.tpl";

		$smarty = TikiLib::lib('smarty');
		$access = TikiLib::lib('access');
		foreach ($output as $key => $value) {
			$smarty->assign($key, $value);
		}

		if ($access->is_xml_http_request()) {
			return $smarty->fetch($template);
		} else {
			$smarty->assign('mid', $template);
			return $smarty->fetch('tiki.tpl');
		}
	}
}

