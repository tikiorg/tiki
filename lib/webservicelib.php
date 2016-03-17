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

	private $templates = array();
	private $all = false;

    /**
     * @param $name
     * @return Tiki_Webservice
     */
    public static function create( $name )
	{
		if ( ! ctype_alpha($name) ) {
			return;
		}

		$ws = new self;
		$ws->name = strtolower($name);

		return $ws;
	}

    /**
     * @param $name
     * @return Tiki_Webservice
     */
    public static function getService( $name )
	{
		$name = strtolower($name);

		global $tikilib;
		$result = $tikilib->query(
			"SELECT url, operation, wstype, body, schema_version, schema_documentation FROM tiki_webservice WHERE service = ?",
			array( $name )
		);

		while ( $row = $result->fetchRow() ) {
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
		return array('REST', 'SOAP');
	}

    /**
     * @return array
     */
    public static function getList()
	{
		global $tikilib;

		$result = $tikilib->query("SELECT service FROM tiki_webservice ORDER BY service");
		$list = array();

		while( $row = $result->fetchRow() )
			$list[] = $row['service'];

		return $list;
	}

	function save()
	{
		global $tikilib;
		$tikilib->query("DELETE FROM tiki_webservice WHERE service = ?", array( $this->name ));

		$tikilib->query(
			"INSERT INTO tiki_webservice (service, url, operation, wstype, body, schema_version, schema_documentation) VALUES(?,?,?,?,?,?,?)",
			array(
				$this->name,
				$this->url,
				$this->operation,
				$this->wstype,
				$this->body,
				$this->schemaVersion,
				$this->schemaDocumentation,
			)
		);
	}

	function delete()
	{
		global $tikilib;
		$tikilib->query("DELETE FROM tiki_webservice WHERE service = ?", array( $this->name ));
		$tikilib->query("DELETE FROM tiki_webservice_template WHERE service = ?", array( $this->name ));
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
				if ( preg_match_all("/%(\w+)%/", $this->url . ' ' . $this->body, $matches, PREG_PATTERN_ORDER) ) {
					return array_diff($matches[1], array( 'service', 'template' ));
				} else {
					return array();
				}
		}
	}

    /**
     * @param $params
     * @return array
     */
    function getParameterMap( $params )
	{
		$parameters = array();

		foreach ( $this->getParameters() as $key => $name ) {
			if ( isset($params[$name]) ) {
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
    function performRequest( $params, $fullReponse = false )
	{
		global $soaplib, $prefs;

		$built = $this->url;
		$builtBody = $this->body;

		$map = $this->getParameterMap($params);

		if ( $built ) {
			switch ( $this->wstype ) {
				case 'SOAP':
					if ( !empty($this->operation) ) {
						$options = array( 'encoding' => 'UTF-8' );

						if ( $prefs['use_proxy'] == 'y' && !strpos($built, 'localhost') ) {
							$options['proxy_host'] = $prefs['proxy_host'];
							$options['proxy_port'] = $prefs['proxy_port'];
						}

						$response = new OIntegrate_Response();
						$soaplib->allowCookies = $this->allowCookies;
						try {
							$response->data = $soaplib->performRequest($built, $this->operation, $map, $options, $fullReponse);
						} catch (Exception $e) {
							TikiLib::lib('errorreport')->report(
									tr('Webservice error on %0 request "%1"', $this->wstype, $this->url) . '<br>' . $e->getMessage()
							);
						}

						return $response;
					}

					return false;

				case 'REST':
				default:
					foreach ( $map as $name => $value ) {
						$built = str_replace("%$name%", urlencode($value), $built);
						$builtBody = str_replace("%$name%", urlencode($value), $builtBody);
					}

					$ointegrate = new OIntegrate;
					$ointegrate->addAcceptTemplate('smarty', 'tikiwiki');
					$ointegrate->addAcceptTemplate('smarty', 'html');
					$ointegrate->addAcceptTemplate('javascript', 'html');

					if ( $this->schemaVersion ) {
						$ointegrate->addSchemaVersion($this->schemaVersion);
					}

				try {
					$response = $ointegrate->performRequest($built, $builtBody);
				} catch (Exception $e) {
					TikiLib::lib('errorreport')->report(
							tr('Webservice error on %0 request "%1"', $this->wstype, $this->url) . '<br>' . $e->getMessage()
					);
				}

					return $response;
			}
		}
	}

    /**
     * @param $name
     * @return Tiki_Webservice_Template
     */
    function addTemplate( $name )
	{
		if ( ! ctype_alpha($name) || empty($name) ) {
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
    function removeTemplate( $name )
	{
		global $tikilib;

		$tikilib->query("DELETE FROM tiki_webservice_template WHERE service = ? AND template = ?", array( $this->name, $name ));
	}

    /**
     * @return array
     */
    function getTemplates()
	{
		if ( $this->all )
			return $this->templates;

		global $tikilib;
		$result = $tikilib->query(
			"SELECT template, last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ?",
			array( $this->name )
		);

		while ( $row = $result->fetchRow() ) {
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
    function getTemplate( $name )
	{
		if ( isset($this->templates[$name]) )
			return $this->templates[$name];

		global $tikilib;

		$result = $tikilib->query(
			"SELECT last_modif, engine, output, content FROM tiki_webservice_template WHERE service = ? AND template = ?",
			array( $this->name, $name )
		);

		while ( $row = $result->fetchRow() ) {
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
			array( $this->webservice->getName(), $this->name )
		);

		$tikilib->query(
			"INSERT INTO tiki_webservice_template (service, template, engine, output, content, last_modif) VALUES(?,?,?,?,?,?)",
			array(
				$this->webservice->getName(),
				$this->name,
				$this->engine,
				$this->output,
				$this->content,
				time(),
			)
		);
	}

    /**
     * @return string
     */
    function getTemplateFile()
	{
		$token = sprintf("%s_%s", $this->webservice->getName(), $this->name);
		$file = "temp/cache/" . md5($token) . '.tpl';

		if ( ! file_exists($file) || $this->lastModif > filemtime($file) ) {
			file_put_contents($file, $this->content);
		}

		return realpath($file);
	}

    /**
     * @param OIntegrate_Response $response
     * @param $outputContext
     * @return mixed|string
     */
    function render( OIntegrate_Response $response, $outputContext )
	{
		return $response->render($this->engine, $this->output, $outputContext, $this->getTemplateFile());
	}
}
