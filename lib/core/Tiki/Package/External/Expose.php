<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package\External;

use Tiki\Package\ComposerPackage;

/**
 * Composer Package Information for Expose
 *
 * @url https://packagist.org/packages/enygma/expose
 * @url https://github.com/enygma/expose
 *
 */
class Expose extends ComposerPackage
{
	/**
	 * Set package info
	 */
	public function __construct()
	{
		$this->setPackageInfo(
			'enygma/expose',
			'^3.0',
			'MIT',
			'https://github.com/enygma/expose/blob/master/LICENSE',
			['ids_enabled']
		);
	}
}