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

		self::deleteDirectory($vendors . 'adodb/adodb/docs');
		self::deleteDirectory($vendors . 'adodb/adodb/tests');
		self::deleteDirectory($vendors . 'aFarkas/html5shiv/build');
		self::deleteDirectory($vendors . 'aFarkas/html5shiv/test');
		self::deleteDirectory($vendors . 'codemirror/codemirror/demo');
		self::deleteDirectory($vendors . 'codemirror/codemirror/doc');
		self::deleteDirectory($vendors . 'codemirror/codemirror/test');
		self::deleteDirectory($vendors . 'jarnokurlin/fullcalendar/demos');
		self::deleteFile(     $vendors . 'jquery/jquery-sheet/jquery-1.5.2.js');
		self::deleteFile(     $vendors . 'jquery/jquery-sheet/jquery-1.5.2.min.js');
		self::deleteDirectory($vendors . 'jquery/jquery-sheet/jquery-ui');
		self::deleteDirectory($vendors . 'jquery/jquery-ui-selectmenu/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-ui-selectmenu/tests');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/tests');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/themes');
		self::deleteDirectory($vendors . 'jquery/plugins/anythingslider/demos');
		self::deleteDirectory($vendors . 'jquery/plugins/brosho/__MACOSX');
		self::deleteDirectory($vendors . 'jquery/plugins/cluetip/demo');
		self::deleteDirectory($vendors . 'jquery/plugins/cluetip/test');
		self::deleteDirectory($vendors . 'jquery/plugins/jquery-validation/demo');
		self::deleteDirectory($vendors . 'jquery/plugins/jquery-validation/lib');
		self::deleteDirectory($vendors . 'jquery/plugins/jquery-validation/test');
		self::deleteDirectory($vendors . 'ezyang/htmlpurifier/docs');
		self::deleteDirectory($vendors . 'ezyang/htmlpurifier/tests');
		self::deleteDirectory($vendors . 'phpcas/phpcas/CAS-1.3.2/docs');
		self::deleteDirectory($vendors . 'phpseclib/phpseclib/tests');
		self::deleteDirectory($vendors . 'smarty/smarty/development');
		self::deleteDirectory($vendors . 'smarty/smarty/documentation');
		self::deleteDirectory($vendors . 'smarty/smarty/distribution/demo');
		self::deleteDirectory($vendors . 'zetacomponents/base/design');
		self::deleteDirectory($vendors . 'zetacomponents/base/docs');
		self::deleteDirectory($vendors . 'zetacomponents/base/tests');
		self::deleteDirectory($vendors . 'zetacomponents/webdav/design');
		self::deleteDirectory($vendors . 'zetacomponents/webdav/docs');
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

	private static function deleteFile($path)
	{
		if (! file_exists($path)) {
			return;
		}

		unlink($path);
	}
}

