<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Package\External;

use Tiki\Package\ComposerPackage;

/**
 * Composer Package Information for Mpdf
 *
 * @url https://packagist.org/packages/mpdf/mpdf
 * @url https://github.com/mpdf/mpdf
 *
 */
class Mpdf extends ComposerPackage
{
	/**
	 * Set package info
	 */
	public function __construct()
	{
		$this->setPackageInfo(
			'mpdf/mpdf',
			'^6.1',
			'GPL',
			'https://github.com/mpdf/mpdf/blob/master/LICENSE.txt',
			['Tiki Print']
		);
	}
}