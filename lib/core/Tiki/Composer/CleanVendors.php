<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;
use Composer\Script\Event;
use Composer\Util\FileSystem;

class CleanVendors
{
	public static function clean(Event $event)
	{
		$themes = __DIR__ . '/../../../../themes/';
		$vendors = __DIR__ . '/../../../../vendor/';

		$fs = new FileSystem;
		$fs->ensureDirectoryExists($themes);

		self::addIndexFile($themes);
		self::addIndexFile($vendors);

		$fs->remove($vendors . 'adodb/adodb/docs');
		$fs->remove($vendors . 'adodb/adodb/tests');
		$fs->remove($vendors . 'aFarkas/html5shiv/build');
		$fs->remove($vendors . 'aFarkas/html5shiv/test');
		$fs->remove($vendors . 'alvarotrigo/fullpage.js/examples');
		$fs->remove($vendors . 'bombayworks/zendframework1/library/Zend/Service/WindowsAzure/CommandLine/Scaffolders');
		$fs->remove($vendors . 'codemirror/codemirror/demo');
		$fs->remove($vendors . 'codemirror/codemirror/doc');
		$fs->remove($vendors . 'codemirror/codemirror/test');
		$fs->remove($vendors . 'codemirror/codemirror/mode/tiki');
		$fs->remove($vendors . 'dompdf/dompdf/www');
		$fs->remove($vendors . 'gabordemooij/redbean/testing');
		$fs->remove($vendors . 'jarnokurlin/fullcalendar/demos');
		$fs->remove($vendors . 'jcapture-applet/jcapture-applet/src');
		$fs->remove($vendors . 'jquery/jquery-mobile/demos');
		$fs->remove($vendors . 'jquery/jquery-s5/lib/dompdf/www');
		$fs->remove($vendors . 'jquery/jquery-sheet/jquery-1.5.2.js');
		$fs->remove($vendors . 'jquery/jquery-sheet/jquery-1.5.2.min.js');
		$fs->remove($vendors . 'jquery/jquery-sheet/jquery-ui');
		$fs->remove($vendors . 'jquery/jquery-sheet/parser.php');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/lib');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/src');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/test');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/.gitignore');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/.jshintrc');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/bower.json');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/composer.json');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/Gruntfile.js');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/jquery-ui-timepicker-addon.json');
		$fs->remove($vendors . 'jquery/jquery-timepicker-addon/package.json');
		$fs->remove($vendors . 'jquery/jquery-ui/development-bundle');
		$fs->remove($vendors . 'jquery/jquery-ui/js/jquery-1.10.2.js');
		$fs->remove($vendors . 'jquery/photoswipe/examples');
		$fs->remove($vendors . 'jquery/plugins/anythingslider/demos');
		$fs->remove($vendors . 'jquery/plugins/brosho/__MACOSX');
		$fs->remove($vendors . 'jquery/plugins/colorbox/content');
		$fs->remove($vendors . 'jquery/plugins/superfish/examples');
		$fs->remove($vendors . 'jquery/plugins/superfish/src');
		$fs->remove($vendors . 'jquery/plugins/superfish/test');
		$fs->remove($vendors . 'jquery/plugins/superfish/Gruntfile.coffee');
		$fs->remove($vendors . 'jquery/plugins/superfish/.gitignore');
		$fs->remove($vendors . 'jquery/plugins/superfish/bower.json');
		$fs->remove($vendors . 'jquery/plugins/superfish/package.json');
		$fs->remove($vendors . 'jquery/plugins/superfish/superfish.jquery.json');
		$fs->remove($vendors . 'jquery/plugins/tablesorter/docs');
		$fs->remove($vendors . 'jquery/plugins/tablesorter/testing');
		$fs->remove($vendors . 'jquery/plugins/tablesorter/test.html');
		$fs->remove($vendors . 'jquery/plugins/jquery-validation/demo');
		$fs->remove($vendors . 'jquery/plugins/jquery-validation/lib');
		$fs->remove($vendors . 'jquery/plugins/jquery-validation/test');
		$fs->remove($vendors . 'ezyang/htmlpurifier/docs');
		$fs->remove($vendors . 'ezyang/htmlpurifier/tests');
		$fs->remove($vendors . 'phpcas/phpcas/CAS-1.3.2/docs');
		$fs->remove($vendors . 'phpseclib/phpseclib/tests');
		$fs->remove($vendors . 'player/mp3/template_default/test.mp3');
		$fs->remove($vendors . 'player/mp3/template_mini/test.mp3');
		$fs->remove($vendors . 'player/mp3/template_maxi/test.mp3');
		$fs->remove($vendors . 'player/mp3/template_js/test.mp3');
		$fs->remove($vendors . 'player/mp3/template_multi/test.mp3');
		$fs->remove($vendors . 'smarty/smarty/development');
		$fs->remove($vendors . 'smarty/smarty/documentation');
		$fs->remove($vendors . 'smarty/smarty/distribution/demo');
		$fs->remove($vendors . 'twitter/bootstrap/docs');
		$fs->remove($vendors . 'zetacomponents/base/design');
		$fs->remove($vendors . 'zetacomponents/base/docs');
		$fs->remove($vendors . 'zetacomponents/base/tests');
		$fs->remove($vendors . 'zetacomponents/webdav/design');
		$fs->remove($vendors . 'zetacomponents/webdav/docs');
		$fs->remove($vendors . 'zetacomponents/webdav/tests');
		$fs->remove($vendors . 'player/flv/base');
		$fs->remove($vendors . 'player/flv/classes');
		$fs->remove($vendors . 'player/flv/html5');
		$fs->remove($vendors . 'player/flv/mtasc');
		$fs->remove($vendors . 'player/mp3/classes');
		$fs->remove($vendors . 'player/mp3/mtasc');

		// These are removed to avoid composer warnings caused by classes declared in multiple locations
		$fs->remove($vendors . 'adodb/adodb/datadict/datadict');
		$fs->remove($vendors . 'adodb/adodb/session/session');
		$fs->remove($vendors . 'adodb/adodb/perf/perf');
		$fs->remove($vendors . 'adodb/adodb/drivers/drivers');
		$fs->remove($vendors . 'adodb/adodb/adodb-active-recordx.inc.php');
		$fs->remove($vendors . 'adodb/adodb/drivers/adodb-informix.inc.php');
		$fs->remove($vendors . 'adodb/adodb/perf/perf-informix.inc.php');
		$fs->remove($vendors . 'adodb/adodb/datadict/datadict-informix.inc.php');

		// html5shiv uses a component installer that doesn't seem to be optional, so delete the spare copy we end up with.
		$fs->remove($vendors . '../components');
		// and cwspear/bootstrap-hover-dropdown includes bootstrap and jquery without asking
		$fs->remove($vendors . 'components');
	}

	private static function addIndexFile($path)
	{
		if (file_exists($path) || !is_writable($path)) {
			return;
		}

		file_put_contents($path . 'index.php', '<?php header("location: ../index.php"); die;');
	}
}

