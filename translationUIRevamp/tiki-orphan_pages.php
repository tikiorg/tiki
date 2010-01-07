<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-orphan_pages.php,v 1.14 2007-03-06 19:29:50 sylvieg Exp $
require_once ('tiki-setup.php');
$access->check_feature(array('feature_wiki', 'feature_listorphanPages'));
$listpages_orphans = true;
include ('tiki-listpages.php');
