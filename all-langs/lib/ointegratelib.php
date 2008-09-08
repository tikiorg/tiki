<?php

class OIntegrate
{
	private $schemaVersion = array();
	private $acceptTemplates = array();

	public static function getEngine( $name, $engineOutput ) // {{{
	{
		switch( $name )
		{
		case 'javascript':
			return new OIntegrate_Engine_JavaScript;
		case 'smarty':
			return new OIntegrate_Engine_Smarty( $engineOutput == 'tikiwiki' );
		}
	} // }}}

	public static function getConverter( $from, $to ) // {{{
	{
		switch( $from )
		{
		case 'html':
			if( $to == 'tikiwiki' )
				return new OIntegrate_Converter_HtmlToTiki;
			break;	
		}
	} // }}}

	function performRequest( $url ) // {{{
	{
		global $cachelib;
		require_once 'lib/cache/cachelib.php';

		if( $cachelib->isCached( $url ) ) {
			$cache = $cachelib->getCached( $url );
			$cache = unserialize( $cache );

			if( time() < $cache['expires'] )
				return $cache['data'];

			$cachelib->invalidate( $url );
		}

		$opts = array(
			'http' => array(
				'method' => 'GET',
				'header' =>
					"Accept: application/json,text/x-yaml\r\n"
					. "OIntegrate-Version: 1.0\r\n",
				'content' => '',
			),
		);

		if( count( $this->schemaVersion ) )
			$opts['http']['header'] .= "OIntegrate-SchemaVersion: " . implode( ', ', $this->schemaVersion ) . "\r\n";
		if( count( $this->acceptTemplates ) )
			$opts['http']['header'] .= "OIntegrate-AcceptTemplate: " . implode( ', ', $this->acceptTemplates ) . "\r\n";

		$context = stream_context_create( $opts );
		$content = file_get_contents( $url, false, $context );

		$contentType = $this->extractHeader( $http_response_header, 'Content-Type' );
		$cacheControl = $this->extractHeader( $http_response_header, 'Cache-Control' );

		$response = new OIntegrate_Response;
		$response->data = $this->unserialize( $contentType, $content );
		$response->version = $this->extractHeader( $http_response_header, 'OIntegrate-Version' );
		$response->schemaVersion = $this->extractHeader( $http_response_header, 'OIntegrate-SchemaVersion' );
		if( ! $response->schemaVersion && isset( $response->data->_version ) )
			$response->schemaVersion = $response->data->_version;

		global $prefs;
		// Respect cache duration asked for
		if( preg_match( '/max-age=(\d+)/', $cacheControl, $parts ) ) {
			$expiry = time() + $parts[1];

			$cachelib->cacheItem( $url, serialize( array(
				'expires' => $expiry,
				'data' => $response,
			) ) );
		// Unless service specifies not to cache result, apply a default cache
		} elseif( false !== strpos( $cacheControl, 'no-cache' ) && $prefs['webservice_consume_defaultcache'] > 0 ) {
			$expiry = time() + $prefs['webservice_consume_defaultcache'];

			$cachelib->cacheItem( $url, serialize( array(
				'expires' => $expiry,
				'data' => $response,
			) ) );
		}

		return $response;
	} // }}}

	private function extractHeader( $headerList, $name ) // {{{
	{
		$name = strtolower( $name );
		foreach( $headerList as $line )
			if( strpos( strtolower($line), $name ) === 0 ) {
				list( $header, $value ) = explode( ':', $line, 2 );

				return trim( $value );
			}
	} // }}}

	function unserialize( $type, $data ) // {{{
	{
		$parts = explode( ';', $type );
		$type = trim($parts[0]);

		switch( $type )
		{
		case 'application/json':
		case 'text/javascript':
			return json_decode( $data, true );
		case 'text/x-yaml':
			return Horde_Yaml::load( $data );
		}
	} // }}}

	function addSchemaVersion( $version ) // {{{
	{
		$this->schemaVersion[] = $version;
	} // }}}

	function addAcceptTemplate( $engine, $output ) // {{{
	{
		$this->acceptTemplate[] = "$engine/$output";
	} // }}}
}

class OIntegrate_Response
{
	public $version = null;
	public $schemaVersion = null;
	public $data;

	private $errors = array();

	function render( $engine, $engineOutput, $outputContext, $templateFile ) // {{{
	{
		$engine = OIntegrate::getEngine( $engine, $engineOutput );
		if( $engineOutput == $outputContext ) {
			$output = new OIntegrate_Converter_Direct;
		} else {
			$output = OIntegrate::getConverter( $engineOutput, $outputContext );

			if( ! $output ) {
				$this->errors = array( 1001, 'Output converter not found.' );
				return;
			}
		}

		if( ! $engine ) {
			$this->errors = array( 1000, 'Engine not found' );
			return;
		}

		$raw = $engine->process( $this->data, $templateFile );
		return $output->convert( $raw );
	} // }}}
}

interface OIntegrate_Converter
{
	function convert( $content );
}

interface OIntegrate_Engine
{
	function process( $data, $templateFile );
}

class OIntegrate_Engine_JavaScript implements OIntegrate_Engine // {{{
{
	function process( $data, $templateFile )
	{
		$json = json_encode( $data );

		return <<<EOC
<script type="text/javascript">
var response = $json;
</script>
EOC
		. file_get_contents( $templateFile );
	}
} // }}}

class OIntegrate_Engine_Smarty implements OIntegrate_Engine // {{{
{
	private $changeDelimiters;

	function __construct( $changeDelimiters = false )
	{
		$this->changeDelimiters = $changeDelimiters;
	}

	function process( $data, $templateFile )
	{
		$smarty = new Smarty;
		$smarty->security = true;
		$smarty->template_dir = dirname($templateFile);

		if( $this->changeDelimiters ) {
			$smarty->left_delimiter = '{{';
			$smarty->right_delimiter = '}}';
		}

		$smarty->assign( 'response', $data );
		return $smarty->fetch( $templateFile );
	}
} // }}}

class OIntegrate_Converter_Direct implements OIntegrate_Converter // {{{
{
	function convert( $content )
	{
		return $content;
	}
} // }}}

class OIntegrate_Converter_HtmlToTiki implements OIntegrate_Converter // {{{
{
	function convert( $content )
	{
		return '~np~' . $content . '~/np~';
	}
} // }}}

?>
