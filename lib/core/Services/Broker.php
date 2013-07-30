<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
			$this->preExecute();

			$output = $this->attemptProcess($controller, $action, $request);

			if (isset($output['FORWARD'])) {
				$output['FORWARD'] = array_merge(
					array(
						'controller' => $controller,
						'action' => $action,
					),
					$output['FORWARD']
				);
			}

			if ($access->is_serializable_request()) {
				echo $access->output_serialized($output);
			} else {
				echo $this->render($controller, $action, $output);
			}
		} catch (Services_Exception $e) {
			$access->display_error(NULL, $e->getMessage(), $e->getCode());
		}
	}

	function internal($controller, $action, $request = array())
	{
		if (! $request instanceof JitFilter) {
			$request = new JitFilter($request);
		}

		return $this->attemptProcess($controller, $action, $request);
	}

	private function attemptProcess($controller, $action, $request)
	{
		if (isset($this->controllerMap[$controller])) {
			$controllerClass = $this->controllerMap[$controller];
			$handler = new $controllerClass;
			$method = 'action_' . $action;

			if (method_exists($handler, $method)) {
				if (method_exists($handler, 'setUp')) {
					$handler->setUp();
				}

				return $handler->$method($request);
			} else {
				throw new Services_Exception(tr('Action not found (%0 in %1)', $action, $controller), 404);
			}
		} else {
			throw new Services_Exception(tr('Controller not found (%0)', $controller), 404);
		}
	}

	private function preExecute()
	{
		$access = TikiLib::lib('access');

		if ($access->is_xml_http_request() && ! $access->is_serializable_request()) {
			$headerlib = TikiLib::lib('header');
			$headerlib->clear_js(); // Only need the partials
		}
	}

	private function render($controller, $action, $output)
	{
		if (isset($output['FORWARD'])) {
			$loc = $_SERVER['PHP_SELF'];
			$arguments = $output['FORWARD'];
			header("Location: $loc?" . http_build_query($arguments, '', '&'));
			exit;
		}

		$smarty = TikiLib::lib('smarty');

		$template = "$controller/$action.tpl";

		//if template doesn't exists, simply return the array given from the action
		if ($smarty->templateExists($template) == false) return json_encode($output);

		$access = TikiLib::lib('access');
		foreach ($output as $key => $value) {
			$smarty->assign($key, $value);
		}

		if ($access->is_xml_http_request()) {
			$headerlib = TikiLib::lib('header');
			$content = $smarty->fetch($template);
			$content .= $headerlib->output_js();

			return $content;
		} else {
			$smarty->assign('mid', $template);
			return $smarty->fetch('tiki.tpl');
		}
	}
}

