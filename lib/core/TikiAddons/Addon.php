<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Addon
{

	private $libraries = array();
	private $configuration = null;
	public $smarty = null;
	private $utilities;

	function __construct($folder)
	{
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		$prefname = 'ta_' . $folder . '_on';
			if (!isset($GLOBALS['prefs'][$prefname]) || $GLOBALS['prefs'][$prefname] != 'y') {
			throw new Exception(tra('Addon is not activated: ') . $folder);
		}
		$file = TIKI_PATH . "/addons/$folder/tikiaddon.json";
		$this->configuration = json_decode(file_get_contents($file));
		$this->utilities = new TikiAddons_Utilities;
		$this->utilities->checkDependencies($this->getFolder());
		$this->utilities->checkProfilesInstalled($this->getFolder(), $this->getVersion());
		if ($this->configuration->smarty) {
			$this->smarty = new Smarty_Tiki;
			$this->smarty->assign('prefs', $GLOBALS['prefs']);
			$this->smarty->assign('user', $GLOBALS['user']);
			$this->smarty->assign('tikiaddon_package', $this->configuration->package);
		}
	}

	function getName()
	{
		return $this->configuration->name;
	}

	function getPackage()
	{
		return $this->configuration->package;
	}

	function getVersion()
	{
		return $this->configuration->version;
	}

	function getURL()
	{
		return $this->configuration->url;
	}

	function getFolder()
	{
		return str_replace('/', '_', $this->configuration->package);
	}

	function getVendor()
	{
		$parts = explode('/', $this->getPackage());
		return $parts[0];
	}

	function getShortName()
	{
		$parts = explode('/', $this->getPackage());
		return $parts[1];
	}

	function getDepends() {
		if (is_array($this->configuration->depends)) {
			return $this->configuration->depends;
		} else {
			return array();
		}
	}

	function lib($name)
	{
		if (isset($this->libraries[$name])) {
			return $this->libraries[$name];
		}

		$container = TikiInit::getContainer();
		$service = 'tikiaddon.' . $this->getVendor() . '.' . $this->getShortName() . '.' . $name;

		if ($lib = $container->get($service, \Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE)) {
			return $lib;
		}

		unlink(TIKI_PATH . '/temp/cache/container.php'); // Remove the container cache to help transition
		throw new Exception(tr("%0 library not found. This may be due to a typo or caused by a recent update.", $name));
	}
}