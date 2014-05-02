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

		self::addIndexFile($vendors);

		self::deleteDirectory($vendors . 'adodb/adodb/docs');
		self::deleteDirectory($vendors . 'adodb/adodb/tests');
		self::deleteDirectory($vendors . 'aFarkas/html5shiv/build');
		self::deleteDirectory($vendors . 'aFarkas/html5shiv/test');
		self::deleteDirectory($vendors . 'codemirror/codemirror/demo');
		self::deleteDirectory($vendors . 'codemirror/codemirror/doc');
		self::deleteDirectory($vendors . 'codemirror/codemirror/test');
		self::deleteDirectory($vendors . 'codemirror/codemirror/mode/tiki');
		self::deleteDirectory($vendors . 'jarnokurlin/fullcalendar/demos');
		self::deleteDirectory($vendors . 'jcapture-applet/jcapture-applet/src');
		self::deleteDirectory($vendors . 'jquery/jquery-mobile/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-s5/lib/dompdf/www');
		self::deleteFile(     $vendors . 'jquery/jquery-sheet/jquery-1.5.2.js');
		self::deleteFile(     $vendors . 'jquery/jquery-sheet/jquery-1.5.2.min.js');
		self::deleteDirectory($vendors . 'jquery/jquery-sheet/jquery-ui');
		self::deleteFile(     $vendors . 'jquery/jquery-sheet/parser.php');
		self::deleteDirectory($vendors . 'jquery/jquery-timepicker-addon/lib');
		self::deleteDirectory($vendors . 'jquery/jquery-timepicker-addon/src');
		self::deleteDirectory($vendors . 'jquery/jquery-timepicker-addon/test');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/.gitignore');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/.jshintrc');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/bower.json');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/composer.json');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/Gruntfile.js');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/jquery-ui-timepicker-addon.json');
		self::deleteFile(     $vendors . 'jquery/jquery-timepicker-addon/package.json');
		self::deleteDirectory($vendors . 'jquery/jquery-ui-selectmenu/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-ui-selectmenu/tests');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/demos');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/tests');
		self::deleteDirectory($vendors . 'jquery/jquery-ui/themes');
		self::deleteDirectory($vendors . 'jquery/photoswipe/examples');
		self::deleteDirectory($vendors . 'jquery/plugins/anythingslider/demos');
		self::deleteDirectory($vendors . 'jquery/plugins/brosho/__MACOSX');
		self::deleteDirectory($vendors . 'jquery/plugins/cluetip/demo');
		self::deleteDirectory($vendors . 'jquery/plugins/cluetip/test');
		self::deleteDirectory($vendors . 'jquery/plugins/colorbox/content');
		self::deleteDirectory($vendors . 'jquery/plugins/superfish/examples');
		self::deleteDirectory($vendors . 'jquery/plugins/superfish/src');
		self::deleteDirectory($vendors . 'jquery/plugins/superfish/test');
		self::deleteFile(     $vendors . 'jquery/plugins/superfish/Gruntfile.coffee');
		self::deleteFile(     $vendors . 'jquery/plugins/superfish/.gitignore');
		self::deleteFile(     $vendors . 'jquery/plugins/superfish/bower.json');
		self::deleteFile(     $vendors . 'jquery/plugins/superfish/package.json');
		self::deleteFile(     $vendors . 'jquery/plugins/superfish/superfish.jquery.json');
		self::deleteDirectory($vendors . 'jquery/plugins/tablesorter/docs');
		self::deleteDirectory($vendors . 'jquery/plugins/tablesorter/testing');
		self::deleteFile(     $vendors . 'jquery/plugins/tablesorter/test.html');
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
		self::deleteDirectory($vendors . 'player/flv/base');
		self::deleteDirectory($vendors . 'player/flv/classes');
		self::deleteDirectory($vendors . 'player/flv/html5');
		self::deleteDirectory($vendors . 'player/flv/mtasc');
		self::deleteDirectory($vendors . 'player/mp3/classes');
		self::deleteDirectory($vendors . 'player/mp3/mtasc');
	}

	private static function deleteDirectory($path)
	{
		if (! file_exists($path)) {
			return;
		}

		if (! is_readable($path)) {
			return;
		}

		foreach (scandir($path) as $file) {
			if ($file === '.' || $file === '..') {
				continue;
			}

			$full = "$path/$file";

			if (is_link($full)) {
				self::deleteFile($full);
			} elseif (is_dir($full)) {
				self::deleteDirectory($full);
			} else {
				self::deleteFile($full);
			}
		}

		$remaining = scandir($path);
		$remaining = array_filter($remaining, function ($file) {
			return $file !== '.' && $file !== '..';
		});

		if (is_writable($path) && ! count($remaining)) {
			rmdir($path);
		}
	}

	private static function deleteFile($path)
	{
		if (! file_exists($path) || ! is_writable($path)) {
			return;
		}

		unlink($path);
	}

	private static function addIndexFile($path)
	{
		if (! file_exists($path) || ! is_writable($path)) {
			return;
		}

		file_put_contents($path . 'index.php', '<?php header("location: ../index.php"); die;');
	}
}

