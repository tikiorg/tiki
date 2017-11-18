<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class IDS_log extends \Expose\Log
{

	protected $logger = null;

	protected $resource = null;

	/**
	 * Init the object and connect if string is given
	 *
	 * @param String $filepath The file to log
	 */
	public function __construct($filePath)
	{
		$logger = new \Monolog\Logger('IDS');
		$logger->pushHandler(new Monolog\Handler\StreamHandler($filePath, \Monolog\Logger::INFO));
		$this->setLogger($logger);
	}

	/**
	 * Set the logger object instance
	 *
	 * @param object $logger Logger instance
	 */
	public function setLogger($logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Get the current logger instance
	 *
	 * @return object Logger instance
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * Log emergency messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function emergency($message, array $context = [])
	{
		return $this->log('emergency', $message, $context);
	}

	/**
	 * Log alert messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function alert($message, array $context = [])
	{
		return $this->log('alert', $message, $context);
	}

	/**
	 * Log critical messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function critical($message, array $context = [])
	{
		return $this->log('critical', $message, $context);
	}

	/**
	 * Log error messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function error($message, array $context = [])
	{
		return $this->log('error', $message, $context);
	}

	/**
	 * Log warning messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function warning($message, array $context = [])
	{
		return $this->log('warning', $message, $context);
	}

	/**
	 * Log notice messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function notice($message, array $context = [])
	{
		return $this->log('notice', $message, $context);
	}

	/**
	 * Log info messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function info($message, array $context = [])
	{
		return $this->log('info', $message, $context);
	}

	/**
	 * Log debug messages
	 *
	 * @param string $message Log message
	 * @param array $context Extra contact information
	 * @return boolean Log pass/fail
	 */
	public function debug($message, array $context = [])
	{
		return $this->log('debug', $message, $context);
	}

	/**
	 * Push the log message and context information into Mongo
	 *
	 * @param string $level Logging level (ex. info, debug, notice...)
	 * @param string $message Log message
	 * @param array $context Extra context information
	 * @return boolean Success/fail of logging
	 */
	public function log($level, $message, array $context = [])
	{
		$logger = $this->getLogger();
		return $logger->$level($message, $context);
	}
}
