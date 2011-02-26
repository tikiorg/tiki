<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_extendcarthold_info() {
	return array(
		'name' => tra('Extend Cart Inventory Hold'),
		'documentation' => tra('PluginExtendCartHold'),
		'description' => tra('Extends the time that items are held in the shop before timing out'),
		'prefs' => array(), // TODO
		'filter' => 'wikicontent',
		'format' => 'html',
		'params' => array(
		),
	);
}

function wikiplugin_extendcarthold( $data, $params ) {
	global $cartlib; require_once 'lib/payment/cartlib.php';
	$cartlib->extend_onhold_list(); 
} 
			
