<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function payment_behavior_execute_datachannel( $data, $params, $posts, $executionId ) {
	include 'lib/wiki-plugins/wikiplugin_datachannel.php';
	unset($params['price']);
	$_POST['datachannel_execution'] = $executionId;
	foreach ($posts as $key => $post) {
		$_POST[$key] = $post;
	}
	wikiplugin_datachannel( $data, $params );
}

