<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package;

use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Allows the management of Composer Packages
 */
class ComposerManager
{

	const STATUS_INSTALLED = 'installed';
	const STATUS_MISSING = 'missing';

	/**
	 * @var string the path where the composer file is located
	 */
	protected $basePath = '';
	/**
	 * @var string the base namespace for the package definition files
	 */
	protected $packagesNamespace;

	/**
	 * @var ComposerCli wrapper for composer phar
	 */
	protected $composerWrapper;

	/**
	 * Setups the composer.json location
	 *
	 * @param string $basePath
	 * @param string $workingPath
	 * @param ComposerCli $composerWrapper composer.phar wrapper, optional in the constructor to allow injection for test
	 */
	function __construct($basePath, $workingPath = null, $composerWrapper = null)
	{
		$this->packagesNamespace = __NAMESPACE__ . '\\External\\';
		$this->basePath = $basePath;
		if (is_null($composerWrapper)){
			$composerWrapper = new ComposerCli($basePath, $workingPath);
		}
		$this->composerWrapper = $composerWrapper;
	}

	/**
	 * Return the Composer Wrapper
	 * @return ComposerCli
	 */
	public function getComposer()
	{
		return $this->composerWrapper;
	}

	/**
	 * Check if composer is available
	 * @return bool
	 */
	public function composerIsAvailable()
	{
		return $this->composerWrapper->canExecuteComposer();
	}

	/**
	 * Check if composer is available
	 * @return bool
	 */
	public function composerPath()
	{
		return $this->composerWrapper->getComposerPharPath();
	}

	/**
	 * Get list of packages installed
	 * @return array
	 */
	public function getInstalled()
	{
		$installedPackages = $this->composerWrapper->getListOfPackagesFromConfig();

		$packageDefinitions = $this->getAvailable(false);
		$keyLookup = [];
		foreach($packageDefinitions as $package){
			$keyLookup[$package['name']] = $package['key'];
		}
		foreach($installedPackages as &$package){
			if (isset($keyLookup[$package['name']])){
				$package['key'] = $keyLookup[$package['name']];
			} else {
				$package['key'] = '';
			}
		}

		return $installedPackages;
	}

	/**
	 * Install missing packages (according to composer.json)
	 * @return bool
	 */
	public function fixMissing()
	{
		return $this->composerWrapper->installMissingPackages();
	}

	/**
	 * Get List of available (defined) packages
	 *
	 * @param bool $filterInstalled don't return if the package is already installed
	 * @return array
	 */
	public function getAvailable($filterInstalled = true)
	{
		$packagesDir = __DIR__ . DIRECTORY_SEPARATOR . 'External';
		if (!is_dir($packagesDir)) {
			return [];
		}

		$installedPackages = [];
		if ($filterInstalled){
			$installedPackages = $this->getListOfInstalledPackages($filterInstalled);
		}

		$availablePackages = [];
		foreach (new \GlobIterator($packagesDir . DIRECTORY_SEPARATOR . '*.php') as $fileInfo) {
			$class = $fileInfo->getBasename('.php');
			$fullClassName = $this->packagesNamespace . $class;
			if (class_exists($fullClassName)) {
				try {
					/** @var ComposerPackage $externalPackage */
					$externalPackage = new $fullClassName;
					if ($externalPackage->getType() != Type::COMPOSER) {
						continue;
					}
					if ($filterInstalled && array_key_exists($externalPackage->getName(), $installedPackages)) {
						continue;
					}
					$availablePackages[] = $externalPackage->getAsArray();
				} catch (Exception $e) {
					//ignore
				}
			}
		}

		return $availablePackages;
	}

	/**
	 * return the list of packages installed
	 *
	 * @param $filterInstalled
	 * @return array
	 */
	protected function getListOfInstalledPackages($filterInstalled)
	{
		$installedPackages = [];
		if ($filterInstalled) {
			$installed = $this->getInstalled();
			if ($installed !== false){
				foreach ( $installed as $pkg) {
					if ($pkg['status'] == self::STATUS_INSTALLED) {
						$installedPackages[$pkg['name']] = $pkg['name'];
					}
				}
			}
		}

		return $installedPackages;
	}

	/**
	 * Assure that only allowed chars are present in the package key name
	 * @param $packageKey
	 * @return mixed
	 */
	protected function sanitizePackageKey($packageKey)
	{
		return preg_replace("/[^a-zA-Z0-9]+/", "", $packageKey);
	}
	/**
	 * Try to install a packages by the package key (corresponding to the class name)
	 *
	 * @param $packageKey
	 * @return bool|string
	 */
	public function installPackage($packageKey)
	{
		$packageKey = $this->sanitizePackageKey($packageKey);
		$packageClass = $this->packagesNamespace . $packageKey;
		try {
			if (class_exists($packageClass)) {
				/** @var ComposerPackage $externalPackage */
				$externalPackage = new $packageClass;

				return $this->composerWrapper->installPackage($externalPackage);
			}
		} catch (Exception $e) {
			//ignore
		}
	}

	/**
	 * Try to remove a packages by the package key (corresponding to the class name)
	 *
	 * @param $packageKey
	 * @return bool|string
	 */
	public function removePackage($packageKey)
	{
		$packageKey = $this->sanitizePackageKey($packageKey);
		$packageClass = $this->packagesNamespace . $packageKey;
		try {
			if (class_exists($packageClass)) {
				/** @var ComposerPackage $externalPackage */
				$externalPackage = new $packageClass;

				return $this->composerWrapper->removePackage($externalPackage);
			}
		} catch (Exception $e) {
			//ignore
		}
	}
}