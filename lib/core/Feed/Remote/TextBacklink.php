<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Feed_Remote_TextBacklink extends Feed_Remote_Abstract
{
	var $type = "textbacklink";
	
	static function url($feedUrl)
	{
		$me = new self($feedUrl);
		return $me;
	}
	
	public function sendData($page, $name, $data)
	{
		global $tikilib, $feedItem, $caching;
		
 		$pageInfo = $tikilib->get_page_info($page);
		$client = new Zend_Http_Client($this->feedUrl);
		
		$client->setParameterGet('type', "textbacklink_contribution");
		$client->setParameterGet('contribution', json_encode(array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array(
				'page'=> $page,
				'name'=> $name,
				'description'=> $data,
				'date' => $pageInfo['lastModif'],
				'href' => "http://localhost" . dirname($_SERVER["REQUEST_URI"]) . '/' . TikiLib::lib("wiki")->url_for_operation_on_a_page("tiki-index.php", $pageInfo['pageName'])
			),
		)));
		
		$response = $client->request();
		
		print_r($response->getBody());
	}
}