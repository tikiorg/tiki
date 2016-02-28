<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class OIntegrate
{
	private $schemaVersion = array();
	private $acceptTemplates = array();

    /**
     * @param $name
     * @param $engineOutput
     * @return OIntegrate_Engine_JavaScript|OIntegrate_Engine_Smarty
     */
    public static function getEngine( $name, $engineOutput ) // {{{
	{
		switch( $name )
		{
		case 'javascript':
			return new OIntegrate_Engine_JavaScript;
		case 'smarty':
			return new OIntegrate_Engine_Smarty($engineOutput == 'tikiwiki');
		}
	} // }}}

    /**
     * @param $from
     * @param $to
     * @return OIntegrate_Converter_Direct|OIntegrate_Converter_EncodeHtml|OIntegrate_Converter_HtmlToTiki|OIntegrate_Converter_TikiToHtml
     */
    public static function getConverter( $from, $to ) // {{{
	{
		switch( $from )
		{
		case 'html':
			if ( $to == 'tikiwiki' ) {
				return new OIntegrate_Converter_HtmlToTiki;
			} elseif ( $to == 'html' ) {
				return new OIntegrate_Converter_Direct;
			}
    		break;
		case 'tikiwiki':
			if ( $to == 'html' ) {
				return new OIntegrate_Converter_TikiToHtml;
			} elseif ( $to == 'tikiwiki' ) {
				return new OIntegrate_Converter_EncodeHtml;
			}
		}
	} // }}}

    /**
     * @param $url
     * @param null $postBody
     * @return OIntegrate_Response
     */
    function performRequest( $url, $postBody = null ) // {{{
	{
		$cachelib = TikiLib::lib('cache');
		$tikilib = TikiLib::lib('tiki');

		if ( $cache = $cachelib->getSerialized($url.$postBody)) {
			if ( time() < $cache['expires'] )
				return $cache['data'];

			$cachelib->invalidate($url.$postBody);
		}

		$client = $tikilib->get_http_client($url);
		$method = null;

		if ( empty($postBody) ) {
			$method = 'GET';
			$http_headers = array(
					'Accept' => 'application/json,text/x-yaml',
					'OIntegrate-Version' => '1.0',
				);
		} else {
			$method = 'POST';
			$http_headers = array(
					'Accept' => 'application/json,text/x-yaml',
					'OIntegrate-Version' => '1.0',
					'Content-Type' => 'application/x-www-form-urlencoded',
			);
			$client->setRawBody($postBody);
		}

		if ( count($this->schemaVersion) ) {
			$http_headers['OIntegrate-SchemaVersion'] = implode(', ', $this->schemaVersion);
		}
		if ( count($this->acceptTemplates) ) {
			$http_headers['OIntegrate-AcceptTemplate'] = implode(', ', $this->acceptTemplates);
		}
		$client->setHeaders($http_headers);

		$client->setMethod($method);
		$httpResponse = $client->send();
		$content = $httpResponse->getBody();

		$contentType = $httpResponse->getHeaders()->get('Content-Type');
		$cacheControl = $httpResponse->getHeaders()->get('Cache-Control');

		$response = new OIntegrate_Response;
		$response->contentType = $contentType;
		$response->cacheControl = $cacheControl;
		$response->data = $this->unserialize($contentType->getMediaType(), $content);

		$filter = new DeclFilter;
		$filter->addCatchAllFilter('xss');

		$response->data = $filter->filter($response->data);
		$response->version = $httpResponse->getHeaders()->get('OIntegrate-Version');
		$response->schemaVersion = $httpResponse->getHeaders()->get('OIntegrate-SchemaVersion');
		if ( ! $response->schemaVersion && isset( $response->data->_version ) )
			$response->schemaVersion = $response->data->_version;
		$response->schemaDocumentation = $httpResponse->getHeaders()->get('OIntegrate-SchemaDocumentation');

		global $prefs;
		// Respect cache duration asked for
		$maxage = $cacheControl->getDirective('max-age');
		if ( $maxage ) {
			$expiry = time() + $maxage;

			$cachelib->cacheItem(
				$url,
				serialize(array('expires' => $expiry, 'data' => $response))
			);
		// Unless service specifies not to cache result, apply a default cache
		} elseif ( $cacheControl->getDirective('no-cache') !== null && $prefs['webservice_consume_defaultcache'] > 0 ) {
			$expiry = time() + $prefs['webservice_consume_defaultcache'];

			$cachelib->cacheItem($url, serialize(array('expires' => $expiry, 'data' => $response)));
		}

		return $response;
	} // }}}

    /**
     * @param string $type
     * @param string $data
     * @return array|mixed|null
     */
    function unserialize( $type, $data ) // {{{
	{

		if ( empty($data) ) {
			return null;
		}

		switch( $type )
		{
		case 'application/json':
		case 'text/javascript':
			if ( $out = json_decode($data, true) ) {
				return $out;
			}

			// Handle invalid JSON too...
			$fixed = preg_replace('/(\w+):/', '"$1":', $data);
			$out = json_decode($fixed, true);
			return $out;
		case 'text/x-yaml':
			require_once 'core/Horde/Yaml.php';
			require_once 'core/Horde/Yaml/Loader.php';
			require_once 'core/Horde/Yaml/Node.php';
			return Horde_Yaml::load($data);
		default:
			// Attempt anything...
			if ( $out = $this->unserialize('application/json', $data) ) {
				return $out;
			}
			if ( $out = $this->unserialize('text/x-yaml', $data) ) {
				return $out;
			}
		}
	} // }}}

    /**
     * @param $version
     */
    function addSchemaVersion( $version ) // {{{
	{
		$this->schemaVersion[] = $version;
	} // }}}

    /**
     * @param $engine
     * @param $output
     */
    function addAcceptTemplate( $engine, $output ) // {{{
	{
		$this->acceptTemplate[] = "$engine/$output";
	} // }}}
}

/**
 *
 */
class OIntegrate_Response
{
	public $version = null;
	public $schemaVersion = null;
	public $schemaDocumentation = null;
	public $contentType = null;
	public $cacheControl = null;
	public $data;

	private $errors = array();

    /**
     * @param $data
     * @param $schemaVersion
     * @param int $cacheLength
     * @return OIntegrate_Response
     */
    public static function create( $data, $schemaVersion, $cacheLength = 300 ) // {{{
	{
		$response = new self;
		$response->version = '1.0';
		$response->data = $data;
		$response->schemaVersion = $schemaVersion;

		if ( $cacheLength > 0 )
			$response->cacheControl = "max-age=$cacheLength";
		else
			$response->cacheControl = "no-cache";

		return $response;
	} // }}}

    /**
     * @param $engine
     * @param $output
     * @param $templateLocation
     */
    function addTemplate( $engine, $output, $templateLocation ) // {{{
	{
		if ( ! array_key_exists('_template', $this->data) )
			$this->data['_template'] = array();
		if ( ! array_key_exists($engine, $this->data['_template']) )
			$this->data['_template'][$engine] = array();
		if ( ! array_key_exists($output, $this->data['_template'][$engine]) )
			$this->data['_template'][$engine][$output] = array();

		if ( 0 !== strpos($templateLocation, 'http') ) {
			$host = $_SERVER['HTTP_HOST'];
			$proto = 'http';
			$path = dirname($_SERVER['SCRIPT_NAME']);
			$templateLocation = ltrim($templateLocation, '/');

			$templateLocation = "$proto://$host$path/$templateLocation";
		}

		$this->data['_template'][$engine][$output][] = $templateLocation;
	} // }}}

	function send() // {{{
	{
		header('OIntegrate-Version: 1.0');
		header('OIntegrate-SchemaVersion: ' . $this->schemaVersion);
		if ( $this->schemaDocumentation )
			header('OIntegrate-SchemaDocumentation: ' . $this->schemaDocumentation);
		header('Cache-Control: ' . $this->cacheControl);

		$data = $this->data;
		$data['_version'] = $this->schemaVersion;

		$access = TikiLib::lib('access');
		$access->output_serialized($data);
		exit;
	} // }}}

    /**
     * @param $engine
     * @param $engineOutput
     * @param $outputContext
     * @param $templateFile
     * @return mixed|string
     */
    function render( $engine, $engineOutput, $outputContext, $templateFile ) // {{{
	{
		$engine = OIntegrate::getEngine($engine, $engineOutput);
		if ( ! $output = OIntegrate::getConverter($engineOutput, $outputContext) ) {
			$this->errors = array( 1001, 'Output converter not found.' );
			return;
		}

		if ( ! $engine ) {
			$this->errors = array( 1000, 'Engine not found' );
			return;
		}

		$raw = $engine->process($this->data, $templateFile);
		return $output->convert($raw);
	} // }}}

    /**
     * @param null $supportedPairs
     * @return array
     */
    function getTemplates( $supportedPairs = null ) // {{{
	{
		if ( !is_array($this->data) || ! isset( $this->data['_template'] ) || ! is_array($this->data['_template']) )
			return array();

		$templates = array();

		foreach ( $this->data['_template'] as $engine => $outputs ) {
			foreach ( $outputs as $output => $files ) {
				if ( is_array($supportedPairs) && ! in_array("$engine/$output", $supportedPairs) )
					continue;

				$files = (array) $files;

				foreach ( $files as $file ) {
					$content = TikiLib::lib('tiki')->httprequest($file);

					$templates[] = array(
						'engine' => $engine,
						'output' => $output,
						'content' => $content,
					);
				}
			}
		}

		return $templates;
	} // }}}
}

/**
 *
 */
interface OIntegrate_Converter
{
    /**
     * @param $content
     * @return mixed
     */
    function convert( $content );
}

/**
 *
 */
interface OIntegrate_Engine
{
    /**
     * @param $data
     * @param $templateFile
     * @return mixed
     */
    function process( $data, $templateFile );
}

/**
 *
 */
class OIntegrate_Engine_JavaScript implements OIntegrate_Engine // {{{
{
    /**
     * @param $data
     * @param $templateFile
     * @return string
     */
    function process( $data, $templateFile )
	{
		$json = json_encode($data);

		return <<<EOC
<script type="text/javascript">
var response = $json;
</script>
EOC
		. file_get_contents($templateFile);
	}
} // }}}

/**
 *
 */
class OIntegrate_Engine_Smarty implements OIntegrate_Engine // {{{
{
	private $changeDelimiters;

    /**
     * @param bool $changeDelimiters
     */
    function __construct( $changeDelimiters = false )
	{
		$this->changeDelimiters = $changeDelimiters;
	}

    /**
     * @param $data
     * @param $templateFile
     * @return mixed
     */
    function process( $data, $templateFile )
	{
		$smarty = new Smarty;
		$smarty->security = true;
		$smarty->template_dir = dirname($templateFile);
		$smarty->plugins_dir = array();

		if ( $this->changeDelimiters ) {
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
		}

		$smarty->assign('response', $data);
		return $smarty->fetch($templateFile);
	}
} // }}}

/**
 *
 */
class OIntegrate_Converter_Direct implements OIntegrate_Converter // {{{
{
    /**
     * @param $content
     * @return mixed
     */
    function convert( $content )
	{
		return $content;
	}
} // }}}

/**
 *
 */
class OIntegrate_Converter_EncodeHtml implements OIntegrate_Converter // {{{
{
    /**
     * @param $content
     * @return string
     */
    function convert( $content )
	{
		return htmlentities($content, ENT_QUOTES, 'UTF-8');
	}
} // }}}

/**
 *
 */
class OIntegrate_Converter_HtmlToTiki implements OIntegrate_Converter // {{{
{
    /**
     * @param $content
     * @return string
     */
    function convert( $content )
	{
		return '~np~' . $content . '~/np~';
	}
} // }}}

/**
 *
 */
class OIntegrate_Converter_TikiToHtml implements OIntegrate_Converter // {{{
{
    /**
     * @param $content
     * @return mixed|string
     */
    function convert( $content )
	{
		global $tikilib;
		return $tikilib->parse_data(htmlentities($content, ENT_QUOTES, 'UTF-8'));
	}
} // }}}
