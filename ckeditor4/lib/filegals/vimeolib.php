<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class VimeoLib
{
	private $oauth;

	function __construct()
	{
		$this->oauth = TikiLib::lib('oauth');
	}

	function isAuthorized()
	{
		return $this->oauth->is_authorized('vimeo');
	}

	function getQuota()
	{
		$data = $this->callMethod('vimeo.videos.upload.getQuota');
		return $data['user']['upload_space'];
	}

	function getTicket()
	{
		$data = $this->callMethod('vimeo.videos.upload.getTicket', array(
			'upload_method' => 'post',
		));
		return $data['ticket'];
	}
	
	function complete($ticketId, $fileName)
	{
		$data = $this->callMethod('vimeo.videos.upload.complete', array(
			'ticket_id' => $ticketId,
			'filename' => $fileName,
		));
		return $data['ticket']['video_id'];
	}

	private function callMethod($method, array $arguments = array())
	{
		$response = $this->oauth->do_request('vimeo', array(
			'url' => 'https://vimeo.com/api/rest/v2',
			'post' => array_merge($arguments, array(
				'method' => $method,
				'format' => 'json',
			)),
		));

		return json_decode($response->getBody(), true);
	}
}

