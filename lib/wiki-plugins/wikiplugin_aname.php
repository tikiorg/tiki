<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_aname_info()
{
	return array(
		'name' => tra('Anchor Name'),
		'documentation' => 'PluginAname',
		'description' => tra('Create an anchor that can be linked to'),
		'prefs' => array('wikiplugin_aname'),
		'body' => tra('The name of the anchor.'),
		'tags' => array( 'basic' ),
		'introduced' => 1,
		'params' => array(),
		'iconname' => 'link',
	);
}

function wikiplugin_aname($data, $params)
{
   global $tikilib;
   extract($params, EXTR_SKIP);
        
    // the following replace is necessary to maintain compliance with XHTML 1.0 Transitional
	// and the same behavior as tikilib.php and ALINK. This will change when the world arrives at XHTML 1.0 Strict.
	$data = preg_replace('/[^a-zA-Z0-9]+/', '_', $data);

	return "<a id=\"$data\"></a>";
}
