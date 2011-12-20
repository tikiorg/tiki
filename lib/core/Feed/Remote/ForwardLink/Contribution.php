<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_Remote_ForwardLink_Contribution
{
	static function send($item = array())
	{
		global $tikilib, $feedItem, $caching;
		
		if (empty($item['forwardlink']->href)) return false;
		
		$client = new Zend_Http_Client($item['forwardLink']->href);
		
		$client->setParameterGet('protocol', 'forwardlink');
		$client->setParameterGet('contribution', json_encode(array(
			'version' => '1.0',
			'encoding' => 'UTF-8',
			'feed' => array(
				'type' => 'textlink',
				'entry'=> array(array(
					'date'=> $info['lastModif'],
					'href'=> $item['textlinkHref'],
					'body'=> $item['textlinkBody'],
					'forwardlink'=>$item['forwardLink']
				))
			),
		)));
		
		$response = $client->request();
		
		return $response->getBody();
	}
}
