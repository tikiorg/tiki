<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_Remote_TextBacklink_Contribution
{
	static function sendItem($url, $page, $name, $data)
	{
		global $tikilib, $feedItem, $caching;

		$pageInfo = $tikilib->get_page_info($page);
		$client = new Zend_Http_Client($url);
		
		$client->setParameterGet('type', "textlink");
		$client->setParameterGet('contribution', json_encode(array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array(
				'type' => 'textlink',
				'date' => $pageInfo['lastModif'],
				'entry'=> array(array(
					'page'=> $page,
					'name'=> $name,
					'description'=> $data,
					'date' => $pageInfo['lastModif'],
					'href' => $tikilib->tikiUrl() . '?page=' . $page
				))
			),
		)));
		
		$response = $client->request();
		
		return $response->getBody();
	}
}
