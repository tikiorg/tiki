<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

require_once ('tiki-setup.php');

if (!empty($_REQUEST['feed']) && !empty($_REQUEST['name'])) {

	//here we try to view the results of an external feed, admin only
	$access->check_permission('tiki_p_admin');
	
	
	if (isset($_REQUEST['date'])) {
		$item = HtmlFeed_Remote::url(urldecode($_REQUEST['feed']))
			->getItemFromDate(urldecode($_REQUEST['name']), urldecode($_REQUEST['date']));
	} else {
		$item = HtmlFeed_Remote::url(urldecode($_REQUEST['feed']))->getItem(urldecode($_REQUEST['name']));
	}
	
	print_r(json_encode($item));
	
} else {
	
	$htmlFeed = new HtmlFeed();
	print_r(json_encode(
		$htmlFeed->feed()
	));
	
}