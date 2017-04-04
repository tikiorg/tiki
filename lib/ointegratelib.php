<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

use Symfony\Component\Yaml\Yaml;

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
class OIntegrate
{
	private $schemaVersion = array();
	private $acceptTemplates = array();

    /**
     * @param $name
     * @param $engineOutput
     * @return OIntegrate_Engine_JavaScript|OIntegrate_Engine_Smarty|OIntegrate_Engine_Index
     */
    public static function getEngine( $name, $engineOutput ) // {{{
	{
		switch( $name )
		{
		case 'javascript':
			return new OIntegrate_Engine_JavaScript;
		case 'smarty':
			return new OIntegrate_Engine_Smarty($engineOutput == 'tikiwiki');
		case 'index':
			return new OIntegrate_Engine_Index;
		}
	} // }}}

    /**
     * @param $from
     * @param $to
     * @return OIntegrate_Converter_Direct|OIntegrate_Converter_EncodeHtml|OIntegrate_Converter_HtmlToTiki|OIntegrate_Converter_TikiToHtml|OIntegrate_Converter_Indexer
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
			break;
		case 'index':
		case 'mindex':
			if ($to == 'index') {
				return new OIntegrate_Converter_Indexer;
			} elseif ( $to == 'html' ) {
				return new OIntegrate_Converter_Indexer('html');
			} elseif ( $to == 'tikiwiki' ) {
				return new OIntegrate_Converter_Indexer('tikiwiki');
			}
			break;
		}

	} // }}}

	/**
	 * @param string $url
	 * @param string $postBody url or json encoded post parameters
	 * @param bool $clearCache
	 * @return OIntegrate_Response
	 */
    function performRequest( $url, $postBody = null, $clearCache = false ) // {{{
	{
		$cachelib = TikiLib::lib('cache');
		$tikilib = TikiLib::lib('tiki');

		$cacheKey = $url . $postBody;

		if ( $cache = $cachelib->getSerialized($cacheKey)) {
			if (time() < $cache['expires'] && ! $clearCache) {
				return $cache['data'];
			}

			$cachelib->invalidate($cacheKey);
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
			if (json_decode($postBody)) {	// autodetect if the content type should be json
				$requestContentType = 'application/json';
			} else {
				$requestContentType = 'application/x-www-form-urlencoded';
			}
			$http_headers = array(
					'Accept' => 'application/json,text/x-yaml',
					'OIntegrate-Version' => '1.0',
					'Content-Type' => $requestContentType,
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

		$requestContentType = $httpResponse->getHeaders()->get('Content-Type');
		$cacheControl = $httpResponse->getHeaders()->get('Cache-Control');

		$response = new OIntegrate_Response;
		$response->contentType = $requestContentType;
		$response->cacheControl = $cacheControl;
		if ($requestContentType) {
			$mediaType = $requestContentType->getMediaType();
		} else {
			$mediaType = '';
		}
		$response->data = $this->unserialize($mediaType, $content);

		$filter = new DeclFilter;
		$filter->addCatchAllFilter('xss');

		$response->data = $filter->filter($response->data);
		$response->version = $httpResponse->getHeaders()->get('OIntegrate-Version');
		$response->schemaVersion = $httpResponse->getHeaders()->get('OIntegrate-SchemaVersion');
		if ( ! $response->schemaVersion && isset( $response->data->_version ) )
			$response->schemaVersion = $response->data->_version;
		$response->schemaDocumentation = $httpResponse->getHeaders()->get('OIntegrate-SchemaDocumentation');

		global $prefs;
		if (empty($cacheControl)) {
			$maxage = 0;
			$nocache = false;
		} else {
			// Respect cache duration and no-cache asked for
			$maxage = $cacheControl->getDirective('max-age');
			$nocache = $cacheControl->getDirective('no-cache');
		}
		if ( $maxage ) {
			$expiry = time() + $maxage;

			$cachelib->cacheItem(
				$cacheKey,
				serialize(array('expires' => $expiry, 'data' => $response))
			);
		// Unless service specifies not to cache result, apply a default cache
		} elseif (empty($nocache) && $prefs['webservice_consume_defaultcache'] > 0 ) {
			$expiry = time() + $prefs['webservice_consume_defaultcache'];

			$cachelib->cacheItem($cacheKey, serialize(array('expires' => $expiry, 'data' => $response)));
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
			return Yaml::parse($data);
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
	public $errors = array();

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
		if ( ! $engine ) {
			$this->errors = array( 1000, tr('Engine "%0" not found.', $engineOutput) );
			return false;
		}

		if ( ! $output = OIntegrate::getConverter($engineOutput, $outputContext) ) {
			$this->errors = array( 1001, tr('Output converter "%0" not found.', $outputContext) );
			return false;
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
		/** @var Smarty $smarty */
		$smarty = new Smarty;
		$smarty->security = true;
		$smarty->setTemplateDir(dirname($templateFile));
		$smarty->setCompileDir(TIKI_PATH . "/temp/templates_c");
		$smarty->setPluginsDir([]);

		if ( $this->changeDelimiters ) {
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
		}

		$smarty->assign('response', $data);
		return $smarty->fetch($templateFile);
	}
} // }}}

/**
 * Engine to pass on raw data and mapping info from the template
 */
class OIntegrate_Engine_Index implements OIntegrate_Engine
{
    /**
     * @param array $data
     * @param string $templateFile
     * @return array
	 */
    function process( $data, $templateFile )
	{
		$mappingString = file_get_contents($templateFile);
		$mapping = json_decode($mappingString, true);

		return [
			'data' => $data,
			'mapping' => $mapping,
		];
	}
}


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
		return TikiLib::lib('parser')->parse_data(htmlentities($content, ENT_QUOTES, 'UTF-8'));
	}
} // }}}

/**
 * Attempt to index the result from the request
 */
class OIntegrate_Converter_Indexer implements OIntegrate_Converter
{
	private $format;

	function __construct($format = 'none')
	{
		$this->format = $format;
	}

	/**
     * @param $content
     * @return mixed|string
     */
    function convert( $content )
	{
		if ($this->format === 'html' || $this->format === 'tikiwiki') {

			if (! empty($_REQUEST['nt_name'])) {	// preview from admin/webservice page

				$source = new Search_ContentSource_WebserviceSource();
				$factory = new Search_Type_Factory_Direct();

				if ($_REQUEST['nt_output'] === 'mindex') {
					$documents = $source->getDocuments();
					$data = [];
					$count = 0;
					foreach ($documents as $document) {
						if (strpos($document, $_REQUEST['nt_name']) === 0) {
							$data[$document] = $source->getDocument($document, $factory);
							$count++;
							if ($count > 100) {	// enough for a preview?
								break;
							}
						}
					}
				} else {
					$data = $source->getDocument($_REQUEST['nt_name'], $factory);
				}

				$output = '<h3>' . tr('Parsed Data') . '</h3>';
				$output .= '<pre style="max-height: 40em; overflow: auto; white-space: pre-wrap">';
				$output .= htmlentities(
					print_r($data, true),
						ENT_QUOTES,
						'UTF-8'
				);

			} else {

				$output = '<h3>' . tr('Data') . '</h3>';
				$output .= '<pre style="max-height: 20em; overflow: auto; white-space: pre-wrap">';
				$output .= htmlentities(
					json_encode($content['data'], JSON_PRETTY_PRINT),
						ENT_QUOTES,
						'UTF-8'
				);
				$output .= '</pre>';

				if ($this->format === 'html') {
					$output .= '<h3>' . tr('Mapping') . '</h3>';
					$output .= '<pre style="max-height: 20em; overflow: auto; white-space: pre-wrap">';
					$output .= htmlentities(
						json_encode($content['mapping'], JSON_PRETTY_PRINT),
						ENT_QUOTES,
						'UTF-8'
					);
					$output .= '</pre>';

				} else {	// wiki mode from plugin
					$output = "~np~{$output}~/np~";
				}

			}

			return $output;
		} else {

			return $content;
		}

	}
}
