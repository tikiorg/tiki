<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package\External;

use Tiki\Package\ComposerPackage;

/**
 * Composer Package Information for PHPSaml
 *
 * @url https://packagist.org/packages/onelogin/php-saml
 * @url https://github.com/onelogin/php-saml
 *
 */
class PHPSaml extends ComposerPackage
{
	/**
	 * Set package info
	 */
	public function __construct()
	{
		$this->setPackageInfo(
			'onelogin/php-saml',
			'>=2.10.0',
			'MIT',
			'https://github.com/onelogin/php-saml/blob/master/LICENSE',
			['saml_auth_enabled']
		);
	}
}
