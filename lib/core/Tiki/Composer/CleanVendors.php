<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;
use Composer\Script\Event;

class CleanVendors
{
	public static function clean(Event $event)
	{
		$vendors = __DIR__ . '/../../../../vendor/';

		self::deleteDirectory($vendors . 'jquery/jquery-ui/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/tests');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/themes');
		self::deleteDirectory($vendors . 'ezyang/htmlpurifier/tests');
		self::deleteDirectory($vendors . 'smarty/smarty/development');
		self::deleteDirectory($vendors . 'smarty/smarty/documentation');
		self::deleteDirectory($vendors . 'zetacomponents/webdav/docs');
		self::deleteDirectory($vendors . 'zetacomponents/webdav/design');
		self::deleteDirectory($vendors . 'zetacomponents/webdav/tests');
	}

	private static function deleteDirectory($path)
	{
		if (! file_exists($path)) {
			return;
		}

		foreach (scandir($path) as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$full = "$path/$file";

			if (is_link($full)) {
				unlink($full);
			} elseif (is_dir($full)) {
				self::deleteDirectory($full);
			} else {
				unlink($full);
			}
		}

		rmdir($path);
	}
}

