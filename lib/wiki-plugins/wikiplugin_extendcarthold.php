<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_extendcarthold_info()
{
	return [
		'name' => tra('Extend Cart Inventory Hold'),
		'documentation' => tra('PluginExtendCartHold'),
		'description' => tra('Extend the time items are held in a shopping cart'),
		'prefs' => ['wikiplugin_extendcarthold', 'payment_feature'],
		'filter' => 'wikicontent',
		'format' => 'html',
		'iconname' => 'cart',
		'introduced' => 7,
		'tags' => [ 'experimental' ],
		'params' => [
		],
	];
}

function wikiplugin_extendcarthold($data, $params)
{
	$cartlib = TikiLib::lib('cart');
	$cartlib->extend_onhold_list();
}
