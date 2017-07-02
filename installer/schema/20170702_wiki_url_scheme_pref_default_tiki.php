<?php

// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
    header("location: index.php");
    exit;
}

/**
 * Preserve the default url scheme pref as the default changed since 16.x
 *
 * @param Installer $installer
 */
function upgrade_20170702_wiki_url_scheme_pref_default_tiki($installer)
{
	$installer->preservePreferenceDefault('wiki_url_scheme', 'urlencode');
}
