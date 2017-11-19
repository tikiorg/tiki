<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// initially from https://github.com/Studio-42/elFinder/wiki/Adding-file-description-to-Properties-dialog

class tikiElFinder extends elFinder
{
	function __construct($opts)
	{
		parent::__construct($opts);
		/* Adding new command */
		$this->commands['info'] = ['target' => true, 'content' => false];
	}

	protected function info($args)
	{
		$target = $args['target'];
		$newDesc = $args['content'];
		$error = [self::ERROR_UNKNOWN, '#' . $target];

		if (($volume = $this->volume($target)) == false
			|| ($file = $volume->file($target)) == false) {
			return ['error' => $this->error($error, self::ERROR_FILE_NOT_FOUND)];
		}

		$error[1] = $file['name'];

		if ($volume->commandDisabled('info')) {
			return ['error' => $this->error($error, self::ERROR_ACCESS_DENIED)];
		}

		if (($info = $volume->info($target, $newDesc)) == -1) {
			return ['error' => $this->error($error, $volume->error())];
		}

		return ['info' => $info];
	}
}
