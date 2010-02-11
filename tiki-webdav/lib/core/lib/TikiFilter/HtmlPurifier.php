<?php

class TikiFilter_HtmlPurifier implements Zend_Filter_Interface
{
	private $cache;

	function __construct( $cacheFolder ) {
		$this->cache = $cacheFolder;
	}

	function filter( $data ) {
		require_once 'lib/htmlpurifier/HTMLPurifier.includes.php';

		$config = HTMLPurifier_Config::createDefault();
		$config->set( 'Cache', 'SerializerPath', $this->cache );
		$purifier = new HTMLPurifier($config);

		return $purifier->purify( $data );
	}
}

?>
