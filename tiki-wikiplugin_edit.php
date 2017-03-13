<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require 'tiki-setup.php';

trigger_error(tr('Note, deprecated file tiki-wikiplugin_edit.php, code moved to service plugin->replace'));

TikiLib::lib('service')->render('plugin', 'replace', $jitPost);

header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
