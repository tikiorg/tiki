<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_extendcarthold_info()
{
	return array(
		'name' => tra('Extend Cart Inventory Hold'),
		'documentation' => tra('PluginExtendCartHold'),
		'description' => tra('Extend the time items are held in a shopping cart'),
		'prefs' => array('wikiplugin_extendcarthold', 'payment_feature'),
		'filter' => 'wikicontent',
		'format' => 'html',
		'iconname' => 'cart',
		'introduced' => 7,
		'tags' => array( 'experimental' ),
		'params' => array(
		),
	);
}

function wikiplugin_extendcarthold( $data, $params )
{
	$cartlib = TikiLib::lib('cart');
	$cartlib->extend_onhold_list(); 
} 
			
