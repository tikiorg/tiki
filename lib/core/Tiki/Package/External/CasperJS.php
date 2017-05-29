<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package\External;

use Tiki\Package\ComposerPackage;

/**
 * Composer Package Information for CasperJS
 *
 * @url https://packagist.org/packages/jerome-breton/casperjs-installer
 * @url https://github.com/jerome-breton/casperjs-installer
 *
 */
class CasperJS extends ComposerPackage
{
	/**
	 * Set package info
	 */
	public function __construct()
	{
		$this->setPackageInfo(
			'jerome-breton/casperjs-installer',
			'dev-master',
			'MIT',
			'https://github.com/jerome-breton/casperjs-installer/blob/master/LICENSE',
			['wikiplugin_casperjs'],
			[
				'post-install-cmd' => 'CasperJsInstaller\\Installer::install',
				'post-update-cmd' => 'CasperJsInstaller\\Installer::install',
			]
		);
	}
}