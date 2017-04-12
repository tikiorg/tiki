<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_image_list()
{
	return array (
		'image_responsive_class' => array(
			'name' => tra('Default for img-responsive class used in the IMG plugin'),
            'description' => tra('Default option for whether an image produced with the IMG plugin has the img-responsive class - a plugin parameter allows this to be overridden'),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
