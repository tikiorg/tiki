<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ServiceLib
{
	private $broker;

	function getBroker()
	{
		if (! $this->broker) {
			$this->broker = new Services_Broker(TikiInit::getContainer());
		}

		return $this->broker;
	}

	function internal($controller, $action, $request = array())
	{
		return $this->getBroker()->internal($controller, $action, $request);
	}

	function render($controller, $action, $request = array())
	{
		return $this->getBroker()->internalRender($controller, $action, $request);
	}

	function getUrl($params)
	{
		global $prefs;

		if ($prefs['feature_sefurl'] == 'y') {
			$url = "tiki-{$params['controller']}";

			if (isset($params['action'])) {
				$url .= "-{$params['action']}";
			} else {
				$url .= "-x";
			}

			unset($params['controller']);
			unset($params['action']);
		} else {
			$url = 'tiki-ajax_services.php';
		}

		if (count($params)) {
			$url .= '?' . http_build_query($params, '', '&');
		}

		return TikiLib::tikiUrlOpt($url);
	}
}

