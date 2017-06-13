<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Wrapper to composer.phar to allow installation of packages from the admin interface
 */
class ComposerCli
{

	const COMPOSER_PHAR = 'temp/composer.phar';
	const COMPOSER_CONFIG = 'composer.json';
	const COMPOSER_HOME = 'temp/composer';
	const PHP_COMMAND_NAMES = [
		'php',
		'php56',
		'php5.6',
		'php5.6-cli',
	];
	const PHP_MIN_VERSION = '5.6.0';

	/**
	 * @var string path to the base folder from tiki
	 */
	protected $basePath = '';

	/**
	 * @var string path to the folder that will be used
	 */
	protected $workingPath = '';

	/**
	 * @var string|null Will hold the php bin detected
	 */
	protected $phpCli = null;

	/**
	 * ComposerCli constructor.
	 * @param string $basePath
	 * @param string $workingPath
	 */
	public function __construct($basePath, $workingPath = null)
	{
		$basePath = realpath($basePath);
		if ($basePath) {
			$this->basePath = $basePath . '/';
		}

		if (is_null($workingPath)) {
			$this->workingPath = $this->basePath;
		} else {
			$workingPath = realpath($workingPath);
			if ($workingPath) {
				$this->workingPath = $workingPath . '/';
			}
		}
	}

	/**
	 * Returns the location of the composer.json file
	 * @return string
	 */
	protected function getComposerConfigFilePath()
	{
		return $this->workingPath . self::COMPOSER_CONFIG;
	}

	/**
	 * Return the composer.json parsed as array, false if the file can not be processed
	 * @return bool|array
	 */
	protected function getComposerConfig()
	{
		if (!$this->checkConfigExists()) {
			return false;
		}
		$content = json_decode(file_get_contents($this->getComposerConfigFilePath()), true);

		return $content;
	}

	/**
	 * Return the composer.json parsed as array, or a default version for the composer.json if do not exists
	 * First try to load the dist version, if not use a hardcoded version with the minimal setup
	 * @return array|bool
	 */
	protected function getComposerConfigOrDefault()
	{
		$content = $this->getComposerConfig();
		if ($content !== false) {
			return $content;
		}

		$distFile = $this->workingPath . self::COMPOSER_CONFIG . '.dist';
		if (!file_exists($distFile)) {
			$content = json_decode(file_get_contents($distFile), true);
			if ($content !== false) {
				return $content;
			}
		}

		return json_decode('{"minimum-stability": "stable","config": {"process-timeout": 5000,"bin-dir": "bin"}}', true);
	}

	/**
	 * Return the location of the composer.phar file (in the temp folder, as downloaded by setup.sh)
	 * @return string
	 */
	public function getComposerPharPath()
	{
		return $this->basePath . self::COMPOSER_PHAR;
	}

	/**
	 * Check the version of the command line version of PHP
	 *
	 * @param $php
	 * @return string
	 */
	protected function getPhpVersion($php)
	{
		$builder = new ProcessBuilder();
		$builder->setPrefix($php);
		$builder->setArguments(['--version']);
		$process = $builder->getProcess();
		$process->run();
		foreach (explode("\n", $process->getOutput()) as $line) {
			$parts = explode(' ', $line);
			if ($parts[0] === 'PHP') {
				return $parts[1];
			}
		}

		return '';
	}

	/**
	 * Atempts to resolve the location of the PHP binary
	 *
	 * @return null|bool|string
	 */
	protected function getPhpPath()
	{
		if (!is_null($this->phpCli)) {
			return $this->phpCli;
		}

		$this->phpCli = false;
		foreach (explode(':', $_SERVER['PATH']) as $path) {
			foreach (self::PHP_COMMAND_NAMES as $cli) {
				$possibleCli = $path . DIRECTORY_SEPARATOR . $cli;
				if (file_exists($possibleCli) && is_executable($possibleCli)) {
					$version = $this->getPhpVersion($possibleCli);
					if (version_compare($version, self::PHP_MIN_VERSION, '<')) {
						continue;
					}
					$this->phpCli = $possibleCli;

					return $this->phpCli;
				}
			}
		}

		return $this->phpCli;
	}

	/**
	 * Evaluates if composer can be executed
	 *
	 * @return bool
	 */
	public function canExecuteComposer()
	{
		static $canExecute = null;
		if (!is_null($canExecute)) {
			return $canExecute;
		}

		$canExecute = false;

		if (file_exists($this->getComposerPharPath())) {
			list($output) = $this->execComposer(['--no-ansi', '--version']);
			if (strncmp($output, 'Composer', 8) == 0) {
				$canExecute = true;
			}
		}

		return $canExecute;
	}

	/**
	 * Execute Composer
	 *
	 * @param $args
	 * @return array
	 */
	protected function execComposer($args)
	{
		if (!is_array($args)) {
			$args = array($args);
		}

		$builder = new ProcessBuilder();

		$cmd = $this->getPhpPath();
		if ($cmd) {
			$builder->setPrefix($cmd);
			array_unshift($args, $this->getComposerPharPath());
		} else {
			$builder->setPrefix($this->getComposerPharPath());
		}

		$builder->setArguments($args);

		if (!getenv('HOME') && !getenv('COMPOSER_HOME')){
			$builder->setEnv('COMPOSER_HOME', $this->basePath . self::COMPOSER_HOME);
		}

		$process = $builder->getProcess();

		$process->run();

		$code = $process->getExitCode();

		$output = $process->getOutput();
		$errors = $process->getErrorOutput();

		return [$output, $errors, $code];
	}

	/**
	 * Execute show command
	 *
	 * @return array
	 */
	protected function execShow()
	{
		if (!$this->canExecuteComposer()) {
			return [];
		}
		list($result) = $this->execComposer(['--format=json', 'show', '-d', $this->workingPath]);
		$json = json_decode($result, true);

		return $json;
	}


	/**
	 * Check if the composer.json file exists
	 *
	 * @return bool
	 */
	public function checkConfigExists()
	{
		return file_exists($this->getComposerConfigFilePath());
	}

	/**
	 * Retrieve list of packages in composer.json
	 *
	 * @return array|bool
	 */
	public function getListOfPackagesFromConfig()
	{
		if (!$this->checkConfigExists() || !$this->canExecuteComposer()) {
			return false;
		}

		$content = json_decode(file_get_contents($this->getComposerConfigFilePath()), true);
		$composerShow = $this->execShow();

		$installedPackages = [];
		if (isset($composerShow['installed']) && is_array($composerShow['installed'])){
			foreach ($composerShow['installed'] as $package) {
				$installedPackages[$package['name']] = $package;
			}
		}

		$result = [];
		if (isset($content['require']) && is_array($content['require'])) {
			foreach ($content['require'] as $name => $version) {
				if (isset($installedPackages[$name])) {
					$result[] = [
						'name' => $name,
						'status' => ComposerManager::STATUS_INSTALLED,
						'required' => $version,
						'installed' => $installedPackages[$name]['version'],
					];
				} else {
					$result[] = [
						'name' => $name,
						'status' => ComposerManager::STATUS_MISSING,
						'required' => $version,
						'installed' => '',
					];
				}
			}
		}

		return $result;
	}

	/**
	 * Ensure packages configured in composer.json are installed
	 *
	 * @return bool
	 */
	public function installMissingPackages()
	{
		if (!$this->checkConfigExists() || !$this->canExecuteComposer()) {
			return false;
		}

		list($output, $errors) = $this->execComposer(
			['--no-ansi', '--no-dev', '--prefer-dist', 'update', '-d', $this->workingPath, 'nothing']
		);

		return $output . "\n" .$errors;
	}

	/**
	 * Execute the diagnostic command
	 *
	 * @return array
	 */
	public function execDiagnose()
	{
		if (!$this->canExecuteComposer()) {
			return false;
		}

		list($output, $errors) = $this->execComposer(['--no-ansi', 'diagnose', '-d', $this->workingPath]);

		return $output . "\n" .$errors;
	}

	/**
	 * Install a package (from the package definition)
	 *
	 * @param ComposerPackage $package
	 * @return bool|string
	 */
	public function installPackage(ComposerPackage $package)
	{
		if (!$this->canExecuteComposer()) {
			return false;
		}

		$composerJson = $this->getComposerConfigOrDefault();
		$composerJson = $this->addComposerPackageToJson(
			$composerJson,
			$package->getName(),
			$package->getRequiredVersion(),
			$package->getScripts()
		);
		$fileContent = json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		file_put_contents($this->getComposerConfigFilePath(), $fileContent);

		$commandOutput = $this->installMissingPackages();

		return tr('= New composer.json file content') . ":\n\n"
		. $fileContent . "\n\n"
		. tr('= Composer execution output') . ":\n\n"
		. $commandOutput;
	}

	/**
	 * Remove a package (from the package definition)
	 *
	 * @param ComposerPackage $package
	 * @return bool|string
	 */
	public function removePackage(ComposerPackage $package)
	{
		if (!$this->canExecuteComposer() || !$this->checkConfigExists()) {
			return false;
		}

		list($commandOutput, $errors) = $this->execComposer(
			['remove', $package->getName(), '--update-no-dev', '-d', $this->workingPath, '--no-ansi', '--no-interaction']
		);

		$fileContent = file_get_contents($this->getComposerConfigFilePath());

		return tr('= New composer.json file content') . ":\n\n"
		. $fileContent . "\n\n"
		. tr('= Composer execution output') . ":\n\n"
		. $commandOutput . "\n" . $errors;
	}


	/**
	 * Append a package to composer.json
	 *
	 * @param $composerJson
	 * @param $package
	 * @param $version
	 * @param array $scripts
	 * @return array
	 */
	public function addComposerPackageToJson($composerJson, $package, $version, $scripts = [])
	{

		$scriptsKeys = [
			'pre-install-cmd',
			'post-install-cmd',
			'pre-update-cmd',
			'post-update-cmd',
		];

		if (!is_array($composerJson)) {
			$composerJson = [];
		}
		// require
		if (!isset($composerJson['require'])) {
			$composerJson['require'] = [];
		}
		if (!isset($composerJson['require'][$package])) {
			$composerJson['require'][$package] = $version;
		}

		// scripts
		if (is_array($scripts) && count($scripts)) {
			if (!isset($composerJson['scripts'])) {
				$composerJson['scripts'] = [];
			}
			foreach ($scriptsKeys as $type) {
				if (!isset($scripts[$type])) {
					continue;
				}
				$scriptList = $scripts[$type];
				if (is_string($scriptList)) {
					$scriptList = [$scriptList];
				}
				if (!count($scriptList)) {
					continue;
				}
				if (!isset($composerJson['scripts'][$type])) {
					$composerJson['scripts'][$type] = [];
				}
				foreach ($scriptList as $scriptString) {
					$composerJson['scripts'][$type][] = $scriptString;
				}
				$composerJson['scripts'][$type] = array_unique($composerJson['scripts'][$type]);
			}
		}

		return $composerJson;
	}
}