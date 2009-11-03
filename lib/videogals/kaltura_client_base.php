<?php
/*
This file is part of the Kaltura Collaborative Media Suite which allows users
to do with audio, video, and animation what Wiki platfroms allow them to do with
text.

Copyright (C) 2006-2008 Kaltura Inc.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class KalturaClientBase 
{
	const KALTURA_API_VERSION = "0.7";
	const KALTURA_SERVICE_FORMAT_JSON = 1;
	const KALTURA_SERVICE_FORMAT_XML  = 2;
	const KALTURA_SERVICE_FORMAT_PHP  = 3;

	/**
	 * @var KalturaConfiguration
	 */
	private $config;
	
	/**
	 * @var string
	 */
	private $ks;
	
	/**
	 * @var boolean
	 */
	private $shouldLog = false;
	
	/**
	 * Kaltura client constuctor, expecting configuration object 
	 *
	 * @param KalturaConfiguration $config
	 */
	public function __construct(KalturaConfiguration $config)
	{
		$this->config = $config;
		
		$logger = $this->config->getLogger();
		if ($logger instanceof IKalturaLogger)
		{
			$this->shouldLog = true;	
		}
	}

	function do_http ( $url, $params ,  $optional_headers = null)
	{
		if ( function_exists('curl_init') )
			return self::do_curl ( $url, $params ,  $optional_headers  );
		else
			return self::do_post_request ( $url, $params ,  $optional_headers  );
	}

	function do_curl ( $url, $params ,  $optional_headers = null )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10 );

		$result = curl_exec($ch);
		$curl_error = curl_error($ch);
		curl_close($ch);
		return array( $result, $curl_error );
	}

	// TODO- make sure the $data is infact an array object
	function do_post_request($url, $data, $optional_headers = null)
	{
		$formatted_data = http_build_query($data , "", "&");
		$params = array('http' => array(
		             'method' => 'POST',
		             "Accept-language: en\r\n".
      					"Content-type: application/x-www-form-urlencoded\r\n",
		             'content' => $formatted_data
		          ));
		if ($optional_headers !== null) {
		   $params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			$php_errormsg = "";
		   throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
		   throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return array( $response, '' );
	}
	
	public function hit($method, KalturaSessionUser $session_user, $params)
	{
		$start_time = microtime(true);
		
		$this->log("service url: [" . $this->config->serviceUrl . "]");
		$this->log("trying to call method: [" . $method . "] for user id: [" . $session_user->userId . "] using session: [" .$this->ks . "]");
		
		// append the basic params
		$params["kaltura_api_version"] 	= self::KALTURA_API_VERSION;
		$params["partner_id"] 			= $this->config->partnerId;
		$params["subp_id"] 				= $this->config->subPartnerId;
		$params["format"] 				= $this->config->format;
		$params["uid"] 					= $session_user->userId;
		$this->addOptionalParam($params, "user_name", $session_user->screenName);
		$this->addOptionalParam($params, "ks", $this->ks);
		
		$url = $this->config->serviceUrl . "/index.php/partnerservices2/" . $method;
		$this->log("full reqeust url: [" . $url . "]");

		$signature = $this->signature($params);
		$params["kalsig"] = $signature;

		list( $post_result, $error ) = self::do_http($url, $params);

		if ($error)
		{
			// TODO: add error code?
			$result["error"] = array($error);
		}
		else 
		{
			$this->log("result (serialized): " . $post_result);
			
			if ($this->config->format == self::KALTURA_SERVICE_FORMAT_PHP)
			{
				$result = @unserialize($post_result);

				if (!$result) {
					$result["result"] = null;
					 // TODO: add error code?
					$result["error"] = array("failed to serialize server result");
				}
				$dump = print_r($result, true);
				$this->log("result (object dump): " . $dump);
			}
			else
			{
				throw new Exception("unsupported format");
			}
		}
		
		$end_time = microtime (true);
		
		$this->log("execution time for method [" . $method . "]: [" . ($end_time - $start_time) . "]");
		
		return $result;
	}

	public function start(KalturaSessionUser $session_user, $secret, $admin = null, $privileges = null, $expiry = 86400)
	{
		$result = $this->startsession($session_user, $secret, $admin, $privileges, $expiry);

		$this->ks = @$result["result"]["ks"];
		return $result;
	}
	
	private function signature($params)
	{
		ksort($params);
		$str = "";
		foreach ($params as $k => $v)
		{
			$str .= $k.$v;
		}
		return md5($str);
	}
		
	public function getKs()
	{
		return $this->ks;
	}
	
	public function setKs($ks)
	{
		$this->ks = $ks;
	}
	
	protected function addOptionalParam(&$params, $paramName, $paramValue)
	{
		if ($paramValue !== null)
		{
			$params[$paramName] = $paramValue;
		}
	}
	
	protected function log($msg)
	{
		if ($this->shouldLog)
			$this->config->getLogger()->log($msg);
	}
}

class KalturaSessionUser
{
	var $userId;
	var $screenName;
}

class KalturaConfiguration
{
	private $logger;

	public $serviceUrl    = "http://www.kaltura.com";
	public $format        = KalturaClient::KALTURA_SERVICE_FORMAT_PHP;
	public $partnerId     = null;
	public $subPartnerId  = null;
	
	/**
	 * Constructs new kaltura configuration object, expecting partner id & sub partner id
	 *
	 * @param int $partnerId
	 * @param int $subPartnerId
	 */
	public function __construct($partnerId, $subPartnerId)
	{
		$this->partnerId 	= $partnerId;
		$this->subPartnerId = $subPartnerId;
	}
	
	/**
	 * Set logger to get kaltura client debug logs
	 *
	 * @param IKalturaLogger $log
	 */
	public function setLogger(IKalturaLogger $log)
	{
		$this->logger = $log;
	}
	
	/**
	 * Gets the logger (Internal client use)
	 *
	 * @return unknown
	 */
	public function getLogger()
	{
		return $this->logger;
	}
}

/**
 * Implement to get kaltura client logs
 *
 */
interface IKalturaLogger 
{
	function log($msg); 
}
