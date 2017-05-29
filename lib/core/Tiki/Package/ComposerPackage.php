<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package;

/**
 * Abstract class with most of the operations needed for a Composer Package
 */
abstract class ComposerPackage implements PackageInterface
{
	protected $packageType;

	protected $name;
	protected $requiredVersion;
	protected $licence;
	protected $licenceUrl;
	protected $requiredBy;
	protected $scripts;

	/**
	 * Sets the information related with this package, intended to be used in the constructor of the child class
	 *
	 * @param string $name
	 * @param string $requiredVersion
	 * @param string $licence
	 * @param string $licenceUrl
	 * @param array $requiredBy
	 * @param array $scripts
	 */
	protected function setPackageInfo($name, $requiredVersion, $licence, $licenceUrl, $requiredBy, $scripts = [])
	{

		$this->packageType = Type::COMPOSER;

		$this->name = $name;
		$this->requiredVersion = $requiredVersion;
		$this->licence = $licence;
		$this->licenceUrl = $licenceUrl;
		$this->requiredBy = $requiredBy;
		$this->scripts = $scripts;
	}

	/**
	 * Package Type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->packageType;
	}

	/**
	 * Return package information as Array
	 *
	 * @return array
	 */
	public function getAsArray()
	{
		return [
			'key' => $this->getKey(),
			'name' => $this->name,
			'requiredVersion' => $this->requiredVersion,
			'licence' => $this->licence,
			'licenceUrl' => $this->licenceUrl,
			'requiredBy' => $this->requiredBy,
		];
	}

	/**
	 * Return the key that represents this package
	 * that correspond to the class name without namespace
	 *
	 * @return string
	 */
	public function getKey()
	{
		$className = static::class;
		$pos = strrpos($className, '\\');
		if ($pos === false) {
			return $className;
		}

		return substr($className, $pos + 1);
	}

	/**
	 * Returns the script property
	 *
	 * @return array
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Returns the package name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the package version
	 *
	 * @return string
	 */
	public function getRequiredVersion()
	{
		return $this->requiredVersion;
	}

	/**
	 * Returns the package licence
	 *
	 * @return string
	 */
	public function getLicence()
	{
		return $this->licence;
	}

	/**
	 * Returns the link to the package url
	 *
	 * @return string
	 */
	public function getLicenceUrl()
	{
		return $this->licenceUrl;
	}

	/**
	 * Returns the list of features that requires this package
	 *
	 * @return array
	 */
	public function getRequiredBy()
	{
		return $this->requiredBy;
	}

}