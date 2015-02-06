<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ServiceLib
{
	private $broker;
	private $addonbrokers = array();

	function getBroker($addonpackage = '')
	{
		if ($addonpackage) {
			$utilities = new TikiAddons_Utilities;
			if (!$utilities->isInstalled(str_replace('.', '/', $addonpackage))) {
				$addonpackage = '';
			}
		}

		if ($addonpackage && !isset($this->addonbrokers[$addonpackage])) {
			$this->addonbrokers[$addonpackage] = new Services_Broker(TikiInit::getContainer(), $addonpackage);
		} else if (! $this->broker) {
			$this->broker = new Services_Broker(TikiInit::getContainer());
		}

		if ($addonpackage) {
			return $this->addonbrokers[$addonpackage];
		} else {
			return $this->broker;
		}
	}

	function internal($controller, $action, $request = array(), $addonpackage = '')
	{
		return $this->getBroker($addonpackage)->internal($controller, $action, $request);
	}

	function render($controller, $action, $request = array(), $addonpackage = '')
	{
		return $this->getBroker($addonpackage)->internalRender($controller, $action, $request);
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

