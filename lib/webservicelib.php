<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/ointegratelib.php';
require_once 'soap/soaplib.php';
require_once 'soap/wsdllib.php';

/**
 * Tiki_Webservice
 *
 */
class Tiki_Webservice
{
	private $name;
	public $url;
	public $body;
	public $operation;
	public $wstype;
	public $schemaVersion;
	public $schemaDocumentation;
	public $allowCookies;

	private $templates = [];
	private $all = false;

	/**
	 * @param $name
	 * @return Tiki_Webservice
	 */
	public static function create($name)
	{
		if (! ctype_alpha($name) || self::getService($name)) {
			return null;
		}

		$ws = new self;
		$ws->name = strtolower($name);

		return $ws;
	}

	/**
	 * @param $name
	 * @return Tiki_Webservice
	 */
	public static function getService($name)
	{
		$name = strtolower($name);

		global $tikilib;
		$result = $tikilib->query(
			"SELECT url, operation, wstype, body, schema_version, schema_documentation FROM tiki_webservice WHERE service = ?",
			[ $name ]
		);

		while ($row = $result->fetchRow()) {
			$service = new self;

			$service->name = $name;
			$service->url = $row['url'];
			$service->body = $row['body'];
			$service->operation = $row['operation'];
			$service->wstype = $row['wstype'];
			$service->schemaVersion = $row['schema_version'];
			$service->schemaDocumentation = $row['schema_documentation'];

			return $service;
		}
	}

	/**
	 * @return array
	 */
	public static function getTypes()
	{
		return ['REST', 'SOAP'];
	}

	/**
	 * @return array
	 */
	public static function getList()
	{
		global $tikilib;

		$result = $tikilib->query("SELECT service FROM tiki_webservice ORDER BY service");
		$list = [];

		while ($row = $result->fetchRow()) {
			$list[] = $row['service'];
		}

		return $list;
	}

	function save()
	{
		global $tikilib;
		$tikilib->query("DELETE FROM tiki_webservice WHERE service = ?", [ $this->name ]);

		$tikilib->query(
			"INSERT INTO tiki_webservice (service, url, operation, wstype, body, schema_version, schema_documentation) VALUES(?,?,?,?,?,?,?)",
			[
				$this->name,
				$this->url,
				$this->operation,
				$this->wstype,
				$this->body,
				$this->schemaVersion,
				$this->schemaDocumentation,
			]
		);
	}

	function delete()
	{
		global $tikilib;
		$tikilib->query("DELETE FROM tiki_webservice WHERE service = ?", [ $this->name ]);
		$tikilib->query("DELETE FROM tiki_webservice_template WHERE service = ?", [ $this->name ]);
	}

	/**
	 * @param $newName
	 * @return $this|null
	 */
	function rename($newName)
	{
		$tiki_webservice = TikiDb::get()->table('tiki_webservice');
		if (ctype_alpha($newName) && $tiki_webservice->fetchCount(['service' => $newName]) == 0) {
			TikiDb::get()->table('tiki_webservice_template')->updateMultiple(
				['service' => $newName,],
				['service' => $this->name]
			);
			$tiki_webservice->update(['service' => $newName,], ['service' => $this->name]);
			$this->name = $newName;
			return $this;
		} else {
			return null;
		}
	}

	/**
	 * @return array
	 */
	function getParameters()
	{
		global $wsdllib;

		switch ($this->wstype) {
			case 'SOAP':
				return $wsdllib->getParametersNames($this->url, $this->operation);

			case 'REST':
			default:
				if (preg_match_all("/%(\w+)%/", $this->url . ' ' . $this->body, $matches, PREG_PATTERN_ORDER)) {
					return array_diff($matches[1], [ 'service', 'template' ]);
				} else {
					return [];
				}
		}
	}

	/**
	 * @param $params
	 * @return array
	 */
	function getParameterMap($params)
	{
		$parameters = [];

		foreach ($this->getParameters() as $key => $name) {
			if (isset($params[$name])) {
				$parameters[$name] = $params[$name];
			} else {
				$parameters[$name] = '';
			}
		}

		return $parameters;
	}

	/*
	*	If fullResponse = true, "out" parameters from .NET calls are included in the response.
	*	If false, only the <request>Response part of the reply is included.
	*	fullResponse has no effect for REST calls
	*/
	/**
	 * @param $params
	 * @param bool $fullReponse
	 * @return bool|OIntegrate_Response
	 */
	function performRequest($params, $fullReponse = false, $clearCache = false)
	{
		global $soaplib, $prefs;

		$built = $this->url;
		$builtBody = $this->body;

		$map = $this->getParameterMap($params);

		if ($built) {
			switch ($this->wstype) {
				case 'SOAP':
					if (! empty($this->operation)) {
						$options = [ 'encoding' => 'UTF-8' ];

						if ($prefs['use_proxy'] == 'y' && ! strpos($built, 'localhost')) {
							$options['proxy_host'] = $prefs['proxy_host'];
							$options['proxy_port'] = $prefs['proxy_port'];
						}

						$response = new OIntegrate_Response();
						$soaplib->allowCookies = $this->allowCookies;
						try {
							$response->data = $soaplib->performRequest($built, $this->operation, $map, $options, $fullReponse);
						} catch (Exception $e) {
							Feedback::error(tr('Webservice error on %0 request "%1"', $this->wstype, $this->url)
								. '<br>' . $e->getMessage(), 'session');
						}

						return $response;
					}

					return false;

				case 'REST':
				default:
					foreach ($map as $name => $value) {
						$built = str_replace("%$name%", urlencode($value), $built);
						$builtBody = str_replace("%$name%", urlencode($value), $builtBody);
					}

					$ointegrate = new OIntegrate;
					$ointegrate->addAcceptTemplate('smarty', 'tikiwiki');
					$ointegrate->addAcceptTemplate('smarty', 'html');
					$ointegrate->addAcceptTemplate('javascript', 'html');

					if ($this->schemaVersion) {
						$ointegrate->addSchemaVersion($this->schemaVersion);
					}

					try {
						$response = $ointegrate->performRequest($built, $builtBody, $clearCache);
					} catch (Exception $e) {
						Feedback::error(tr('Webservice error on %0 request "%1"', $this->wstype, $this->url)
						. '<br>' . $e->getMessage(), 'session');
					}

					return $response;
			}
		}
	}

	/**
	 * @param $name
	 * @return Tiki_Webservice_Template
	 */
	function addTemplate($name)
	{
		if (! ctype_alpha($name) || empty($name)) {
			return;
		}

		$template = new Tiki_Webservice_Template;
		$template->webservice = $this;
		$template->name = strtolower($name);

		$this->templates[$name] = $template;

		return $template;
	}

	/**
	 * @param $name
	 */
	function removeTemplate($name)
	{
		global $tikilib;

		$tikilib->query("DELETE FROM tiki_webservice_template WHERE service = ? AND template = ?", [ $this->name, $name ]);
	}

	/**
	 * @return array
	 */
	function getTemplates()
	{
		if ($this->all) {
			return $this->templates;
		}

		global $tikilib;
		$result = $tikilib->query(
			"SELECT template, last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ?",
			[ $this->name ]
		);

		while ($row = $result->fetchRow()) {
			$template = new Tiki_Webservice_Template;
			$template->webservice = $this;
			$template->name = $row['template'];
			$template->lastModif = $row['last_modif'];
			$template->engine = $row['engine'];
			$template->output = $row['output'];
			$template->content = $row['content'];

			$this->templates[$template->name] = $template;
		}

		$this->all = true;
		return $this->templates;
	}

	/**
	 * @param $name
	 * @return Tiki_Webservice_Template
	 */
	function getTemplate($name)
	{
		if (isset($this->templates[$name])) {
			return $this->templates[$name];
		}

		global $tikilib;

		$result = $tikilib->query(
			"SELECT last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ? AND template = ?",
			[ $this->name, $name ]
		);

		while ($row = $result->fetchRow()) {
			$template = new Tiki_Webservice_Template;
			$template->webservice = $this;
			$template->name = $name;
			$template->lastModif = $row['last_modif'];
			$template->engine = $row['engine'];
			$template->output = $row['output'];
			$template->content = $row['content'];

			$this->templates[$name] = $template;
			return $template;
		}
	}

	function getName()
	{
		return $this->name;
	}
}


/**
 * Tiki_Webservice_Template
 *
 */
class Tiki_Webservice_Template
{
	public $webservice;
	public $name;
	public $engine;
	public $output;
	public $content;
	public $lastModif;

	function save()
	{
		global $tikilib;

		$tikilib->query(
			"DELETE FROM tiki_webservice_template WHERE service = ? AND template = ?",
			[ $this->webservice->getName(), $this->name ]
		);

		$tikilib->query(
			"INSERT INTO tiki_webservice_template (service, template, engine, output, content, last_modif) VALUES(?,?,?,?,?,?)",
			[
				$this->webservice->getName(),
				$this->name,
				$this->engine,
				$this->output,
				$this->content,
				time(),
			]
		);

		if ($this->engine === 'index') {
			if ($this->output === 'mindex') {
				Feedback::warning(tra('You will need to rebuild the search index to see these changes'));
			}

			require_once 'lib/search/refresh-functions.php';
			refresh_index('webservice', $this->name);
		}
	}

	/**
	 * @return string
	 */
	function getTemplateFile()
	{
		$token = sprintf("%s_%s", $this->webservice->getName(), $this->name);
		$file = "temp/cache/" . md5($token) . '.tpl';

		if (! file_exists($file) || $this->lastModif > filemtime($file)) {
			file_put_contents($file, $this->content);
		}

		return realpath($file);
	}

	/**
	 * @param OIntegrate_Response $response
	 * @param $outputContext
	 * @return mixed|string
	 */
	function render(OIntegrate_Response $response, $outputContext)
	{
		return $response->render($this->engine, $this->output, $outputContext, $this->getTemplateFile());
	}
}
