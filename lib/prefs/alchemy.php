<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_alchemy_list()
{
	return [
		'alchemy_ffmpeg_path' => [
			'name' => tra('Path to ffmpeg binary'),
			'description' => tra('Path to the location of ffmpeg'),
			'type' => 'text',
			'size' => '256',
			'default' => '/usr/bin/ffmpeg',
		],

		'alchemy_ffprobe_path' => [
			'name' => tra('Path to ffprobe binary'),
			'description' => tra('Path to the location of ffprobe'),
			'type' => 'text',
			'size' => '256',
			'default' => '/usr/bin/ffprobe',
		],
		'alchemy_imagine_driver' => [
			'name' => tra('Image library to use with Alchemy'),
			'description' => tra('Select one of Image Magick or GD Graphics Library'),
			'type' => 'list',
			'options' => [
				'imagick' => tra('Imagemagick'),
				'gd' => tra('GD')
			],
			'default' => 'imagick',
		],

	];
}
