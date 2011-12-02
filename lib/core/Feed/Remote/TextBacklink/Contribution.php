<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_Remote_TextBacklink_Contribution
{
	static function sendItem($item = array())
	{
		global $tikilib, $feedItem, $caching;
		
		$pageInfo = $tikilib->get_page_info($item['pageName']);
		$client = new Zend_Http_Client($item['href']);
		
		$client->setParameterGet('type', "textlink");
		$client->setParameterGet('contribution', json_encode(array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array(
				'type' => 'textlink',
				'entry'=> array(array(
					'page'=> $pageInfo['pageName'],
					'name'=> $item['linkName'],
					'description'=> $data,
					'date'=> $pageInfo['lastModif'],
					'href'=> $tikilib->tikiUrl() . 'tiki-index.php?page=' . $pageInfo['pageName'],
					'originName'=>$item['originName']
				))
			),
		)));
		
		$response = $client->request();
		
		return $response->getBody();
	}
}
