<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class VimeoLib
{
	private $oauth;

	/**
	 * VimeoLib constructor.
	 * @param OAuthLib $oauthlib
	 */
	function __construct($oauthlib)
	{
		$this->oauth = $oauthlib;
	}

	function isAuthorized()
	{
		return $this->oauth->is_authorized('vimeo');
	}

	/**
	 * Gets array of space and uploads left for the Vimeo account
	 *
	 * @return array
	 */
	function getQuota()
	{
		$data = $this->callMethod('/me');
		return $data['upload_quota'];
	}

	/**
	 * Gets an upload ticket
	 *
	 * @return array
	 */
	function getTicket()
	{
		$data = $this->callMethod(
			'/me/videos',
			array('type' => 'streaming'),
			'post'
		);
		return $data;
	}

	function complete($completeUri)
	{
		$data = $this->callMethod(
			$completeUri,
			array(),
			'delete'
		);
		return $data;
	}

	function setTitle($videoId, $title)
	{
		$data = $this->callMethod(
			'/videos/' . $videoId,
			array(
				'name' => $title,
			),
			'patch'
		);
		return $data;
	}

	function deleteVideo($videoId)
	{
		$data = $this->callMethod(
			'/videos/' . $videoId,
			array(),
			'delete'
		);
		return $data;
	}

	private function callMethod($method, array $arguments = array(), $httpmethod = 'get')
	{
		$oldVal = ini_get('arg_separator.output');
		ini_set('arg_separator.output', '&');
		$response = $this->oauth->do_request(
			'vimeo',
			array(
				'url' => 'https://api.vimeo.com' . $method,
				$httpmethod => $arguments,
			)
		);

		ini_set('arg_separator.output', $oldVal);

		if ($httpmethod == 'delete' || $httpmethod == 'patch') {
			$headers = $response->getHeaders();	
			return $headers->toArray();
		} else {
			return json_decode($response->getBody(), true);
		}
	}
}

