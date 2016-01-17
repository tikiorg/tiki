<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Composer;
use Composer\Script\Event;
use Composer\Util\FileSystem;

class BootstrapCompiler
{
	public static function build(Event $event)
	{
		if (class_exists('lessc')) {	// this can get called before composer has installed all the packages

			$base = __DIR__ . '/../../../../vendor/twitter/bootstrap';
			if (! file_exists($base . '/css')) {
				mkdir($base . '/css');

				$compiler = new \lessc;
				$compiler->compileFile("$base/less/bootstrap.less", "$base/css/bootstrap.css");
			}
		}
	}
}

